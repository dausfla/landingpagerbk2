<?php

namespace App\Controllers\Public;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Validator;
use App\Models\LeadModel;
use App\Models\WaClickModel;
use App\Models\SettingModel;
use App\Services\NotificationService;

class LeadController
{
    /**
     * AJAX Step 1 Submission
     */
    public function step1(Request $request): void
    {
        // Check honeypot
        if (!empty($request->post('website_url_check'))) {
            Response::json(['success' => true, 'lead_id' => 0, 'code' => 'RBK-HONEYPOT'], 200);
            return;
        }

        $validator = new Validator();
        $data = [
            'name'  => trim($request->post('name', '')),
            'phone' => trim($request->post('phone', '')),
            'need'  => trim($request->post('need', '')),
        ];

        if (!$validator->validate($data, [
            'name'  => 'required|min:2',
            'phone' => 'required|phone',
            'need'  => 'required',
        ])) {
            Response::json([
                'success' => false,
                'errors'  => $validator->getFirstErrors()
            ], 422);
            return;
        }

        $data['ip_hash'] = $request->getIpHash();
        $data['user_agent'] = $request->getUserAgent();

        $result = LeadModel::createStep1($data);

        Response::json([
            'success' => true,
            'lead_id' => $result['id'],
            'code'    => $result['code']
        ]);
    }

    /**
     * AJAX Step 2 Submission
     */
    public function step2(Request $request): void
    {
        $leadId = (int)$request->post('lead_id');
        $lead = LeadModel::findById($leadId);

        if (!$lead) {
            Response::json(['success' => false, 'message' => 'Lead tidak ditemukan.'], 404);
            return;
        }

        $calcSnapshot = null;
        if (!empty($request->post('calc_snapshot'))) {
            $calcSnapshot = json_decode($request->post('calc_snapshot'), true);
        }

        $attribution = $request->getAttributionData();

        $updateData = array_merge($attribution, [
            'location'         => trim($request->post('location', '')),
            'land_size'        => trim($request->post('land_size', '')),
            'building_size_m2' => trim($request->post('building_size_m2', '')),
            'budget_range'     => trim($request->post('budget_range', '')),
            'package_choice'   => trim($request->post('package_choice', '')),
            'notes'            => trim($request->post('notes', '')),
            'calc_snapshot'    => $calcSnapshot,
            'first_touch'      => [
                'utm_source'   => $attribution['utm_source'],
                'utm_medium'   => $attribution['utm_medium'],
                'utm_campaign' => $attribution['utm_campaign'],
            ]
        ]);

        LeadModel::updateStep2($leadId, $updateData);

        // Send notifications
        $fullLead = LeadModel::findById($leadId);
        NotificationService::notifyNewLead($fullLead);

        Response::json([
            'success' => true,
            'code'    => $lead['code'],
            'redirect'=> "/terima-kasih?kode={$lead['code']}"
        ]);
    }

    /**
     * AJAX Record Direct WA Click
     */
    public function waClick(Request $request): void
    {
        $data = [
            'cta_location' => $request->input('cta_location', 'floating_wa'),
            'utm_source'   => $request->input('utm_source'),
            'utm_medium'   => $request->input('utm_medium'),
            'utm_campaign' => $request->input('utm_campaign'),
            'page_url'     => $request->getReferrer(),
            'device'       => $request->getDevice()
        ];

        WaClickModel::record($data);
        Response::json(['success' => true]);
    }

    /**
     * Thank You Page View
     */
    public function terimaKasih(Request $request): void
    {
        $code = trim($request->get('kode', ''));
        $lead = !empty($code) ? LeadModel::findByCode($code) : null;
        $settings = SettingModel::getAll();

        // Construct WhatsApp Direct Message
        $template = $settings['wa_message_template'] ?? 'Halo RBK, saya {nama}. Kode: {kode}. Kebutuhan: {kebutuhan}.';
        $waNum = $settings['whatsapp_number'] ?? '6281234593742';

        if ($lead) {
            $msg = strtr($template, [
                '{nama}'       => $lead['name'],
                '{kode}'       => $lead['code'],
                '{kebutuhan}'  => $lead['need'],
                '{lokasi}'     => $lead['location'] ?? '-',
                '{luas_tanah}' => $lead['land_size'] ?? '-',
                '{lantai}'     => $lead['floors'] ?? '-',
                '{budget}'     => $lead['budget_range'] ?? '-',
                '{paket}'      => $lead['package_choice'] ?? '-'
            ]);
        } else {
            $msg = "Halo RBK, saya ingin berkonsultasi mengenai jasa arsitek dan bangun rumah.";
        }

        $waUrl = "https://wa.me/{$waNum}?text=" . urlencode($msg);

        $html = View::render('public/terima-kasih', [
            'lead'     => $lead,
            'code'     => $code,
            'waUrl'    => $waUrl,
            'settings' => $settings
        ]);

        Response::html($html);
    }
}

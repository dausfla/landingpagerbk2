<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\SettingModel;
use App\Models\ActivityLogModel;

class SettingsController
{
    public function index(Request $request): void
    {
        $settings = SettingModel::getAll();
        $tab = $request->get('tab', 'general');

        $html = View::renderWithLayout('admin/settings/index', 'admin/layout', [
            'title'    => 'Pengaturan Sistem',
            'settings' => $settings,
            'activeTab'=> $tab,
            'success'  => $request->get('success')
        ]);

        Response::html($html);
    }

    public function update(Request $request): void
    {
        $group = $request->post('group', 'general');
        $inputs = $request->all();

        // Remove csrf_token and group from inputs before saving
        unset($inputs['csrf_token'], $inputs['group']);

        SettingModel::setMany($inputs, $group, Auth::id());

        ActivityLogModel::log('update_settings', 'settings', null, null, $inputs, Auth::id(), $request->getIpHash());

        Response::redirect("/admin/settings?tab={$group}&success=1");
    }
}

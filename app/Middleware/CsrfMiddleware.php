<?php

namespace App\Middleware;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;

class CsrfMiddleware
{
    public function handle(Request $request): bool
    {
        if ($request->isPost()) {
            $token = $request->post('csrf_token') ?? $request->get('csrf_token') ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

            if (!Csrf::validate($token)) {
                if ($request->isAjax()) {
                    Response::json([
                        'success' => false,
                        'message' => 'Sesi CSRF tidak valid atau telah kedaluwarsa. Silakan muat ulang halaman.'
                    ], 403);
                    return false;
                }

                Response::html('Sesi CSRF tidak valid atau telah kedaluwarsa.', 403);
                return false;
            }
        }

        return true;
    }
}

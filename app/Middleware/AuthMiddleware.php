<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

class AuthMiddleware
{
    public function handle(Request $request): bool
    {
        if (!Auth::check()) {
            if ($request->isAjax()) {
                Response::json([
                    'success' => false,
                    'message' => 'Sesi Anda telah berakhir. Silakan login kembali.'
                ], 401);
                return false;
            }

            Response::redirect('/admin/login');
            return false;
        }

        return true;
    }
}

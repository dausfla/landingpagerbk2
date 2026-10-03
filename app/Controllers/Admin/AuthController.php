<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

class AuthController
{
    public function showLogin(Request $request): void
    {
        if (Auth::check()) {
            Response::redirect('/admin');
            return;
        }

        Response::html(\App\Core\View::render('admin/login', [
            'error' => $request->get('error')
        ]));
    }

    public function login(Request $request): void
    {
        $email = trim($request->post('email', ''));
        $password = trim($request->post('password', ''));

        if (empty($email) || empty($password)) {
            Response::html(\App\Core\View::render('admin/login', [
                'error' => 'Email dan password wajib diisi.',
                'email' => $email
            ]));
            return;
        }

        if (Auth::attempt($email, $password, $request)) {
            \App\Models\ActivityLogModel::log('login', 'users', Auth::id(), null, null, Auth::id(), $request->getIpHash());
            Response::redirect('/admin');
            return;
        }

        Response::html(\App\Core\View::render('admin/login', [
            'error' => 'Email atau password salah, atau akun telah ditangguhkan.',
            'email' => $email
        ]));
    }

    public function logout(Request $request): void
    {
        if (Auth::check()) {
            \App\Models\ActivityLogModel::log('logout', 'users', Auth::id(), null, null, Auth::id(), $request->getIpHash());
            Auth::logout();
        }
        Response::redirect('/admin/login');
    }
}

<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Validator;
use App\Models\UserModel;
use App\Models\ActivityLogModel;

class UserController
{
    public function index(Request $request): void
    {
        $users = UserModel::all();
        $html = View::renderWithLayout('admin/users/index', 'admin/layout', [
            'title' => 'Pengolahan Pengguna (Super Admin)',
            'users' => $users
        ]);
        Response::html($html);
    }

    public function create(Request $request): void
    {
        $html = View::renderWithLayout('admin/users/form', 'admin/layout', [
            'title' => 'Tambah Super Admin Baru',
            'user'  => null,
            'error' => null
        ]);
        Response::html($html);
    }

    public function store(Request $request): void
    {
        $validator = new Validator();
        $data = [
            'name'     => trim($request->post('name', '')),
            'email'    => trim($request->post('email', '')),
            'password' => trim($request->post('password', '')),
        ];

        if (!$validator->validate($data, [
            'name'     => 'required|min:3',
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ])) {
            Response::html(View::renderWithLayout('admin/users/form', 'admin/layout', [
                'title'  => 'Tambah Super Admin Baru',
                'user'   => $data,
                'errors' => $validator->getErrors()
            ]));
            return;
        }

        // Check email uniqueness
        if (UserModel::findByEmail($data['email'])) {
            Response::html(View::renderWithLayout('admin/users/form', 'admin/layout', [
                'title' => 'Tambah Super Admin Baru',
                'user'  => $data,
                'error' => 'Email tersebut sudah terdaftar.'
            ]));
            return;
        }

        $userId = UserModel::create([
            'name'                 => $data['name'],
            'email'                => $data['email'],
            'password_hash'        => Auth::hashPassword($data['password']),
            'is_active'            => 1,
            'must_change_password' => 1
        ]);

        ActivityLogModel::log('create_user', 'users', (int)$userId, null, ['name' => $data['name'], 'email' => $data['email']], Auth::id(), $request->getIpHash());

        Response::redirect('/admin/users');
    }

    public function edit(Request $request, string $id): void
    {
        $user = UserModel::findById((int)$id);
        if (!$user) {
            Response::redirect('/admin/users');
            return;
        }

        $html = View::renderWithLayout('admin/users/form', 'admin/layout', [
            'title' => 'Edit Super Admin',
            'user'  => $user,
            'error' => null
        ]);
        Response::html($html);
    }

    public function update(Request $request, string $id): void
    {
        $userId = (int)$id;
        $user = UserModel::findById($userId);
        if (!$user) {
            Response::redirect('/admin/users');
            return;
        }

        $name = trim($request->post('name', ''));
        $email = trim($request->post('email', ''));
        $password = trim($request->post('password', ''));
        $isActive = (int)$request->post('is_active', 1);

        // Safety Rule: Admin cannot deactivate their own account
        if ($userId === Auth::id() && $isActive === 0) {
            Response::html(View::renderWithLayout('admin/users/form', 'admin/layout', [
                'title' => 'Edit Super Admin',
                'user'  => array_merge($user, ['name' => $name, 'email' => $email]),
                'error' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.'
            ]));
            return;
        }

        // Safety Rule: Minimal 1 active super admin must remain
        if ($isActive === 0 && UserModel::countActiveSuperAdmins() <= 1) {
            Response::html(View::renderWithLayout('admin/users/form', 'admin/layout', [
                'title' => 'Edit Super Admin',
                'user'  => array_merge($user, ['name' => $name, 'email' => $email]),
                'error' => 'Harus selalu ada minimal 1 akun Super Admin yang aktif.'
            ]));
            return;
        }

        $updateData = [
            'name'      => $name,
            'email'     => $email,
            'is_active' => $isActive,
        ];

        if (!empty($password)) {
            $updateData['password_hash'] = Auth::hashPassword($password);
            $updateData['must_change_password'] = 0;
        }

        UserModel::update($userId, $updateData);

        ActivityLogModel::log('update_user', 'users', $userId, $user, $updateData, Auth::id(), $request->getIpHash());

        Response::redirect('/admin/users');
    }

    public function delete(Request $request, string $id): void
    {
        $userId = (int)$id;

        // Safety Rule: Admin cannot delete their own account
        if ($userId === Auth::id()) {
            Response::json(['success' => false, 'message' => 'Anda tidak dapat menghapus akun Anda sendiri.'], 400);
            return;
        }

        // Safety Rule: Minimal 1 active super admin must remain
        if (UserModel::countActiveSuperAdmins() <= 1) {
            Response::json(['success' => false, 'message' => 'Harus selalu ada minimal 1 akun Super Admin yang aktif.'], 400);
            return;
        }

        $user = UserModel::findById($userId);
        if ($user) {
            UserModel::softDelete($userId);
            ActivityLogModel::log('delete_user', 'users', $userId, $user, null, Auth::id(), $request->getIpHash());
        }

        Response::json(['success' => true, 'message' => 'Pengguna berhasil dihapus.']);
    }
}

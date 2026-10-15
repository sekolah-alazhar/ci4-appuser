<?php

namespace App\Controllers;

use App\Services\UserService;
use Config\Services;

class Users extends BaseController
{
    protected UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function initController($request, $response, $logger)
    {
        parent::initController($request, $response, $logger);
    }

    // ==================== Auth ====================

    public function login()
    {
        $session = Services::session();
        if ($session->get('logged_in')) {
            $redirect = $session->get('role') === 'admin' ? '/admin/users' : '/guest/users';
            return redirect()->to($redirect);
        }

        $data = [
            'title' => 'Login - Manajemen User',
            'error' => $session->get('error') ?? null
        ];

        if ($this->request->is('post')) {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            $user = $this->userService->verifyPassword($username, $password);
            
            if ($user) {
                $session->set([
                    'logged_in' => true,
                    'user_id'   => $user['id'],
                    'username'  => $user['username'],
                    'role'      => $user['role'],
                    'name'      => $user['name']
                ]);
                
                $redirect = $user['role'] === 'admin' ? '/admin/users' : '/guest/users';
                return redirect()->to($redirect)->with('success', 'Login berhasil!');
            }
            
            $data['error'] = 'Username atau password salah.';
        }

        return view('auth/login', $data);
    }

    public function logout()
    {
        Services::session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah logout.');
    }

    // ==================== Admin Routes ====================

    public function adminIndex()
    {
        $session = Services::session();
        if (!$session->get('logged_in') || $session->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $page = max(1, (int)($this->request->getGet('page') ?? 1));
        $limit = 20;
        $filters = [
            'search' => $this->request->getGet('search') ?? '',
            'role'   => $this->request->getGet('role') ?? ''
        ];

        $result = $this->userService->getPaginatedUsers($page, $limit, $filters);

        $data = [
            'title'      => 'Manajemen User - Admin',
            'users'      => $result['users'],
            'pagination' => [
                'current_page' => $result['page'],
                'total_pages'  => $result['pages'],
                'total'        => $result['total'],
                'base_url'     => '/admin/users',
                'search'       => $filters['search'],
                'role'         => $filters['role']
            ],
            'stats' => [
                'total' => $result['total']
            ]
        ];

        return view('admin/users/index', $data);
    }

    public function adminDashboard()
    {
        $session = Services::session();
        if (!$session->get('logged_in') || $session->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $stats = $this->userService->getDashboardStats();

        return view('dashboard/index', [
            'title' => 'Dashboard - Admin',
            'stats' => $stats,
            'role'  => 'admin',
        ]);
    }

    public function adminCreate()
    {
        $session = Services::session();
        if (!$session->get('logged_in') || $session->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        if ($this->request->is('post')) {
            $rules = [
                'name'     => 'required|max_length[100]',
                'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
                'email'    => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'role'     => 'required|in_list[admin,guest]'
            ];

            if (!$this->validate($rules)) {
                $data = [
                    'title'      => 'Tambah User - Admin',
                    'old_input'  => $this->request->getPost(),
                    'errors'     => $this->validator->getErrors()
                ];
                return view('admin/users/form', $data);
            }

            $this->userService->createUser($this->request->getPost());
            return redirect()->to('/admin/users')->with('success', 'User berhasil ditambahkan.');
        }

        return view('admin/users/form', ['title' => 'Tambah User - Admin']);
    }

    public function adminEdit(string $id)
    {
        $session = Services::session();
        if (!$session->get('logged_in') || $session->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $user = $this->userService->getUserById($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan.');
        }

        if ($this->request->is('post')) {
            $rules = [
                'name'     => 'required|max_length[100]',
                'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]",
                'email'    => "required|valid_email|is_unique[users.email,id,{$id}]",
                'role'     => 'required|in_list[admin,guest]'
            ];

            $postData = $this->request->getPost();
            if (!empty($postData['password'])) {
                $rules['password'] = 'min_length[6]';
            } else {
                unset($rules['password']);
            }

            if (!$this->validate($rules)) {
                $data = [
                    'title'     => 'Edit User - Admin',
                    'user'      => $user,
                    'old_input' => $postData,
                    'errors'    => $this->validator->getErrors()
                ];
                return view('admin/users/form', $data);
            }

            $updateData = $postData;
            if (empty($updateData['password'])) {
                unset($updateData['password']);
            }
            
            $this->userService->updateUser($id, $updateData);
            return redirect()->to('/admin/users')->with('success', 'User berhasil diperbarui.');
        }

        return view('admin/users/form', ['title' => 'Edit User - Admin', 'user' => $user]);
    }

    public function adminDelete(string $id)
    {
        $session = Services::session();
        if (!$session->get('logged_in') || $session->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        if (!$this->request->is('post')) {
            return redirect()->to('/admin/users')->with('error', 'Metode tidak diizinkan.');
        }

        if ($id === $session->get('user_id')) {
            return redirect()->to('/admin/users')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $this->userService->deleteUser($id);
        return redirect()->to('/admin/users')->with('success', 'User berhasil dihapus.');
    }

    // ==================== Guest Routes ====================

    public function guestIndex()
    {
        $session = Services::session();
        if (!$session->get('logged_in') || $session->get('role') !== 'guest') {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $users = $this->userService->getAllUsers();

        $data = [
            'title' => 'Daftar User - Guest',
            'users' => $users
        ];

        return view('guest/users/index', $data);
    }

    public function guestDashboard()
    {
        $session = Services::session();
        if (!$session->get('logged_in') || $session->get('role') !== 'guest') {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $stats = $this->userService->getDashboardStats();

        return view('dashboard/index', [
            'title' => 'Dashboard - Guest',
            'stats' => $stats,
            'role'  => 'guest',
        ]);
    }
}

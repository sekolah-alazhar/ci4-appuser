<?php

namespace App\Services;

use App\Models\UserModel;
use CodeIgniter\I18n\Time;

class UserService
{
    protected UserModel $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    /**
     * Get paginated users with search/filter
     */
    public function getPaginatedUsers(int $page = 1, int $limit = 20, array $filters = []): array
    {
        $builder = $this->model->select('id, name, username, email, role, created_at');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('username', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['role'])) {
            $builder->where('role', $filters['role']);
        }
        
        $total = $builder->countAllResults(false);
        $builder->orderBy('created_at', 'DESC');
        $users = $builder->paginate($limit, 'default', $page);
        
        return [
            'users' => $users,
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ];
    }

    /**
     * Verify password against username
     */
    public function verifyPassword(string $username, string $password): ?array
    {
        $user = $this->model->where('username', $username)->first();
        if (!$user || !password_verify($password, $user['password'])) {
            return null;
        }
        return $user;
    }

    /**
     * Get all users (read-only, for guest role)
     */
    public function getAllUsers(): array
    {
        return $this->model
            ->select('id, name, username, email, role, created_at, updated_at')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get single user by ID
     */
    public function getUserById(string $id): ?array
    {
        return $this->model->find($id);
    }

    /**
     * Create new user
     */
    public function createUser(array $data): array
    {
        $data['id'] = bin2hex(random_bytes(16));
        $data['created_at'] = Time::now()->format('Y-m-d H:i:s');
        $data['updated_at'] = Time::now()->format('Y-m-d H:i:s');
        
        $this->model->insert($data);
        return $this->model->find($data['id']);
    }

    /**
     * Update user
     */
    public function updateUser(string $id, array $data): bool
    {
        $data['updated_at'] = Time::now()->format('Y-m-d H:i:s');
        return $this->model->update($id, $data);
    }

    /**
     * Delete user
     */
    public function deleteUser(string $id): bool
    {
        return $this->model->delete($id);
    }

    /**
     * Check if username exists
     */
    public function usernameExists(string $username, string $excludeId = ''): bool
    {
        $builder = $this->model->where('username', $username);
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }

    /**
     * Check if email exists
     */
    public function emailExists(string $email, string $excludeId = ''): bool
    {
        $builder = $this->model->where('email', $email);
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }

    /**
     * Statistik untuk halaman Dashboard:
     * total user, distribusi role, grafik pendaftaran 6 bulan terakhir,
     * dan daftar user terbaru.
     */
    public function getDashboardStats(): array
    {
        $total  = $this->model->builder()->countAllResults();
        $admins = $this->model->builder()->where('role', 'admin')->countAllResults();
        $guests = $this->model->builder()->where('role', 'guest')->countAllResults();

        // Pendaftaran per bulan (6 bulan terakhir, termasuk bulan berjalan)
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $t     = Time::now()->subMonths($i);
            $key   = $t->format('Y-m');
            $months[$key] = ['label' => $t->format('M Y'), 'count' => 0];
        }

        $since = Time::now()->subMonths(5)->format('Y-m-01 00:00:00');
        $rows = $this->model->builder()
            ->select("DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS c")
            ->where('created_at >=', $since)
            ->where('created_at IS NOT NULL', null, false)
            ->groupBy('ym')
            ->get()->getResultArray();

        foreach ($rows as $r) {
            if (isset($months[$r['ym']])) {
                $months[$r['ym']]['count'] = (int) $r['c'];
            }
        }

        // User terbaru (5)
        $recent = $this->model
            ->select('id, name, username, email, role, created_at')
            ->orderBy('created_at', 'DESC')
            ->findAll(5);

        // Pendaftaran bulan ini
        $monthStart = Time::now()->format('Y-m-01 00:00:00');
        $thisMonth = $this->model->builder()
            ->where('created_at >=', $monthStart)
            ->where('created_at IS NOT NULL', null, false)
            ->countAllResults();

        return [
            'total'      => $total,
            'admin'      => $admins,
            'guest'      => $guests,
            'thisMonth'  => $thisMonth,
            'labels'     => array_column($months, 'label'),
            'counts'     => array_column($months, 'count'),
            'recent'     => $recent,
        ];
    }
}

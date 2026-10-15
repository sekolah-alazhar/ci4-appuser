<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = ['id', 'name', 'username', 'email', 'password', 'role'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validasi dilakukan di Controller (Users::adminCreate/adminEdit),
    // jadi validasi model dimatikan agar placeholder {id} tidak error saat insert.
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = true;

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Hash password sebelum insert/update.
     * Event data CI4: $data['data'] berisi baris yang akan disimpan.
     */
    protected function hashPassword(array $data)
    {
        if (empty($data['data']['password'])) {
            return $data;
        }

        // Jangan hash ulang jika nilainya sudah berupa hash bcrypt/argon
        $info = password_get_info($data['data']['password']);
        if (!empty($info['algo'])) {
            return $data;
        }

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }

    /**
     * Find user by username
     */
    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Verify password
     */
    public function verifyPassword(string $username, string $password): ?array
    {
        $user = $this->findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }
}

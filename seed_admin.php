#!/usr/bin/env php
<?php
/**
 * Seed admin user
 */

// Load CI4
chdir('/home/upudin/ci4-appuser/ci4-appuser');
require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$config = require APPPATH . 'Config/Database.php';
$db = \Config\Database::connect([
    'DSN'      => $config->default['DSN'],
    'hostname' => $config->default['hostname'],
    'username' => $config->default['username'],
    'password' => $config->default['password'],
    'database' => $config->default['database'],
    'DBDriver' => $config->default['DBDriver'],
    'DBPrefix' => $config->default['DBPrefix'],
    'port'     => $config->default['port'],
]);

// Check if admin exists
$existing = $db->table('users')->where('username', 'admin')->get()->getRowArray();
if ($existing) {
    echo "Admin user already exists. Skipping.\n";
    exit(0);
}

// Create admin
$db->table('users')->insert([
    'id'         => 'admin-001',
    'name'       => 'Administrator',
    'username'   => 'admin',
    'email'      => 'admin@pudin.eu.org',
    'password'   => password_hash('admin123', PASSWORD_DEFAULT),
    'role'       => 'admin',
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
]);

echo "Admin user created: admin / admin123\n";

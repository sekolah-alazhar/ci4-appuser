#!/bin/bash
# Seed admin user if not exists
cd /home/upudin/ci4-appuser/ci4-appuser

# Simple check - create admin if not exists
mysql -u appuser -p@C0b4d1b4c4 -h localhost db_appuser -e "
SELECT COUNT(*) as count FROM users WHERE username = 'admin';
" 2>/dev/null | tail -1 | grep -q "^[0-9]*$" && {
    ADMIN_COUNT=$(mysql -u appuser -p@C0b4d1b4c4 -h localhost db_appuser -e "SELECT COUNT(*) FROM users WHERE username = 'admin';" 2>/dev/null | tail -1)
    if [ "$ADMIN_COUNT" = "0" ] || [ -z "$ADMIN_COUNT" ]; then
        php -r "
require 'vendor/autoload.php';
\$db = \Config\Database::connect();
\$db->table('users')->insert([
    'id' => 'admin-001',
    'name' => 'Administrator',
    'username' => 'admin',
    'email' => 'admin@pudin.eu.org',
    'password' => password_hash('admin123', PASSWORD_DEFAULT),
    'role' => 'admin',
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
]);
echo 'Admin user created: admin / admin123\n';
"
    else
        echo "Admin user already exists (count: $ADMIN_COUNT). Skipping."
    fi
}

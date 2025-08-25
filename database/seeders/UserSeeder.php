<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $domain = env('USER_EMAIL_DOMAIN', 'sahabatagro.co.id');

        $users = [
            [
                'name' => 'Administrator',
                'local' => 'admin',
                'password_env' => 'ADMIN_PASSWORD',
                'default_password' => 'Admin@123',
            ],
            [
                'name' => 'Manager Kebun',
                'local' => 'manager',
                'password_env' => 'MANAGER_PASSWORD',
                'default_password' => 'Manager@123',
            ],
            [
                'name' => 'Operator',
                'local' => 'operator',
                'password_env' => 'OPERATOR_PASSWORD',
                'default_password' => 'Operator@123',
            ],
        ];

        foreach ($users as $u) {
            $email = $u['local'].'@'.$domain;
            $passwordPlain = env($u['password_env'], $u['default_password']);
            User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $u['name'],
                    'email' => $email,
                    'password' => Hash::make($passwordPlain),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}

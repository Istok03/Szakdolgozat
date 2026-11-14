<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Bemutató Admin', 'email' => 'admin@example.com', 'password' => 'admin123', 'admin' => true],
            ['name' => 'Bemutató Vásárló', 'email' => 'vasarlo@example.com', 'password' => 'vasarlo123', 'admin' => false],
        ] as $account) {
            if (User::where('email', $account['email'])->exists()) {
                continue;
            }
            $user = new User();
            $user->name = $account['name'];
            $user->email = $account['email'];
            $user->password = Hash::make($account['password']);
            $user->is_admin = $account['admin'];
            $user->email_verified_at = now();
            $user->save();
        }
    }
}

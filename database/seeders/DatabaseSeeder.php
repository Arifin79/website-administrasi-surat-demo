<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Administrator DRB',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Staf Administrasi',
            'username' => 'staf',
            'password' => Hash::make('staf123'),
            'role'     => 'staf',
        ]);

        SystemSetting::set('letter_number_prefix', 'DRB');
        SystemSetting::set('company_name', 'PT. DURI REJANG BERSERI');
    }
}

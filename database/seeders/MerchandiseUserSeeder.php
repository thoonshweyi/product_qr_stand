<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MerchandiseUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branch = Branch::where('branch_name', 'Head Office')->first();
        $status = Status::where('name', 'Active')->first();
        $role   = Role::where('name', 'Editor')->first();
        $department = Department::where('name', 'Merchandise')->first();

        $users = [
            ['name' => 'Zin Nyi Nyi Naing', 'employee_id' => '113-000050', 'department_id' => 6],
            ['name' => 'Myat Soe Htut', 'employee_id' => '005-000243', 'department_id' => 6],
            ['name' => 'Waddy Pwint Oo', 'employee_id' => '114-000045', 'department_id' => 6],
            ['name' => 'Soe Pa Pa', 'employee_id' => '007-000136', 'department_id' => 6],
            ['name' => 'Thae Mg Mg', 'employee_id' => '001-000670', 'department_id' => 6],
            ['name' => 'Chan Thar', 'employee_id' => '114-000041', 'department_id' => 6],
            ['name' => 'Yair Lwin Oo', 'employee_id' => '001-000933', 'department_id' => 6],
            ['name' => 'Hnin Wai Phyo Hlaing', 'employee_id' => '003-000838', 'department_id' => 6],
            ['name' => 'Cho Cho Win', 'employee_id' => '000-000527', 'department_id' => 6],
            ['name' => 'Khaing Mar', 'employee_id' => '000-000305', 'department_id' => 6],
            ['name' => 'Aye Chan Pyae', 'employee_id' => '000-000309', 'department_id' => 6],
            ['name' => 'Htet Lin Swe', 'employee_id' => '001-000745', 'department_id' => 6],
            ['name' => 'Wai Mar Lwin', 'employee_id' => '008-000009', 'department_id' => 6],
            ['name' => 'Aung Naing Phyo', 'employee_id' => '003-000834', 'department_id' => 6],
            ['name' => 'Hnin Ei Hlaing', 'employee_id' => '002-000456', 'department_id' => 6],
            ['name' => 'Su Su Hlaing', 'employee_id' => '006-000032', 'department_id' => 6],
            ['name' => 'Lae Thinzar Paing', 'employee_id' => '001-000878', 'department_id' => 6],
            ['name' => 'Ei Thandar Win', 'employee_id' => '000-000391', 'department_id' => 6],
            ['name' => 'Eaint Hmu Nwe', 'employee_id' => '002-000149', 'department_id' => 6],
            ['name' => 'Phyo Mg Mg', 'employee_id' => '007-000212', 'department_id' => 6],
            ['name' => 'Ye Min Aung', 'employee_id' => '004-000242', 'department_id' => 6],
            ['name' => 'May Thandar Tun', 'employee_id' => '008-000017', 'department_id' => 6],
            ['name' => 'Thet Myat Noe', 'employee_id' => '000-000402', 'department_id' => 6],
            ['name' => 'Thuzar Lwin', 'employee_id' => '001-000449', 'department_id' => 6],
            ['name' => 'Zaw Htet Aung', 'employee_id' => '007-000129', 'department_id' => 6],
            ['name' => 'Yin Mar Htwe', 'employee_id' => '008-000031', 'department_id' => 6],
            ['name' => 'Su Mi Mi Zaw', 'employee_id' => '008-000046', 'department_id' => 6],
            ['name' => 'Aye Mar Lwin', 'employee_id' => '000-000304', 'department_id' => 6],
            ['name' => 'Theint Ei Ei Lwin', 'employee_id' => '001-000561', 'department_id' => 6],
            ['name' => 'Ei Ei Phyo', 'employee_id' => '000-000302', 'department_id' => 6],
            ['name' => 'Poe Ei Phyu', 'employee_id' => '008-000043', 'department_id' => 6],
            ['name' => 'Hnin Pwint Wai', 'employee_id' => '002-000245', 'department_id' => 6],
            ['name' => 'Htwe Htwe', 'employee_id' => '001-000815', 'department_id' => 6],
            ['name' => 'Khaing Mar', 'employee_id' => '000-0000305', 'department_id' => 6],
            ['name' => 'ZarChi Thein', 'employee_id' => '006-000053', 'department_id' => 6],
            ['name' => 'Wutt Yee Thant', 'employee_id' => '001-000384', 'department_id' => 6],
            ['name' => 'May Phue Aung', 'employee_id' => '000-000259', 'department_id' => 6],
            ['name' => 'Myo Thant', 'employee_id' => '003-000215', 'department_id' => 6],
            ['name' => 'Nilar Khaing', 'employee_id' => '001-000915', 'department_id' => 6],
            ['name' => 'Soe Yu Nwe', 'employee_id' => '008-000087', 'department_id' => 6],
            ['name' => 'Nway Wutt Yee', 'employee_id' => '001-000300', 'department_id' => 6],
            ['name' => 'Thandar Aye', 'employee_id' => '000-000144', 'department_id' => 6],
            ['name' => 'Pann Ei San', 'employee_id' => '000-000248', 'department_id' => 6],
            ['name' => 'Ei Ei Aung', 'employee_id' => '112-000010', 'department_id' => 6],
            ['name' => 'Khin Mar Phyu', 'employee_id' => '000-000045', 'department_id' => 6],
            ['name' => 'Khin Sandar Aye', 'employee_id' => '001-000336', 'department_id' => 6],
            ['name' => 'Yu Naing', 'employee_id' => '001-000693', 'department_id' => 6],
            ['name' => 'Aye malar Nyunt', 'employee_id' => '002-000428', 'department_id' => 6],
            ['name' => 'Ei Thandar Kyaw', 'employee_id' => '003-000086', 'department_id' => 6],
            ['name' => 'Aye Chan May', 'employee_id' => '001-000254', 'department_id' => 6],
            ['name' => 'Cho Cho Win', 'employee_id' => '000-000276', 'department_id' => 6],
            ['name' => 'Poe Ei Phyu', 'employee_id' => '002-000057', 'department_id' => 6],
            ['name' => 'Nay Chi', 'employee_id' => '001-000694', 'department_id' => 6],
            ['name' => 'Thazin Aung', 'employee_id' => '000-000172', 'department_id' => 6],
            ['name' => 'Nan Zin Mar Shein', 'employee_id' => '008-000229', 'department_id' => 6],
            ['name' => 'Yin Mar Htwe', 'employee_id' => '008-000031', 'department_id' => 6],
            ['name' => 'Khatta Kyaw', 'employee_id' => '001-000884', 'department_id' => 6],
            ['name' => 'Zaw Win Htet', 'employee_id' => '112-000016', 'department_id' => 6],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate([
                'employee_id'   => $userData['employee_id'],
            ],[
                'name'          => $userData['name'],
                'password'      => Hash::make($userData['employee_id']),
                'branch_id'     => $branch?->id, // Head Office
                'status_id'     => $status?->id, // Active
                'department_id' => $department?->id, // Merchandise
            ]);

            if ($role) {
                RoleUser::firstOrCreate([
                    'role_id' => $role->id,
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}

// php artisan make:seeder MerchandiseUserSeeder
// php artisan db:seed --class=MerchandiseUserSeeder
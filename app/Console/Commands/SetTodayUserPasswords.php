<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetTodayUserPasswords extends Command
{
    protected $signature = 'users:set-today-passwords';

    protected $description = 'Set today created users password from employee_id';

    public function handle()
    {
        $users = User::whereDate('created_at', today())
            ->whereNotNull('employee_id')
            ->get();
        // dd($users);

        foreach ($users as $user) {
            $user->password = Hash::make($user->employee_id);
            $user->save();

            // Start Assign Role
            $departmentRoleMap = [
                'Operation'   => 'Viewer',
                'Merchandise' => 'Editor',
                'Marketing'   => 'Ecommerce Admin',
            ];

            $department = Department::find($user['department_id']);

            $roleName = $departmentRoleMap[$department->name] ?? null;
            if ($roleName) {
                $role_id = Role::where('name', $roleName)->value('id');

                RoleUser::create([
                    'role_id' => $role_id,
                    'user_id' => $user['id'],
                ]);
            }

            // End Assign Role
        }

        $this->info("Updated {$users->count()} users.");

        return Command::SUCCESS;
    }
}

// php artisan make:command SetTodayUserPasswords

// php artisan users:set-today-passwords
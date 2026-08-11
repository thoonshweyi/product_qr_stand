<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Models\User;
use App\Models\Workflow;
use Illuminate\Database\Seeder;

class WorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $statusId = Status::whereIn('slug', ['active', 'enabled'])
            ->value('id') ?? Status::whereKey(1)->value('id') ?? Status::value('id');
        $userId = User::value('id');

        foreach ([
            ['name' => 'Stand', 'slug' => 'stand'],
            ['name' => 'Online', 'slug' => 'online'],
        ] as $workflow) {
            Workflow::updateOrCreate(
                ['slug' => $workflow['slug']],
                $workflow + ['status_id' => $statusId, 'user_id' => $userId],
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChannelSeeder extends Seeder
{
    public function run(): void
    {
        $statusId = Status::whereIn('slug', ['active', 'enabled'])
            ->value('id') ?? Status::whereKey(1)->value('id') ?? Status::value('id');
        $userId = User::value('id');

        foreach ([
            ['name' => 'Stand', 'slug' => 'stand'],
            ['name' => 'Online', 'slug' => 'online'],
        ] as $channel) {
            Channel::updateOrCreate(
                ['slug' => $channel['slug']],
                $channel + ['status_id' => $statusId, 'user_id' => $userId],
            );
        }
    }
}

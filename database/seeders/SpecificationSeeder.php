<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Specification;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class SpecificationSeeder extends Seeder
{
    public function run(): void
    {
        $statusId = Status::whereKey(3)->value('id') ?? Status::value('id');
        $userId = User::value('id');
        $categoryId = Category::value('id');

        if (! $statusId || ! $userId || ! $categoryId) {
            throw new RuntimeException('A status, user, and category must exist before seeding specifications.');
        }

        foreach (['Weight', 'Length', 'Width', 'Height', 'Size'] as $name) {
            Specification::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'status_id' => $statusId,
                    'user_id' => $userId,
                    'category_id' => $categoryId,
                ],
            );
        }
    }
}

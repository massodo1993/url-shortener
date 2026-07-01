<?php

namespace Database\Seeders;

use App\Models\Click;
use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name'  => 'Admin',
            'email' => 'admin@example.com',
        ]);

        Link::factory(3)->for($user)->create()->each(function (Link $link) {
            $count = rand(2, 10);
            Click::factory($count)->for($link)->create();
            $link->update(['clicks_count' => $count]);
        });
    }
}

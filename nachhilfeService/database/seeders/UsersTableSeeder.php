<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User();
        $user->name = 'testUser';
        $user->email = 'test@gmail.com';
        $user->password = bcrypt('secret');
        $user->role = 'geber';
        $user->ausbildung = 'KWM Bachelor';
        $user->save();

        $user1 = new User();
        $user1->name = 'AngebotNehmer';
        $user1->email = 'test2@gmail.com';
        $user1->password = bcrypt('secret');
        $user1->role = 'nehmer';
        $user1->ausbildung = 'HTL Informatik';
        $user1->save();

        $user2 = new User();
        $user2->name = 'AngebotGeber2';
        $user2->email = 'test3@gmail.com';
        $user2->password = bcrypt('secret');
        $user2->role = 'geber';
        $user2->ausbildung = 'HTL Informatik';
        $user2->save();
    }
}

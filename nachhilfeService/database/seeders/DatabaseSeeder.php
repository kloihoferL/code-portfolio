<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        $this->call(UsersTableSeeder::class);
        $this->call(CoursesTableSeeder::class);
        $this->call(SubCourseTableSeeder::class);
        $this->call(OfferTableSeeder::class);
        $this->call(SlotTableSeeder::class);
        $this->call(AppointmentRequestTableSeeder::class);
        $this->call(BookingTableSeeder::class);

    }
}

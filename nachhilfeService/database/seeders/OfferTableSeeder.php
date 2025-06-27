<?php

namespace Database\Seeders;

use App\Models\Offer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfferTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offer = new Offer();
        $offer->name = 'Mathe Nachhilfe';
        $offer->description = 'Ich biete Mathe Nachhilfe für alle Schulstufen an.';
        $offer->course_id = 1;
        $offer->user_id = 1;
        //comment ist default ''
        $offer->save();

        $offer2 = new Offer();
        $offer2->name = 'Webentwicklung Nachhilfe';
        $offer2->description = 'Ich biete Nachhilfe für HTML und JavaScript an.';
        $offer2->course_id = 2;
        $offer2->user_id = 1;
        //comment ist default ''
        $offer2->save();

    }
}

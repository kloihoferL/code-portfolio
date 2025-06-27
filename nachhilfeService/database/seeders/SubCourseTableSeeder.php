<?php

namespace Database\Seeders;

use App\Models\SubCourse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubCourseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subCourse = new SubCourse();
        $subCourse->name = 'Binomische Formeln';
        $subCourse->course_id = 1;
        $subCourse->save();

        $subCourse2 = new SubCourse();
        $subCourse2->name = 'Geometrie';
        $subCourse2->course_id = 1;
        $subCourse2->save();

        $subCourse3 = new SubCourse();
        $subCourse3->name = 'HTML';
        $subCourse3->course_id = 2;
        $subCourse3->save();

        $subCourse4 = new SubCourse();
        $subCourse4->name = 'JavaScript';
        $subCourse4->course_id = 2;
        $subCourse4->save();
    }
}

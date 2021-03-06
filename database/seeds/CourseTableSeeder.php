<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CourseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $faker = Faker::create();
      $courses =['PHP', 'Java', 'Offshore', 'Android'];
      $img = file_get_contents('public/images/fake.png');
        foreach (range(0,3) as $index) {
            DB::table('course')->insert([
              'name'         =>  $courses[$index],
              'image'        =>  base64_encode($img),
              'descriptions' =>  $faker->paragraph,
              'start_date'   =>  $faker->date('Y-m-d', now()),
              'end_date'     =>  $faker->date('Y-m-d', strtotime('+1 Days')),
              'created_at'   =>  now(),
              'updated_at'   =>  now()
            ]);
          }
    }
}

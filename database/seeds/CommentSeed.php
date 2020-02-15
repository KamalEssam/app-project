<?php

use Illuminate\Database\Seeder;
use App\Models\Comment;
class CommentSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      /*  $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            Comment::create([
                'visit_id' => $faker->numberBetween($from = 1, $to = 18),
                'comment' =>$faker->text($maxNbChars = 200),
            ]);
        }*/

        $row = new Comment();//1
        $row->visit_id = 1;
        $row->comment = "when an unknown printer took a galley of type and scrambled it to make a type specimen book.";
        $row->save();

        $row = new Comment();//1
        $row->visit_id = 1;
        $row->comment = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy ";
        $row->save();

        $row = new Comment();//1
        $row->visit_id = 2;
        $row->comment = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy ";
        $row->save();

        $row = new Comment();//1
        $row->visit_id = 2;
        $row->comment = "when an unknown printer took a galley of type and scrambled it to make a type specimen book.";
        $row->save();

        $row = new Comment();//1
        $row->visit_id = 2;
        $row->comment = "where an unknown printer took a galley of type and scrambled it to make a type specimen book.";
        $row->save();

        $row = new Comment();//1
        $row->visit_id = 3;
        $row->comment = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy ";
        $row->save();


        $row = new Comment();//1
        $row->visit_id = 4;
        $row->comment = "when an unknown printer took a galley of type and scrambled it to make a type specimen book.";
        $row->save();

        $row = new Comment();//1
        $row->visit_id = 5;
        $row->comment = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy ";
        $row->save();
    }
}

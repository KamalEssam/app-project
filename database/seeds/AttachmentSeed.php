<?php

use Illuminate\Database\Seeder;
use App\Models\Attachment;

class AttachmentSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $row = new Attachment();//1
        $row->user_id = 6;
        $row->attachment = 'default.pdf';
        $row->type = 1;
        $row->save();

        $row = new Attachment();//1
        $row->user_id = 5;
        $row->attachment = 'word.docx';
        $row->type = 2;
        $row->save();


        $row = new Attachment();//1
        $row->user_id = 25;
        $row->attachment = 'test.docx';
        $row->type = 2;
        $row->save();


        $row = new Attachment();//1
        $row->user_id = 26;
        $row->attachment = 'default.pdf';
        $row->type = 1;
        $row->save();
    }
}
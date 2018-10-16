<?php

use Illuminate\Database\Seeder;

class NoteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('notes')->delete();
        \DB::table('notes')->insert(array(
            0 => array(
                'author' => '4uuu Nya',
                'content' => 'nginx是坠吼的 ( 好麻烦，默认配置也是坠吼的'
            )
        ));
    }
}

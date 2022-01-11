<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PatternTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pattern_types = [
            [
                'id' => 1,
                'name' => '全文一致',
                'pattern' => '/<PATTERN>/'
            ],
            [
                'id' => 2,
                'name' => '部分一致',
                'pattern' => '/^(?=.*<PATTERN>).*$/'
            ],
            [
                'id' => 3,
                'name' => '前方一致',
                'pattern' => '/^<PATTERN>.*$/'
            ],
            [
                'id' => 4,
                'name' => '後方一致',
                'pattern' => '/.*<PATTERN>$/'
            ],
            [
                'id' => 5,
                'name' => '正規表現',
                'pattern' => '<PATTERN>'
            ],
            [
                'id' => 6,
                'name' => '全てのメッセージ',
                'pattern' => '/.*?/'
            ],
        ];

        foreach($pattern_types as $pattern_type){
            DB::table('pattern_types')->insert([
                'id' => $pattern_type['id'],
                'name' => $pattern_type['name'],
                'pattern' => $pattern_type['pattern'],
            ]);
        }

    }
}

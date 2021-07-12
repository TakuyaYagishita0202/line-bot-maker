<?php

namespace Database\Seeders;

use App\Models\MessageType;
use Illuminate\Database\Seeder;

class MessageTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $message_type = new MessageType;
        $message_type->prefix = 'text';
        $message_type->save();
    }
}

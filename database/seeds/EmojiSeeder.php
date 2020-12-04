<?php

use App\Models\Emoji;
use Illuminate\Database\Seeder;

class EmojiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $emojis = [
            'funny',
            'sad',
            'strange',
            'inspiring',
            'amazing',
            'dumb',
            'cool',
        ];

        foreach ($emojis as $emoji){
           Emoji::create(['name'=> $emoji]);
        }
    }
}

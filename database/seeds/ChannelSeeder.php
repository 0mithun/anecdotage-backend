<?php

use App\Models\Channel;
use Illuminate\Database\Seeder;

class ChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $channels = [
            'Entertainment', 'Other', 'Architecture', 'Art', 'Books', 'Business', 'Celebrities', 'Death', 'Dumb', 'Education', 'Food', 'Funny', 'History', 'Insults', 'Life', 'Love', 'Mistakes', 'Money', 'Movies',
            'Music', 'Politics', 'Pranks', 'Religion', 'Science', 'Sex', 'Sports', 'Travel', 'Television', 'War',
        ];

        foreach ($channels as $channel) {
           Channel::create([
               'name'   => $channel,
               'slug'   => str_slug($channel),
           ]);
        }
    }
}

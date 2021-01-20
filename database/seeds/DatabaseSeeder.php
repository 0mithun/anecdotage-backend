<?php

use App\Models\User;
use Illuminate\Database\Seeder;

use Grimzy\LaravelMysqlSpatial\Types\Point;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'           => 'Anecdotage Admins',
            'email'          => 'kakooljay@gmail.com',
            'username'       => 'anecdotage',
            'formatted_address'       => 'New York, USA',
            'location'       => new Point(40.71, -73.93),
            'password'       => bcrypt('secret'),
            'remember_token' => str_random(10),
            'email_verified_at'      => now(),
        ]);
        // $this->call(UsersTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(ChannelSeeder::class);
        $this->call(EmojiSeeder::class);
    }
}

<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $user = factory(App\User::class)->create([
             'username' => 'dangtu',
             'email' => 'dangtu@gmail.com',
             'password' => bcrypt('dangtu'),
             'lastname' => 'Mr',
             'firstname' => 'tu'
         ]);
    }
}

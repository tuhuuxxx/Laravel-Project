<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\User;

class ExampleTest extends DuskTestCase
{
    use DatabaseMigrations;
    
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $user = factory(User::class)->create([
            'email' => 'admin@gmail.com'
        ]);

        $this->browse(function ($browser) use ($user) {
           try {
                $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', 'admin')
                    ->press('Login')          
                    ->assertPathIs('/')
                    ->clickLink('User Management')
                    ->clickLink('Add new user')
                    ->pause(3000)
                    ->type('username', 'dangtupro')
                    ->type('firstname', 'tu')
                    ->pause(3000);
            } catch(\Exception $e){
                dump($e);
            }
        });
    }
}

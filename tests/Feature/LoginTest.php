<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function username_field_is_required()
    {

        $body = ['password' => 'test'];


        $response = $this->post(route('login'), $body, self::headers());

        $response->assertStatus(422)
            ->assertExactJson([
                'errors' => [
                    'username' => ['The username field is required.']
                ],
                'message' => "The username field is required.",
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function password_field_is_required()
    {

        $body = ['username' => 'test'];


        $response = $this->post(route('login'), $body, self::headers());

        $response->assertStatus(422)
            ->assertExactJson([
                'errors' => [
                    'password' => ['The password field is required.']
                ],
                'message' => "The password field is required.",
            ]);
    }

    /**
     * @test
     * @return void
     */
    public function password_field_should_match()
    {

        //create user (password is equals to 'password' by default)
        $user = User::factory()->count(1)->create()->first();

        $body = ['password' => 'password_not', 'username' => $user->username];

        $response = $this->post(route('login'), $body, self::headers());

        $response->assertStatus(401)
            ->assertExactJson([
                'meta' => [
                    'success' => false,
                    'errors' => ["Invalid Credentials"]
                ]
            ]);
    }


    /**
     * @test
     * @return void
     */
    public function should_be_able_to_login_with_the_right_credentials()
    {

        //create user (password is equals to 'password' by default)
        $user = User::factory()->count(1)->create()->first();

        $body = ['password' => 'password', 'username' => $user->username];

        $response = $this->post(route('login'), $body, self::headers());

        $response->assertStatus(200)
            ->assertJson([
                'meta' => [
                    'success' => true,
                    'errors' => []
                ],
                'data' => [
                    'minutes_to_expire' => 1440
                ]
            ]);

        //cotains token
        $response->assertJsonStructure(['data' => ['token']]);
    }


    /**
     * @test
     * @return void
     */
    public function last_login_field_should_be_updated_after_login()
    {

        //create user (password is equals to 'password' by default)
        $user = User::factory()->count(1)->create()->first();

        $body = ['password' => 'password', 'username' => $user->username];

        $response = $this->post(route('login'), $body, self::headers());

        $response->assertStatus(200)
            ->assertJson([
                'meta' => [
                    'success' => true,
                    'errors' => []
                ],
                'data' => [
                    'minutes_to_expire' => 1440
                ]
            ]);

        $oldLastLogin = $user->last_login;
        $currentLastLogin = $user->refresh()->last_login;

        self::assertNotEquals($oldLastLogin, $currentLastLogin);
    }


}

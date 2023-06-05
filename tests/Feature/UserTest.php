<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_given_email_and_name_when_calling_store_user_api_it_will_create_user(): void
    {
        $email = fake()->email();
        $name = "John Doe";

        $response = $this->post(route('user.create'), [
            'email' => $email,
            'name' => $name
        ]);

        $response->assertValid();
        $response->assertStatus(201)
                 ->assertJsonPath('data.name', $name)
                 ->assertJsonPath('data.email', $email);

        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('data.token')
        );
    }

    public function test_given_missing_name_when_calling_store_user_api_it_will_fail(): void {
        $response = $this->post(route('user.create'), [
            'email' => fake()->email()
        ]);

        $response->assertStatus(400);
        $response->assertInvalid(['name']);
    }

    public function test_given_missing_email_when_calling_store_user_api_it_will_fail(): void {
        $response = $this->post(route('user.create'), [
            'name' => fake()->name()
        ]);

        $response->assertStatus(400);
        $response->assertInvalid(['email']);
    }

    public function test_given_existing_email_when_calling_store_user_api_it_will_fail(): void {
        $email = fake()->email();
        $name = fake()->name();

        $this->post(route('user.create'), [
            'email' => $email,
            'name' => $name
        ]);

        $response = $this->post(route('user.create'), [
            'email' => $email,
            'name' => $name
        ]);

        $response->assertStatus(400);
        $response->assertInvalid(['email']);
    }
}

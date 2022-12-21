<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class CreateCustomerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /* public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    } */

    public function testRequiredFieldsForCreateCustomer()
    {
        $this->json('POST', 'api/create-customer', ['Accept' => 'application/json'])
            ->assertStatus(400)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "name" => ["The name field is required."],
                    "email" => ["The email field is required."],
                    "password" => ["The password field is required."],
                ]
            ]);
    }

    public function testRepeatPassword()
    {
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "password" => "demo12345"
        ];

        $this->json('POST', 'api/create-customer', $userData, ['Accept' => 'application/json'])
            ->assertStatus(400)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "password" => ["The password confirmation does not match."]
                ]
            ]);
    }

    public function testSuccessfulCreateCustomer()
    {
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "password" => "demo12345",
            "password_confirmation" => "demo12345"
        ];

        $this->json('POST', 'api/create-customer', $userData, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJsonStructure([
                "user" => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
                "access_token",
                "message"
            ]);
    }

    public function testCustomerListedSuccessfully()
    {
        $user = User::factory(2)->create();

        $this->json('GET', 'api/list-customer', ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJson([
                "users" => $user,
                "message" => "Retrieved successfully"
            ]);
    }

    public function testRetrieveCustomerSuccessfully()
    {
        $user = User::factory(1)->create();

        $this->json('GET', 'api/retrive-customer/' . $user->id, [], ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
                "user" => $user,
                "message" => "Retrieved successfully"
            ]);
    }

    public function testCustomerUpdatedSuccessfully()
    {
        $user = User::factory(1)->create();

        $payload = [
            "name" => "Susan3",
            "email" => "test@test3.com",
        ];

        $this->json('PATCH', 'api/update-customer/' . $user->id , $payload, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
                "user" => $user,
                "message" => "Updated successfully"
            ]);
    }

    public function testDeleteCustomer()
    {
       $user = \App\Models\User::factory()->create();
       dd(User::all());
       //$this->assertTrue(true);

        //$this->json('DELETE', 'api/delete-customer/' . $user->id, [], ['Accept' => 'application/json'])
            //->assertStatus(204);
    }
}

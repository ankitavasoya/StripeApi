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

        $user = factory(User::class)->create();
        $this->actingAs($user, 'api');

        factory(User::class)->create([
            "name" => "Susan",
            "email" => "susan@test.com",
            "password" => "12345678",
        ]);

        factory(User::class)->create([
            "name" => "Mark",
            "email" => "mark@test.com",
            "password" => "12345678",
        ]);

        $this->json('GET', 'api/list-customer', ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJson([
                "users" => [
                    [
                        "id" => 1,
                        "name" => "Susan",
                        "email" => "susan@test.com",
                    ],
                    [
                        "id" => 2,
                        "name" => "Mark",
                        "email" => "mark@test.com",
                    ]
                ],
                "message" => "Retrieved successfully"
            ]);
    }

    public function testRetrieveCustomerSuccessfully()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user, 'api');

        $ceo = factory(User::class)->create([
            "name" => "Susan",
            "email" => "test@test1.com",
            "password" => "12345678",
        ]);

        $this->json('GET', 'api/retrive-customer/' . $ceo->id, [], ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
                "ceo" => [
                        "name" => "Susan",
                        "email" => "test@test1.com",
                ],
                "message" => "Retrieved successfully"
            ]);
    }

    public function testCEOUpdatedSuccessfully()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user, 'api');

        $ceo = factory(User::class)->create([
            "name" => "Susan2",
            "email" => "test@test2.com",
            "password" => "12345678",
        ]);

        $payload = [
            "name" => "Susan3",
            "email" => "test@test3.com",
        ];

        $this->json('PATCH', 'api/update-customer/' . $ceo->id , $payload, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
                "ceo" => [
                    "name" => "Susan3",
                    "email" => "test@test3.com",
                    "password" => "12345678",
                ],
                "message" => "Updated successfully"
            ]);
    }

    public function testDeleteCustomer()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user, 'api');

        $ceo = factory(User::class)->create([
            "name" => "Susan",
            "email" => "test@test.com",
            "password" => "2014",
        ]);

        $this->json('DELETE', 'api/delete-customer/' . $ceo->id, [], ['Accept' => 'application/json'])
            ->assertStatus(204);
    }
}

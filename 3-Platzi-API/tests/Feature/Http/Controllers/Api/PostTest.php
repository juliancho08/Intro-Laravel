<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\User;
use App\Post;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Los usuarios no autenticados no pueden acceder al api de post.
     *
     * @test
     */
    public function unauthenticated_users_cannot_access_the_post_api()
    {
        //$this->withoutExceptionHandling();

        $this->json('GET',    '/api/posts')->assertStatus(401);
        $this->json('POST',   '/api/posts')->assertStatus(401);
        $this->json('GET',    '/api/posts/1000')->assertStatus(401);
        $this->json('PUT',    '/api/posts/1000')->assertStatus(401);
        $this->json('DELETE', '/api/posts/1000')->assertStatus(401);
    }

    /**
     * Los usuarios autenticados pueden acceder al listado de posts.
     *
     * @test
     */
    public function can_see_paginated_post_list()
    {
        //$this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        factory(Post::class, 5)->create();

        $response = $this->actingAs($user, 'api')->json('GET', '/api/posts');

        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'created']
            ],
            'links' => ['first', 'last', 'prev', 'next'],
        ])->assertStatus(200);
    }

    /**
     * Los usuarios autenticados pueden crear un post.
     *
     * @test
     */
    public function a_user_can_create_a_post()
    {
        //$this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->json('POST', '/api/posts', [
            'title' => 'Post de prueba'
        ]);

        $response->assertJsonStructure(['id', 'title', 'created'])
            ->assertJson(['title' => 'Post de prueba'])
            ->assertStatus(201);

        $this->assertDatabaseHas('posts', [
            'title' => 'Post de prueba'
        ]);
    }

    /**
     * Validación si crea un post con el título vacío.
     *
     * @test
     */
    public function validation_if_you_create_a_post_with_the_empty_title()
    {
        //$this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->json('POST', '/api/posts', [
            'title' => ''
        ]);

        $response->assertStatus(422)
            //->assertJsonValidationErrors('title')
            ->assertExactJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'title' => ['The title field is required.']
                ]
            ]);
    }

    /**
     * Puedo acceder y ver un post.
     *
     * @test
     */
    public function can_get_a_post()
    {
        //$this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        $post = factory(Post::class)->create();

        $response = $this->actingAs($user, 'api')->json('GET', "/api/posts/$post->id");

        $response->assertJsonStructure(['id', 'title', 'created'])
            ->assertJson(['title' => $post->title])
            ->assertStatus(200);
    }

    /**
     * Si el post no existe recibiré un 404.
     *
     * @test
     */
    public function get_404_if_the_post_is_not_found()
    {
        //$this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->json('GET', '/api/posts/1000');

        $response->assertStatus(404);
    }

    /**
     * Los usuarios autenticados pueden actualizar un post.
     *
     * @test
     */
    public function a_user_can_update_a_post()
    {
        //$this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        $post = factory(Post::class)->create();

        $response = $this->actingAs($user, 'api')->json('PUT', "/api/posts/$post->id", [
            'title' => 'Nuevo título'
        ]);

        $response->assertJsonStructure(['id', 'title', 'created'])
            ->assertJson(['title' => 'Nuevo título'])
            ->assertStatus(200);

        $this->assertDatabaseHas('posts', [
            'title' => 'Nuevo título'
        ]);
    }

    /**
     * Los usuarios autenticados pueden eliminar un post.
     *
     * @test
     */
    public function can_delete_a_post()
    {
        //$this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        $post = factory(Post::class)->create();

        $response = $this->actingAs($user, 'api')->json('DELETE', "/api/posts/$post->id", [
            'title' => 'Nuevo título'
        ]);

        $response->assertStatus(204)->assertSee(null);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}

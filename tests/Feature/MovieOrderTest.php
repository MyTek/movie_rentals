<?php


use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Movie;

class MovieOrderTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test fetching movies.
     */
    public function test_can_fetch_movies()
    {
        // Arrange: Seed the database with movies
        Movie::factory()->count(3)->create();

        // Act: Call the endpoint
        $response = $this->getJson('/api/movies');

        // Assert: Check the response
        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'title', 'price', 'tag', 'adjusted_price'],
            ]);
    }

    /**
     * Test creating an order.
     */
    public function test_can_create_order()
    {
        // Arrange: Seed the database with movies
        $movies = Movie::factory()->count(2)->create();

        $movieIds = $movies->pluck('id')->toArray();

        // Act: Post to the orders endpoint
        $response = $this->postJson('/api/orders', [
            'movie_ids' => $movieIds,
        ]);

        // Assert: Check the response
        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'total',
                'movies' => [
                    '*' => ['id', 'title', 'price', 'tag'],
                ],
            ]);

        // Additional check: Verify the order exists in the database
        $this->assertDatabaseHas('orders', [
            'id' => $response->json('id'),
        ]);
    }

    /**
     * Test order validation.
     */
    public function test_cannot_create_order_with_invalid_movies()
    {
        // Act: Post with invalid movie IDs
        $response = $this->postJson('/api/orders', [
            'movie_ids' => [999, 1000], // Non-existent movie IDs
        ]);

        // Assert: Ensure validation fails
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['movie_ids.0', 'movie_ids.1']);
    }

    /**
     * Test that the total is calculated correctly when creating an order.
     */
    public function test_order_total_is_calculated_correctly()
    {
        // Arrange: Seed the database with movies
        $movies = Movie::factory()->createMany([
            ['title' => 'Movie 1', 'price' => 10.00, 'tag' => 'trending'],
            ['title' => 'Movie 2', 'price' => 20.00, 'tag' => 'under'],
            ['title' => 'Movie 3', 'price' => 30.00, 'tag' => null],
        ]);

        $movieIds = $movies->pluck('id')->toArray();

        // Calculate expected total using the model logic
        $expectedTotal = $movies->reduce(function ($carry, $movie) {
            return $carry + ($movie->price * $movie->getPriceByTag());
        }, 0);

        $expectedTotal = round($expectedTotal, 2); // Ensure proper rounding

        // Act: Post to the orders endpoint
        $response = $this->postJson('/api/orders', [
            'movie_ids' => $movieIds,
        ]);

        // Assert: Check the response
        $response->assertStatus(201)
            ->assertJson([
                'total' => $expectedTotal,
            ]);

        // Additional check: Verify the order exists in the database with the correct total
        $this->assertDatabaseHas('orders', [
            'id' => $response->json('id'),
            'total' => $expectedTotal,
        ]);
    }


}

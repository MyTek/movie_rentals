<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Movie;

class OrdersTableSeeder extends Seeder
{
    public function run()
    {
        // Fetch all movies
        $movies = Movie::all();

        // Create sample orders
        $orders = [
            // Order 1: Includes first 2 movies
            ['movie_ids' => [1, 2]],
            // Order 2: Includes last 2 movies
            ['movie_ids' => [7, 8]],
            // Order 3: Random selection
            ['movie_ids' => [3, 5]],
        ];

        foreach ($orders as $orderData) {
            // Calculate total cost
            $total = collect($orderData['movie_ids'])->reduce(function ($sum, $id) use ($movies) {
                $movie = $movies->find($id);
                if (!$movie) return $sum;

                $price = $movie->price;
                if ($movie->tag === 'trending') $price *= 1.35;
                if ($movie->tag === 'under') $price *= 0.5;

                return $sum + $price;
            }, 0);

            // Create the order
            $order = Order::create(['total' => $total]);

            // Attach movies to the order
            $order->movies()->attach($orderData['movie_ids']);
        }
    }
}

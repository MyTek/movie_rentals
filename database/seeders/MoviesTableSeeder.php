<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Movie;

class MoviesTableSeeder extends Seeder
{
    public function run()
    {
        $movies = [
            ['title' => 'Inception', 'price' => 10.00, 'tag' => 'trending'],
            ['title' => 'The Shawshank Redemption', 'price' => 8.00, 'tag' => 'under'],
            ['title' => 'Interstellar', 'price' => 12.00, 'tag' => null],
            ['title' => 'The Dark Knight', 'price' => 15.00, 'tag' => 'trending'],
            ['title' => 'Pulp Fiction', 'price' => 9.50, 'tag' => 'under'],
            ['title' => 'Forrest Gump', 'price' => 10.50, 'tag' => null],
            ['title' => 'The Matrix', 'price' => 11.00, 'tag' => 'trending'],
            ['title' => 'Fight Club', 'price' => 8.75, 'tag' => 'under'],
        ];

        foreach ($movies as $movie) {
            Movie::create($movie);
        }
    }
}

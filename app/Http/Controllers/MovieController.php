<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

/**
 * @OA\Info(title="Movie Rentals API", version="1.0.0")
 */
class MovieController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/movies",
     *     summary="Get all movies with adjusted prices",
     *     @OA\Response(
     *         response=200,
     *         description="List of movies with adjusted prices",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="price", type="number", format="float"),
     *                 @OA\Property(property="tag", type="string", nullable=true),
     *                 @OA\Property(property="adjusted_price", type="number", format="float")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $movies = Movie::priceAdjusted();
        return response()->json($movies);
    }

    /**
     * @OA\Post(
     *     path="/api/movies",
     *     summary="Create a new movie",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="price", type="number", format="float"),
     *             @OA\Property(property="tag", type="string", nullable=true, enum={"trending", "under"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Movie created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="price", type="number", format="float"),
     *             @OA\Property(property="tag", type="string", nullable=true)
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'tag' => 'nullable|in:trending,under',
        ]);

        $movie = Movie::create($validated);
        return response()->json($movie, 201); // 201 Created
    }

    /**
     * @OA\Get(
     *     path="/api/movies/{id}",
     *     summary="Get a single movie",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Movie details",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="price", type="number", format="float"),
     *             @OA\Property(property="tag", type="string", nullable=true)
     *         )
     *     )
     * )
     */
    public function show(Movie $movie)
    {
        return response()->json($movie);
    }

    /**
     * @OA\Put(
     *     path="/api/movies/{id}",
     *     summary="Update a movie",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="price", type="number", format="float"),
     *             @OA\Property(property="tag", type="string", nullable=true, enum={"trending", "under"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Movie updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="price", type="number", format="float"),
     *             @OA\Property(property="tag", type="string", nullable=true)
     *         )
     *     )
     * )
     */
    public function update(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'tag' => 'nullable|in:trending,under',
        ]);

        $movie->update($validated);
        return response()->json($movie);
    }

    /**
     * @OA\Delete(
     *     path="/api/movies/{id}",
     *     summary="Delete a movie",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Movie deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function destroy(Movie $movie)
    {
        $movie->delete();
        return response()->json(['message' => 'Movie deleted successfully.']);
    }
}

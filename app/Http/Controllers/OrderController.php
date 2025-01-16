<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Movie;
use Illuminate\Http\Request;

/**
 * @OA\Info(title="Movie Rentals API", version="1.0.0")
 */
class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Get all orders",
     *     @OA\Response(
     *         response=200,
     *         description="List of orders with related movies",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="total", type="number", format="float"),
     *                 @OA\Property(
     *                     property="movies",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="title", type="string"),
     *                         @OA\Property(property="price", type="number", format="float"),
     *                         @OA\Property(property="tag", type="string", nullable=true)
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $orders = Order::with('movies')->get();
        return response()->json($orders);
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Create a new order",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="movie_ids", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="total", type="number", format="float"),
     *             @OA\Property(
     *                 property="movies",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="price", type="number", format="float"),
     *                     @OA\Property(property="tag", type="string", nullable=true)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'movie_ids' => 'required|array',
            'movie_ids.*' => 'exists:movies,id',
        ]);

        $movies = Movie::whereIn('id', $validated['movie_ids'])->get();

        $total = $movies->reduce(function ($carry, $movie) {
            $price = $movie->price * $movie->getPriceByTag();
            return $carry + $price;
        }, 0);

        $total = ceil($total * 100) / 100;

        $order = Order::create(['total' => $total]);
        $order->movies()->attach($validated['movie_ids']);

        return response()->json($order->load('movies'), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Get a single order",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order details",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="total", type="number", format="float"),
     *             @OA\Property(
     *                 property="movies",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="price", type="number", format="float"),
     *                     @OA\Property(property="tag", type="string", nullable=true)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function show(Order $order)
    {
        return response()->json($order->load('movies'));
    }

    /**
     * @OA\Put(
     *     path="/api/orders/{id}",
     *     summary="Update an order",
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
     *             @OA\Property(property="movie_ids", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="total", type="number", format="float"),
     *             @OA\Property(
     *                 property="movies",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="price", type="number", format="float"),
     *                     @OA\Property(property="tag", type="string", nullable=true)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'movie_ids' => 'required|array',
            'movie_ids.*' => 'exists:movies,id',
        ]);

        $movies = Movie::whereIn('id', $validated['movie_ids'])->get();

        $total = $movies->reduce(function ($carry, $movie) {
            $price = $movie->price * $movie->getPriceByTag();
            return $carry + $price;
        }, 0);

        $total = ceil($total * 100) / 100;

        $order->update(['total' => $total]);
        $order->movies()->sync($validated['movie_ids']);

        return response()->json($order->load('movies'));
    }

    /**
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     summary="Delete an order",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function destroy(Order $order)
    {
        $order->movies()->detach();
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully.']);
    }
}

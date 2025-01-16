import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { Head } from '@inertiajs/react';

// Helper function to get tag descriptions
const getTagDescription = (tag: 'trending' | 'under' | '' | null): string => {
    switch (tag) {
        case 'trending':
            return 'Trending Now';
        case 'under':
            return 'Under the Radar';
        case '':
        case null:
            return 'No Tag';
        default:
            return 'Unknown Tag';
    }
};

interface Movie {
    id: number;
    title: string;
    price: number;
    tag: 'trending' | 'under' | '' | null;
    adjusted_price: number;
}

interface Order {
    id: number;
    total: number;
    movies: Movie[];
}

interface Props {
    movies?: Movie[]; // Optional movies prop
}

export default function MovieRentals({ movies = [] }: Props) {
    const [selectedMovies, setSelectedMovies] = useState<number[]>([]);
    const [totalCost, setTotalCost] = useState<number>(0);
    const [isSubmitting, setIsSubmitting] = useState<boolean>(false);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);
    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [order, setOrder] = useState<Order | null>(null);

    const handleCheckboxChange = (movie: Movie) => {
        const isSelected = selectedMovies.includes(movie.id);

        const updatedSelection = isSelected
            ? selectedMovies.filter((id) => id !== movie.id)
            : [...selectedMovies, movie.id];

        setSelectedMovies(updatedSelection);
    };

    useEffect(() => {
        const calculateTotalCost = () => {
            const total = selectedMovies.reduce((sum, id) => {
                const movie = movies.find((m) => m.id === id);
                return movie ? sum + movie.adjusted_price : sum;
            }, 0);
            setTotalCost(total);
        };

        calculateTotalCost();
    }, [selectedMovies, movies]);

    const handleSubmit = async () => {
        setIsSubmitting(true);
        setErrorMessage(null);
        setSuccessMessage(null);

        try {
            const response = await axios.post('/api/orders', { movie_ids: selectedMovies });
            setOrder(response.data);
            setSuccessMessage('Order placed successfully!');
            setSelectedMovies([]);
            setTotalCost(0); // Reset total cost
        } catch (error) {
            setErrorMessage('Failed to place the order. Please try again.');
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <>
            <Head title="Movie Rentals" />
            <div className="min-h-screen bg-gray-100 dark:bg-gray-900 p-6">
                <h1 className="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">Movie Rentals</h1>

                {/* Feedback Messages */}
                {errorMessage && (
                    <div className="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
                        {errorMessage}
                    </div>
                )}
                {successMessage && (
                    <div className="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
                        {successMessage}
                    </div>
                )}

                {/* Movies List */}
                <div className="bg-white dark:bg-gray-800 shadow rounded p-4">
                    <h2 className="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Available Movies</h2>
                    {movies.length === 0 ? (
                        <p className="text-gray-600 dark:text-gray-400">No movies available.</p>
                    ) : (
                        <ul className="space-y-4">
                            {movies.map((movie) => (
                                <li
                                    key={movie.id}
                                    className="flex items-center justify-between bg-gray-100 dark:bg-gray-700 p-4 rounded"
                                >
                                    <div>
                                        <h3 className="font-bold text-gray-800 dark:text-gray-200">{movie.title}</h3>
                                        <p className="text-sm text-gray-600 dark:text-gray-400">
                                            Price: ${movie.adjusted_price.toFixed(2)}{' '}
                                            {movie.tag && (
                                                <span
                                                    className={
                                                        movie.tag === 'trending'
                                                            ? 'text-green-500'
                                                            : movie.tag === 'under'
                                                                ? 'text-red-500'
                                                                : 'text-gray-700'
                                                    }
                                                >
                                                    ({getTagDescription(movie.tag)})
                                                </span>
                                            )}
                                        </p>
                                    </div>
                                    <input
                                        type="checkbox"
                                        checked={selectedMovies.includes(movie.id)}
                                        onChange={() => handleCheckboxChange(movie)}
                                        className="h-5 w-5 text-red-500 focus:ring-red-500 rounded"
                                    />
                                </li>
                            ))}
                        </ul>
                    )}
                </div>

                {/* Order Summary */}
                <div className="bg-white dark:bg-gray-800 shadow rounded p-4 mt-6">
                    <h2 className="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Order Summary</h2>
                    <p className="text-gray-800 dark:text-gray-200">
                        Total Selected: {selectedMovies.length}
                    </p>
                    <p className="text-gray-800 dark:text-gray-200">Total Cost: ${totalCost.toFixed(2)}</p>
                    {order && (
                        <div className="mt-4">
                            <p className="text-gray-800 dark:text-gray-200">Order ID: {order.id}</p>
                            <p className="text-gray-800 dark:text-gray-200">Order Total: ${order.total.toFixed(2)}</p>
                            <ul className="mt-2 text-gray-600 dark:text-gray-400">
                                {order.movies.map((movie) => (
                                    <li key={movie.id}>{movie.title}</li>
                                ))}
                            </ul>
                        </div>
                    )}
                    <button
                        onClick={handleSubmit}
                        disabled={selectedMovies.length === 0 || isSubmitting}
                        className="mt-4 bg-red-500 text-white px-4 py-2 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {isSubmitting ? 'Placing Order...' : 'Place Order'}
                    </button>
                </div>
            </div>
        </>
    );
}

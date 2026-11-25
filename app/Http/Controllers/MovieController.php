<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class MovieController extends Controller
{
    /**
     * Display a listing of the movies.
     */
    public function index(Request $request)
    {
        $query = Movie::with('genre');

        // 1. Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // 2. Genre Filter
        if ($request->filled('genre_id')) {
            $query->where('genre_id', $request->genre_id);
        }

        // 3. Boolean Filter (Careful sa "0")
        if ($request->is_featured !== null && $request->is_featured !== '') {
            $query->where('is_featured', $request->is_featured);
        }

        // Sorting, may allowed list ang column at ang direction
        $sort = $request->query('sort', 'title');
        $direction = $request->query('direction', 'asc');

        $allowedSorts = ['title', 'year', 'rating'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'title';
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $query->orderBy($sort, $direction);

        // 4. Pagination
        $movies = $query->paginate(10)->withQueryString();

        // Kailangan ipasa ang genres para sa dropdown
        $genres = Genre::all();

        return view('movies.index', compact('movies', 'genres'));
    }

    /**
     * Show the form for creating a new movie.
     */
    public function create()
    {
        Gate::authorize('create', Movie::class);

        $genres = Genre::all();
        return view('movies.create', compact('genres'));
    }

    /**
     * Store a newly created movie in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Movie::class);

        $validated = $request->validate([
            'title' => 'required|max:100',
            'genre_id' => 'required|exists:genres,id',
            'rating' => 'required|numeric',
            'director' => 'required|max:100',
            'year' => 'required|numeric',
        ]);

        $movie = Movie::create($validated);
        Log::info('Movie saved', [
            'id' => $movie->id,
        ]);

        return redirect()->route('movies.index')->with('success', 'Movie added successfully.');
    }

    /**
     * Display the specified movie.
     */
    public function show(Movie $movie)
    {
        return view('movies.show', compact('movie'));
    }

    /**
     * Show the form for editing the specified movie.
     */
    public function edit(Movie $movie)
    {
        Gate::authorize('update', $movie);

        $genres = Genre::all();
        return view('movies.edit', compact('movie', 'genres'));
    }

    /**
     * Update the specified movie in storage.
     */
    public function update(Request $request, Movie $movie)
    {
        Gate::authorize('update', $movie);

        $validated = $request->validate([
            'title' => 'required|max:100',
            'genre_id' => 'required|exists:genres,id',
            'rating' => 'required|numeric',
            'director' => 'required|max:100',
            'year' => 'required|numeric',
        ]);

        $movie->update($validated);
        return redirect()->route('movies.index')->with('success', 'Movie updated!');
    }

    /**
     * Remove the specified movie from storage.
     */
    public function destroy(Movie $movie)
    {
        Gate::authorize('delete', $movie);

        // Guard mula sa Lesson 12, dalawang magkaibang pagtanggi
        abort_if($movie->rating == 10, 403, 'Bawal i-delete ang perfect movie!');

        $movie->delete();
        return redirect()->route('movies.index')->with('success', 'Movie deleted!');
    }

    /**
     * Show a featured movie.
     */
    public function featured()
    {
        $movie = Movie::where('is_featured', true)->first()
            ?? Movie::first();

        return view('movies.show', ['movie' => $movie]);
    }
}
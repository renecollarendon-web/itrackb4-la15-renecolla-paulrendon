<?php

namespace App\Policies;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MoviePolicy
{
    
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Movie $movie): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Movie $movie): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Movie $movie): bool
    {
        return $user->role === 'admin';
    }
  
    public function restore(User $user, Movie $movie): bool
    {
        return false;
    }

    public function forceDelete(User $user, Movie $movie): bool
    {
        return false;
    }
}
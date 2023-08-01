<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class MovieEvent extends Event
{
    public const EVENT_NAME = 'movie.add_to_favorites';
    private $movieId;
    private $userId;

    public function __construct($movieId, $userId)
    {
        $this->movieId = $movieId;
        $this->userId = $userId;
    }

    public function getMovieId(): String
    {
        return $this->movieId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}

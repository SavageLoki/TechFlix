<?php

namespace App\Event;

use App\Repository\ViewsRepository;
use App\Service\MovieService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\Event;

class ViewsEvent extends Event
{
    protected $movieId;
    protected MovieService $movieService;

    public function __construct($movieId, MovieService $movieService)
    {
        $this->movieId = $movieId;
        $this->movieService = $movieService;
    }

    public function getViewsMovie($movieId)
    {
        $view = $this->movieService->getViewsOfMovie($movieId);

        return $view;
    }

    public function getMovieId()
    {
        return $this->movieId;
    }
}

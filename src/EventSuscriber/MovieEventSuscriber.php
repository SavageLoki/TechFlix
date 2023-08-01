<?php

namespace App\EventSuscriber;

use App\Event\MovieEvent;
use App\Service\MovieService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MovieEventSuscriber implements EventSubscriberInterface
{
    private $security;
    private $entitytManager;
    private MovieService $movieService;

    public function __construct(Security $security, EntityManagerInterface $entityManager, MovieService $movieService)
    {
        $this->security = $security;
        $this->entitytManager = $entityManager;
        $this->movieService = $movieService;
    }

    public static function getSubscribedEvents()
    {
        return [
            MovieEvent::EVENT_NAME => 'addToFavorites',
        ];
    }

    public function addToFavorites(MovieEvent $event)
    {
        $movieId = $event->getMovieId();
        $userId = $event->getUserId();
        $this->movieService->addToFavorites($movieId, $userId);
    }
}

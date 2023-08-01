<?php

namespace App\Service;

use App\Entity\Favorites;
use App\Entity\Views;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;


class MovieService
{
    private $entityManager;
    private $security;
    private ApiService $apiService;

    public function __construct(EntityManagerInterface $entityManager, Security $security, ApiService $apiService)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->apiService = $apiService;
    }

    public function getViewsOfMovie($movieId)
    {
        $nbView = 0;
        $views = $this->entityManager->getRepository(Views::class)->findOneBy(['movieId' => $movieId]);

        if ($views == NULL) {
            $views = new Views();
            $views->setNbView(1);
            $views->setMovieId($movieId);

            $this->entityManager->persist($views);
        } else {
            $nbView = $views->getNbView();
            $nbView = $nbView + 1;
            $views->setNbView($nbView);
        }
        $this->entityManager->flush();
        return $views;
    }

    public function addToFavorites($movieId, $userId)
    {
        $favorite = $this->entityManager->getRepository(Favorites::class)->findOneBy(['movie_id' => $movieId, 'user_id' => $userId]);

        if ($favorite == NULL) {
            $favorite = new Favorites;
            $favorite->setMovieId($movieId);
            $favorite->setUserId($userId);
            $this->entityManager->persist($favorite);
            $this->entityManager->flush();
        }
    }

    public function getFavorites($userId)
    {
        $arrayId = [];
        $favorites = $this->entityManager->getRepository(Favorites::class)->findBy(['user_id' => $userId]);
        foreach ($favorites as $favorite) {
            array_push($arrayId, $favorite->getMovieId());
        }

        $listId = implode(',', $arrayId);
        $favoritesMovies = $this->apiService->getListOfMovies($listId);

        return $favoritesMovies;
    }
}

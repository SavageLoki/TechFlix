<?php

namespace App\Controller;

use App\Entity\Views;
use App\Entity\Favorites;
use App\Event\MovieEvent;
use App\Event\ViewsEvent;
use App\Service\ApiService;
use App\Form\SearchMovieType;
use App\Service\MovieService;
use App\Event\MovieEventSuscriber;
use Doctrine\Migrations\EventDispatcher;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @IsGranted("ROLE_USER")
 */
class MovieController extends AbstractController
{
    /**
     * @Route("/movie", name="movie_index")
     */
    public function index(ApiService $apiService, Request $request, PaginatorInterface $paginator): Response
    {
        $movies = $apiService->getMovies();

        $movies = $paginator->paginate(
            $movies,
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('movie/index.html.twig', [
            'movies' => $movies
        ]);
    }

    /**
     * @Route("/movie/view/{id}", name="movie_view")
     */
    public function view(ApiService $apiService, $id, EventDispatcherInterface $eventDispatcher, MovieService $movieService, EntityManagerInterface $entityManagerInterface)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $userId = $user->getId();

        $movie = $apiService->getOneMovie($id);
        $rating = $apiService->getRating($id);

        $views = $eventDispatcher->dispatch(new ViewsEvent($id, $movieService), 'movie.page_clicked');

        $nbView  = $entityManagerInterface->getRepository(Views::class)->findOneBy(array('movieId' => $id));
        $nbView = $nbView->getNbView();

        $favorite = $entityManagerInterface->getRepository(Favorites::class)->findOneBy(['movie_id' => $id, 'user_id' => $userId]);


        return $this->render('movie/view.html.twig', [
            'movie' => $movie,
            'rating' => $rating,
            'views' => $nbView,
            'favorite' => $favorite
        ]);
    }

    /**
     * @Route("/movie/add/{movieId}", name="movie_favorite")
     */
    public function addToFavorites($movieId, EventDispatcherInterface $eventDispatcher, Security $security, EntityManagerInterface $entityManager, MovieService $movieService)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $userId = $user->getId();

        $event = new MovieEvent($movieId, $userId);
        $event = $eventDispatcher->dispatch($event, MovieEvent::EVENT_NAME);

        return $this->redirectToRoute('movie_view', ['id' => $movieId]);
    }

    /**
     * @Route("/favorites", name="list_favorite")
     */
    public function favorites(MovieService $movieService, Request $request, PaginatorInterface $paginator)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $userId = $user->getId();

        $favorites = $movieService->getFavorites($userId);

        if ($favorites != NULL) {
            $favorites = $paginator->paginate(
                $favorites,
                $request->query->getInt('page', 1),
                8
            );
        }


        return $this->render('movie/favorites.html.twig', [
            'favorites' => $favorites
        ]);
    }

    /**
     * @Route("/search", name="movie_search")
     */
    public function search(Request $request, ApiService $apiService)
    {


        $defaultData = ['message' => 'Form without Entity'];
        $form = $this->createFormBuilder($defaultData, [
            'method' => 'POST',
            'action' => $this->generateUrl('movie_search')
        ])
            ->add('movie', TextType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $keyword = $form->getData();
            $movieFound = $apiService->search($keyword['movie']);

            return $this->render('movie/searchResults.html.twig', [
                'movieFound' => $movieFound
            ]);
        }

        return $this->renderForm('movie/searchBar.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/", name="root")
     */
    public function rootPage()
    {
        return $this->redirectToRoute('movie_index');
    }
}

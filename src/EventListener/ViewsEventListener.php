<?php

namespace App\EventListener;

use App\Entity\Views;
use App\Event\ViewsEvent;
use App\Repository\ViewsRepository;
use Doctrine\DBAL\Schema\View;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ViewsEventListener
{

    public function __construct()
    {
    }

    public function onMoviePageClicked(ViewsEvent $event)
    {
        $movieId = $event->getMovieId();
        $view = $event->getViewsMovie($movieId);

        return $view;
    }
}

<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class ApiService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getMovies()
    {
        $currentPage = 1;
        $allResults = [];

        //We retrieve only few pages 
        while ($currentPage <= 10) {
            $response = $this->client->request(
                'GET',
                'https://moviesdatabase.p.rapidapi.com/titles',
                [
                    'headers' => [
                        'X-RapidAPI-Key' =>  $_ENV['API_KEY'],
                        'X-RapidAPI-Host' =>  $_ENV['API_HOST']
                    ],
                    'query' => [
                        'page' => $currentPage,

                    ]
                ]
            );
            $currentPage = $currentPage + 1;
            $result = $response->toArray();
            $allResults = array_merge($allResults, $result['results']);
        }


        return $allResults;
    }

    public function getOneMovie($id)
    {
        $response = $this->client->request(
            'GET',
            'https://moviesdatabase.p.rapidapi.com/titles/' . $id,
            [
                'headers' => [
                    'X-RapidAPI-Key' =>  $_ENV['API_KEY'],
                    'X-RapidAPI-Host' =>  $_ENV['API_HOST']
                ],
            ]
        );

        $result =  $response->toArray();
        return $result['results'];
    }

    public function getRating($id)
    {
        $response = $this->client->request(
            'GET',
            'https://moviesdatabase.p.rapidapi.com/titles/' . $id . '/ratings',
            [
                'headers' => [
                    'X-RapidAPI-Key' =>  $_ENV['API_KEY'],
                    'X-RapidAPI-Host' =>  $_ENV['API_HOST']
                ],
            ]
        );

        $result =  $response->toArray();
        return $result['results'];
    }

    public function getListOfMovies($arrayId)
    {
        $response = $this->client->request(
            'GET',
            'https://moviesdatabase.p.rapidapi.com/titles/x/titles-by-ids',
            [
                'headers' => [
                    'X-RapidAPI-Key' =>  $_ENV['API_KEY'],
                    'X-RapidAPI-Host' =>  $_ENV['API_HOST']
                ],
                'query' => [
                    'idsList' => $arrayId
                ]
            ]
        );

        $result =  $response->toArray();
        return $result['results'];
    }

    public function search($keyword)
    {
        $response = $this->client->request(
            'GET',
            'https://moviesdatabase.p.rapidapi.com/titles/search/keyword/' . $keyword,
            [
                'headers' => [
                    'X-RapidAPI-Key' =>  $_ENV['API_KEY'],
                    'X-RapidAPI-Host' =>  $_ENV['API_HOST']
                ]
            ]
        );

        $result =  $response->toArray();
        return $result['results'];
    }
}

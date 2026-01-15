<?php

namespace App\Entity;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class apiDinosaurs
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getAllDinosaurs(): array {
        $response = $this->client->request('GET', 'https://dinoapi.brunosouzadev.com/api/dinosaurs');

        return $response->toArray();
    }

    public function getDinosaurName(string $nameDinosaur): array {
        $name = urlencode($nameDinosaur);

        $response = $this->client->request(
            'GET',
            'https://dinoapi.brunosouzadev.com/api/dinosaurs/' . $name
        );

        return $response->toArray();
    }
}

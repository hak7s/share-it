<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Psr\Http\Message\ResponseInterface;

class HomeController extends AbstractController
{
    public function homepage(ResponseInterface $response, Connection $connection)
    {
        $database = $connection->getDatabase();
        return $this->template($response, 'homepage.html.twig', [
            'database_name' => $database,
            'users' => ['Pierre', 'Paul', 'Jacques']
        ]);
    }
}

<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeController extends AbstractController
{
    public function homepage(ResponseInterface $response, Connection $connection, ServerRequestInterface $request)
    {
        $database = $connection->getDatabase();
        $files = $request->getUploadedFiles();
        var_dump($files);
        return $this->template($response, 'home.html.twig');
    }

    public function download(ResponseInterface $response, int $id)
    {
        $response->getBody()->write(sprintf('identifiant: %d', $id));
        return $response;
    }
}

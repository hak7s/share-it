<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\IBMDB2\Result;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeController extends AbstractController
{
    public function homepage(ResponseInterface $response, Connection $connection, ServerRequestInterface $request)
    {
        $files = $request->getUploadedFiles();
        if (!empty($files['fichier'])) {
            $newfile = $files['fichier'];
            if ($newfile->getError() === UPLOAD_ERR_OK) {
                $uploadFileName = $newfile->getClientFilename();
                $time = time();
                $connection->executeStatement('INSERT INTO fichier (nom, nom_original) VALUES (:nom, :nom_original)', [
                    'nom' => $time,
                    'nom_original' => $uploadFileName,
                ]);
                $newfile->moveTo("../upload/$time");
            }
        }
        return $this->template($response, 'home.html.twig');
    }

    private function success(int $id, Connection $connection) // cette fonction devrait ce trouver dans une classe dediée
    {
        $query = $connection->prepare('SELECT * FROM fichier WHERE id = :id');
        $query->bindValue('id', $id);
        $result = $query->execute();

        $fichier = $result->fetchAllAssociative();
        return $fichier;
    }

    public function download(ResponseInterface $response, Connection $connection, int $id)
    {
        $fichier = static::success($id, $connection);
        if (!empty($fichier)) {
            $file_name = $fichier[0]['nom_original'];
            $path = "../upload/$fichier[0]['nom']";
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));
            flush(); // Flush system output buffer
            readfile($path);
            die;
        } else {
            $response->getBody()->write(sprintf('fichier non-trouver'));
        }
        return $response;
    }
}

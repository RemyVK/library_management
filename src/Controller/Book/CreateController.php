<?php

namespace App\Controller\Book;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\DBAL\Connection;


class CreateController
{
    #[Route('/createnewbook', methods: ['POST'])]
    public function createThought(Connection $connection, Request $request): JsonResponse
    {
        // Read data from request body
        $data = $request->toArray();

        $newBook = $data['newBook'];
        $bookQuantity = $data['copies'];
        $authorIds = $data['authors'];


        $affectedRows = $connection->executeStatement(
            'INSERT INTO Books (name, quantity) VALUES (?, ?)',
            [$newBook, $bookQuantity]
        );

        $bookId = $connection->lastInsertId();

        $sqlQuery = "INSERT INTO BookAuthorMapping (book_id, author_id) VALUES ";
        $sqlParams = [];

        foreach($authorIds as $id) {
            $sqlQuery = $sqlQuery . "(?, ?), ";
            array_push($sqlParams, $bookId, $id);
        }

        $sqlQuery = substr($sqlQuery, 0, -2);

        $connection->executeStatement($sqlQuery, $sqlParams);
        
        if ($affectedRows > 0) {
            return new JsonResponse(
                ['message' => 'Book Added successfully'],
                Response::HTTP_CREATED // 201
            );
        }

        return new JsonResponse(
            ['error' => 'Insert failed unexpectedly'],
            Response::HTTP_INTERNAL_SERVER_ERROR // 500
        );
      
    }
}
<?php

namespace App\Controller\Book;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\DBAL\Connection;


class BorrowController
{
    #[Route('/borrowbook', methods: ['POST'])]
    public function BorrowBook(Connection $connection, Request $request): JsonResponse
    {
        // Read data from request body
        $data = $request->toArray();

        $bookId = $data['bookId'];
        $userId = $data['userId'];

        $affectedRows = $connection->executeStatement(
            'INSERT INTO Library_Transactions (user_id, book_id) VALUES (?, ?)',
            [$userId, $bookId]
        );

        $connection->executeStatement(
            'UPDATE Books set quantity -= 1 WHERE book_id = (?)',
            [$bookId]
        );
        
        if ($affectedRows > 0) {
            return new JsonResponse(
                ['message' => 'Book borrowed'],
                Response::HTTP_CREATED // 201
            );
        }

        return new JsonResponse(
            ['error' => 'Operation failed unexpectedly'],
            Response::HTTP_INTERNAL_SERVER_ERROR // 500
        );
      
    }
}
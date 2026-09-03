<?php

namespace App\Controller\Book;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\DBAL\Connection;


class ReturnController
{
    #[Route('/returnbook', methods: ['POST'])]
    public function ReturnBook(Connection $connection, Request $request): JsonResponse
    {
        // Read data from request body
        $data = $request->toArray();

        $bookId = $data['bookId'];
        $userId = $data['userId'];

        $affectedRows = $connection->executeStatement(
            'UPDATE Library_Transactions SET return_date = CURRENT_TIMESTAMP WHERE user_id = ? AND book_id = ?',
            [$userId, $bookId]
        );

        $connection->executeStatement(
            'UPDATE Books set quantity = quantity + 1 WHERE book_id = (?)',
            [$bookId]
        );
        
        if ($affectedRows > 0) {
            return new JsonResponse(
                ['message' => 'Book Retuned'],
                Response::HTTP_CREATED // 201
            );
        }

        return new JsonResponse(
            ['error' => 'Operation failed unexpectedly'],
            Response::HTTP_INTERNAL_SERVER_ERROR // 500
        );
      
    }
}
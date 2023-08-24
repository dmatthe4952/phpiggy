<?php

namespace App\Services;

use Framework\Database;

class TransactionService
{

    public function __construct(
        private Database $db
    ) {
    }

    public function create(array $data)
    {

        $formattedDate = "{$data['date']} 00:00:00";
        $this->db->query(
            "INSERT INTO transactions (description, amount, date, user_id) 
            VALUES(:description, :amount, :date, :user_id)",
            [
                'description' => $data['description'],
                'amount' => $data['amount'],
                'date' => $formattedDate,
                'user_id' => $_SESSION['user']
            ]
        );
    }

    public function getUserTransactions(int $length, int $offset): array
    {
        $searchTerm = addcslashes($_GET['s'] ?? '', '%_');
        $params = [
            'user_id' => $_SESSION['user'],
            'description' => "%{$searchTerm}%",
        ];

        $transactions = $this->db->query(
            "SELECT id, description, amount,
                DATE_FORMAT(date, '%Y-%m-%d') as 'date',
                user_id from transactions WHERE user_id = :user_id
                AND description LIKE :description
                LIMIT {$length} OFFSET {$offset}",
            $params
        )->findAll();

        $transactions = array_map(function (array $transaction) {
            $transaction['receipts'] = $this->db->query(
                "SELECT * from receipts
               WHERE transaction_id = :transaction_id",
                ['transaction_id' => $transaction['id']]
            )->findAll();
            return $transaction;
        }, $transactions);

        $transactionsCount = $this->db->query(
            "SELECT COUNT(*)
             FROM transactions
             WHERE user_id = :user_id
             AND description LIKE :description",
            $params
        )->count();

        return [$transactions, $transactionsCount];
    }

    public function getUserTransaction(string $id): array
    {
        $transaction = $this->db->query(
            "SELECT * FROM transactions WHERE user_id = :user_id AND id = :transaction_id",
            [
                'user_id' => $_SESSION['user'],
                'transaction_id' => (int) $id
            ]
        )->find();

        return $transaction;
    }

    public function edit(array $data)
    {
        $transaction = $_POST;
        $this->db->query(
            "UPDATE transactions SET 
            description = :description,
            amount = :amount,
            date = :date 
            WHERE id = :id",
            [
                'description' => $transaction['description'],
                'amount' => $transaction['amount'],
                'date' => $transaction['date'],
                'id' => (int) $data['transaction']
            ]
        );
    }

    public function delete(array $data)
    {
        $this->db->query(
            "DELETE FROM transactions where id = :id",
            ['id' => $data['transaction']]
        );
    }
}

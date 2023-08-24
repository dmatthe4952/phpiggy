<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Services\TransactionService;


class HomeController
{

    public function __construct(
        private TemplateEngine $view,
        private TransactionService $transactionService
    )
    {
    }

    public function home()
    {
        $pageNum = $_GET['p'] ?? 1;
        $pageNum = (int) $pageNum;
        $length = 3;
        $offset = ($pageNum - 1) * $length;
        $searchTerm = $_GET['s'] ?? null;

        [$transactions, $transactionsCount] = $this->transactionService->getUserTransactions(
            $length,
            $offset
        );


        $lastPage = ceil($transactionsCount / $length);
        $pages = $lastPage ? range(1, $lastPage) : [];

        $pageLinks = array_map(
            fn ($pageNum) => http_build_query([
                'p' => $pageNum,
                's' => $searchTerm
            ]),
            $pages
        );

        echo $this->view->render('index.php', [
            'transactions' => $transactions,
            'currentPage' => $pageNum,
            'previousPageQuery' =>  http_build_query([
                'p' => $pageNum - 1,
                's' => $searchTerm,
            ]),
            'nextPageQuery' =>  http_build_query([
                'p' => $pageNum + 1,
                's' => $searchTerm,
            ]),
            'lastPage' => $lastPage,
            'pageLinks' => $pageLinks,
            'searchTerm' => $searchTerm,

        ]); 
    }
}

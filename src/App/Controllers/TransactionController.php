<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Services\{ValidatorService, TransactionService};

class TransactionController
{
    public function __construct(
        private TemplateEngine $view,
        private TransactionService $transactionService,
        private ValidatorService $validatorService
    ) {
    }

    public function createView()
    {
        echo $this->view->render("transactions/create.php");
    }

    public function create()
    {
        $this->validatorService->validateTransaction($_POST);
        $this->transactionService->create($_POST);

        redirectTo('/');
    }

    public function editView(array $params)
    {
        $transaction = $this->transactionService->getUserTransaction($params['transaction']);
        echo $this->view->render("transactions/edit.php", [
            'transaction' => $transaction
        ]);
    }

    public function edit(array $params)
    {

        $this->validatorService->validateTransaction($_POST);
        $this->transactionService->edit($params);

        redirectTo('/');
    }

    public function delete(array $params)
    {
        $this->transactionService->delete($params);

        redirectTo('/');
    }
}

<?php

namespace App\Repositories\Transaction;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function create(array $data)
    {
        $transaction = Transaction::create($data);
        return new TransactionResource($transaction);
    }
}

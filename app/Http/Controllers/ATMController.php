<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositRequest;
use App\Http\Requests\WithdrawRequest;
use App\Http\Resources\TransactionResource;
use App\Repositories\Transaction\TransactionRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ATMController extends Controller
{
    protected $transactionRepo;
    protected $userRepo;

    public function __construct(
        TransactionRepositoryInterface $transactionRepo,
        UserRepositoryInterface $userRepo
    ) {
        $this->transactionRepo = $transactionRepo;
        $this->userRepo = $userRepo;
    }

    public function deposit(DepositRequest $request)
    {
        $user = Auth::user();
        $amount = $request->input('amount');

        // Begin database transaction
        DB::beginTransaction();

        try {
            // Update balance
            $user = $this->userRepo->updateBalance($user->id, $amount);

            // Create transaction record
            $this->transactionRepo->create([
                'user_id' => $user->id,
                'type' => 'deposit',
                'amount' => $amount,
                'balance' => $user->balance
            ]);

            // Commit the transaction
            DB::commit();

            return response()->json(['status' => 'success', 'balance' => $user->balance]);
        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();

            return response()->json(['status' => 'error', 'message' => 'An error occurred during the deposit.'], 500);
        }
    }

    public function withdraw(WithdrawRequest $request)
    {
        $user = Auth::user();
        $amount = $request->input('amount');

        // Cek apakah saldo mencukupi dan apakah kelipatan 50,000
        if ($amount % 50000 != 0 || $user->balance - $amount < 50000) {
            return response()->json(['status' => 'error', 'message' => 'Saldo tidak mencukupi atau bukan kelipatan 50,000']);
        }

        // Begin database transaction
        DB::beginTransaction();

        try {
            // Update balance
            $user = $this->userRepo->updateBalance($user->id, -$amount);

            // Create transaction record
            $this->transactionRepo->create([
                'user_id' => $user->id,
                'type' => 'withdrawal',
                'amount' => $amount,
                'balance' => $user->balance
            ]);

            DB::commit();

            return response()->json(['status' => 'success', 'balance' => $user->balance]);
        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function history()
    {
        try {
            $user = Auth::user();
            $transactions = TransactionResource::collection($user->transactions);

            return response()->json($transactions);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

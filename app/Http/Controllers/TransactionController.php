<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function addTransaction(Request $request)
    {
        try {
            $request->validate([
                'description' => 'required|string',
                'amount' => 'required|numeric',
                'type' => 'required|in:expense,income',
            ]);

            $transaction = Transaction::create([
                'description' => $request->description,
                'amount' => $request->amount,
                'type' => $request->type,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction added successfully',
                'transaction' => $transaction,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 500);
        }
    }

    public function getTransactions()
    {
        $user = Auth::user();

        $transactions = Transaction::where('user_id', $user->id)->get();

        $totalExpenses = $transactions->where('type', 'expense')->sum('amount');
        $totalIncomes = $transactions->where('type', 'income')->sum('amount');
        $totalProfit = $totalIncomes - $totalExpenses;

        return response()->json([
            'status' => 'success',
            'message' => 'Transactions retrieved successfully',
            'total_expenses' => $totalExpenses,
            'total_incomes' => $totalIncomes,
            'total_profit' => $totalProfit,
            'transactions' => $transactions,
        ]);
    }
}

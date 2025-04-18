<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Receivable;

class TransactionController extends Controller
{
    /**
 * @OA\Get(
 *     path="/api/transactions",
 *     summary="Get all transactions",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Successful response"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */
    public function getAllTransactions(Request $request)
    {
        $userId = Auth::id();

        $receivables = Receivable::where('user_id', $userId)->get()->map(function ($item) {
            $item->status_text = $this->getStatusText($item->status);
            return $item;
        });

        $transactions = Transaction::where('user_id', $userId)->get()->map(function ($item) {
            $item->status_text = $this->getStatusText($item->status);
            return $item;
        });

        return response()->json([
            'receivables' => $receivables,
            'transactions' => $transactions,
        ]);
    }

	/**
	 * @OA\Get(
	 *     path="/api/transactions/{id}",
	 *     tags={"Transactions"},
	 *     summary="Get a transaction or receivable by ID",
	 *     description="Returns the transaction or receivable details based on the provided ID",
	 *     security={{"bearerAuth":{}}},
	 *     @OA\Parameter(
	 *         name="id",
	 *         in="path",
	 *         required=true,
	 *         description="Transaction or receivable ID",
	 *         @OA\Schema(type="string")
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Successful response",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="id", type="string", example="123"),
	 *             @OA\Property(property="amount", type="number", example=100.50),
	 *             @OA\Property(property="status", type="string", example="completed")
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="Record not found"
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Unauthorized"
	 *     )
	 * )
	 */
    public function getById($id)
    {
        $transaction = Transaction::where('confirmation', $id)->first();
        if ($transaction) {
            $transaction->status_text = $this->getStatusText($transaction->status);
            return response()->json([
                'source' => 'transactions',
                'data' => $transaction
            ]);
        }

        $receivable = Receivable::where('ref_id', $id)->first();
        if ($receivable) {
            $receivable->status_text = $this->getStatusText($receivable->status);
            return response()->json([
                'source' => 'receivables',
                'data' => $receivable
            ]);
        }

        return response()->json(['message' => 'Record not found'], 404);
    }

	/**
	 * @OA\Post(
	 *     path="/api/transactions/{id}/cancel",
	 *     tags={"Transactions"},
	 *     summary="Cancel a transaction or receivable by ID",
	 *     description="Cancels a transaction or receivable using its unique ID",
	 *     security={{"bearerAuth":{}}},
	 *     @OA\Parameter(
	 *         name="id",
	 *         in="path",
	 *         required=true,
	 *         description="Transaction or receivable ID",
	 *         @OA\Schema(type="string")
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Cancelled successfully",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="status", type="string", example="cancelled"),
	 *             @OA\Property(property="message", type="string", example="Transaction cancelled successfully")
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="Record not found"
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Unauthorized"
	 *     )
	 * )
	 */

    public function cancelTransaction($id)
    {
        $transaction = Transaction::where('confirmation', $id)->first();
      if ($transaction) {
			$transaction->status = 10;
			$transaction->save();

			$data = $transaction->toArray();
			$data['status'] = 'cancelled';

			return response()->json([
				'source' => 'transactions',
				'message' => 'Transaction cancelled successfully',
				'data' => $data
			]);
		}

        $receivable = Receivable::where('ref_id', $id)->first();
       if ($receivable) {
			$receivable->status = 10;
			$receivable->save();

			$data = $receivable->toArray();
			$data['status'] = 'cancelled';

			return response()->json([
				'source' => 'receivables',
				'message' => 'Receivable cancelled successfully',
				'data' => $data
			]);
		}

        return response()->json(['message' => 'Record not found'], 404);
    }

    private function getStatusText($status)
    {
        return match ((int)$status) {
            0 => 'Returned',
            1 => 'Cleared',
            10 => 'Cancelled',
            101 => 'Pending',
            default => 'Unknown',
        };
    }
}

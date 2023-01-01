<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Model;

class AccountBalance extends Model
{
    // add fillable
    protected $fillable = [];

    // add guaded
    protected $guarded = ['id'];

    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Fungsi untuk menghitung saldo akun dari entri jurnal terkait
     */
    public static function updateAccountBalance()
    {
        $accounts = Account::all();

        foreach ($accounts as $account) {
            $accountId = $account->id;
            $accountBalance = AccountBalance::where('account_id', $accountId)
                ->first();

            $totalDebit = JournalEntry::with('journal')
                ->whereHas('journal', function ($query) {
                    $query->where('status', TransactionStatus::APPROVED->value);
                })
                ->where('account_id', $accountId)
                ->where('type', 'debit')
                ->sum('amount');

            $totalCredit = JournalEntry::with('journal')
                ->whereHas('journal', function ($query) {
                    $query->where('status', TransactionStatus::APPROVED->value);
                })
                ->where('account_id', $accountId)
                ->where('type', 'credit')
                ->sum('amount');

            $closingBalance = 0;
            $closingBalance = $totalCredit > 0
                ? $totalCredit - $totalDebit
                : $totalDebit;
            // Jika saldo akun belum ada, buat saldo baru
            if (! $accountBalance) {
                AccountBalance::create([
                    'account_id' => $accountId,
                    'fiscal_year' => date('Y') . '-' . date('Y', strtotime('+1 year')),
                    'balance_date' => now(),
                    'opening_balance' => 0,
                    'debit' => $totalDebit,
                    'credit' => $totalCredit,
                    'closing_balance' => $closingBalance,
                ]);
            } else {
                $accountBalance->debit = $totalDebit;
                $accountBalance->credit = $totalCredit;
                $accountBalance->opening_balance = 0;
                $accountBalance->closing_balance = $closingBalance;

                $accountBalance->save();
            }
        }
    }
}

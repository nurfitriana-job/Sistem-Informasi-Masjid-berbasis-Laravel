<?php

namespace App\Actions;

use App\Models\Account;
use App\Models\AccountBalance;
use App\Models\Journal;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManageTransaction
{
    private $data;

    /**
     * Create a new class instance.
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Store transaction in the journal and update the balances
     */
    public function store()
    {
        return DB::transaction(function () {
            // Create the journal record
            $journal = Journal::find($this->data['id']);

            // Get the associated account
            $getAccountPayment = $this->getAccount($this->data['account_id']);
            if (! $getAccountPayment) {
                throw new \Exception('Payment account not found');
            }
            $this->createJournalEntry(
                $journal,
                $getAccountPayment,
                'credit',
                $this->data['amount'],
                $this->data['description']
            );

            if ($this->data['type'] === 'expense') {
                $getAccount = $this->getAccount($this->data['payment_account_id']);
                if (! $getAccount) {
                    throw new \Exception('Payment account not found');
                }
                $this->createJournalEntry(
                    $journal,
                    $getAccount,
                    'debit',
                    $this->data['amount'],
                    'Kas/Bank'
                );
            }

            $this->updateJournalTotals($journal);

            return $journal;
        });
    }

    public function update(Journal $journal)
    {
        return DB::transaction(function () use ($journal) {
            // Hapus jurnal lama
            $journal = $journal;
            if ($journal) {
                $this->deleteOldJournalEntries($journal);
            }

            return $this->store();
        });
    }

    public function delete(Journal $journal)
    {
        return DB::transaction(function () use ($journal) {
            if ($journal) {
                $this->deleteOldJournalEntries($journal);
                $journal->delete();
            }

            return true;
        });
    }

    /**
     * Create a journal entry
     */
    private function createJournal()
    {
        return Journal::create([
            'journal_number' => Journal::generateJournalNumber(),
            'transaction_date' => $this->data['expense_date'],
            'description' => $this->data['description'],
            'created_by' => Auth::user()?->id,
        ]);
    }

    /**
     * Get account by id
     */
    private function getAccount($accountId)
    {
        return Account::where('id', $accountId)
            ->first();
    }

    /**
     * Create a journal entry for the transaction
     */
    private function createJournalEntry($journal, $account, $type, $amount, $description)
    {
        JournalEntry::create([
            'journal_id' => $journal->id,
            'account_id' => $account->id,
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
        ]);
    }

    /**
     * Update account balance based on the journal entry type
     */
    private function updateAccountBalance()
    {
        AccountBalance::updateAccountBalance();
    }

    /**
     * Update the total debit and credit for the journal
     */
    private function updateJournalTotals($journal)
    {
        $totalDebit = JournalEntry::where('journal_id', $journal->id)
            ->where('type', 'debit')
            ->sum('amount');

        $totalCredit = JournalEntry::where('journal_id', $journal->id)
            ->where('type', 'credit')
            ->sum('amount');

        $journal->update([
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
        ]);

        $this->updateAccountBalance();
    }

    /**
     * Delete old journal entries related to the journal
     */
    private function deleteOldJournalEntries($journal)
    {
        $journalEntries = JournalEntry::where('journal_id', $journal->id)->get();
        foreach ($journalEntries as $entry) {
            $entry->delete();
        }
    }
}

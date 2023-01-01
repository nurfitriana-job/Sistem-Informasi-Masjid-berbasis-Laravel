<?php

namespace App\Actions\Accounts;

use App\Models\Account;
use App\Models\AccountBalance;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class InitializeAccount
{
    public $file = null;

    /**
     * Initialize the account generation process.
     */
    public function __construct($file = null)
    {
        if ($file) {
            $this->file = $file;
        } else {
            $this->file = public_path('data/akun.xlsx');
        }
    }

    /**
     * Generate accounts from the Excel file.
     */
    public function generate(): void
    {
        $accounts = $this->readExcel();

        if ($accounts->isEmpty()) {
            $this->logError('No accounts found in the Excel file.');

            return;
        }

        Account::query()->delete();

        foreach ($accounts as $account) {
            $this->processAccount($account);
        }
    }

    /**
     * Process each account: create categories and accounts.
     */
    private function processAccount(array $account): void
    {
        $this->createAccount($account);
    }

    /**
     * Create a new account.
     */
    private function createAccount(array $account): void
    {
        $account = Account::create([
            'name' => $account[1],
            'code' => $account[2],
            'type' => $account[3],
        ]);

        AccountBalance::create([
            'account_id' => $account->id,
            'fiscal_year' => date('Y') . '-' . date('Y', strtotime('+1 year')),
            'balance_date' => now(),
            'opening_balance' => 0,
            'debit' => 0,
            'credit' => 0,
            'closing_balance' => 0,
        ]);
    }

    /**
     * Read the Excel file from the public directory and process the data.
     */
    public function readExcel(): \Illuminate\Support\Collection
    {
        $filePath = $this->file ?? public_path('data/akun.xlsx');

        if (! file_exists($filePath)) {
            $this->logError("File not found: $filePath");

            return collect();
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        return collect($this->getSheetData($sheet));
    }

    /**
     * Extract data from the sheet rows and return it as an array.
     */
    private function getSheetData($sheet): array
    {
        $data = [];

        foreach ($sheet->getRowIterator() as $row) {
            if ($row->getRowIndex() === 1) {
                continue;
            }
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getFormattedValue();
            }
            $data[] = $rowData;
        }

        return $data;
    }

    /**
     * Log an error with the company_id context.
     */
    private function logError(string $message): void
    {
        Log::error($message);
    }
}

<?php

namespace App\Actions\Accounts;

use App\Models\Account;
use App\Models\Tax;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class InitTax
{
    public string $company_id;

    /**
     * Create a new class instance.
     */
    public function __construct(string $company_id)
    {
        $this->company_id = $company_id;
    }

    /**
     * Generate taxes from the Excel file.
     */
    public function generate(): void
    {
        $taxes = $this->readExcel();

        if ($taxes->isEmpty()) {
            $this->logError('No taxes found in the Excel file.');

            return;
        }

        foreach ($taxes as $tax) {
            $this->processTax($tax);
        }
    }

    /**
     * Process each tax: create accounts and taxes.
     */
    private function processTax(array $tax): void
    {
        $salesAccount = Account::where('company_id', $this->company_id)
            ->where('code', $tax[3])
            ->select('id')
            ->value('id') ?? null;
        $purchaseAccount = Account::where('company_id', $this->company_id)
            ->where('code', $tax[4])
            ->select('id')
            ->value('id') ?? null;

        $this->createTax($tax, $salesAccount, $purchaseAccount);
    }

    /**
     * Create a tax if it doesn't exist.
     */
    private function createTax(array $tax, ?string $salesAccount, ?string $purchaseAccount): Tax
    {
        return Tax::firstOrCreate([
            'company_id' => $this->company_id,
            'name' => $tax[0],
            'rate' => $tax[1],
            'type' => $tax[2],
            'sales_account_id' => $salesAccount,
            'purchase_account_id' => $purchaseAccount,
            'is_custom' => (int) $tax[5] == 1 ? true : false,
            'is_sales' => (int) $tax[6] == 1 ? true : false,
            'is_buy' => (int) $tax[7] == 1 ? true : false,
        ]);
    }

    /**
     * Read the Excel file and return the data.
     */
    private function readExcel(): \Illuminate\Support\Collection
    {
        $filePath = public_path('data/taxes.xlsx');

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
        Log::error($message, [
            'company_id' => $this->company_id,
        ]);
    }
}

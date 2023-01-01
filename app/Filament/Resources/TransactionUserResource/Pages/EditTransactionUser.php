<?php

namespace App\Filament\Resources\TransactionUserResource\Pages;

use App\Enums\AccountType;
use App\Enums\TransactionStatus;
use App\Filament\Resources\TransactionUserResource;
use App\Models\Category;
use Filament\Resources\Pages\EditRecord;

class EditTransactionUser extends EditRecord
{
    protected static string $resource = TransactionUserResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    // customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['category_id'])) {
            $category = Category::find($data['category_id']);
            if ($category) {
                $data['category_name'] = $category->name;
                $data['account_name'] = $category->account->name ?? null;
            }
        }

        $data['amount'] = $data['total_credit'];

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($this->data['attachments'])) {
            $data['status'] = TransactionStatus::PENDING->value;
        } else {
            $data['status'] = TransactionStatus::REVIEW->value;
        }

        $category = $data['category_id'];
        if ($category) {
            $category = Category::find($category);
            if ($category) {
                $account = $category->account;
                if ($account) {
                    switch ($account->type->value) {
                        case AccountType::Asset->value:
                            $data['total_debit'] = $data['amount'];
                            $data['total_credit'] = 0;

                            break;
                        case AccountType::Liability->value:
                            $data['total_debit'] = 0;
                            $data['total_credit'] = $data['amount'];

                            break;
                        case AccountType::Equity->value:
                            $data['total_debit'] = 0;
                            $data['total_credit'] = $data['amount'];

                            break;
                        case AccountType::Revenue->value:
                            $data['total_debit'] = 0;
                            $data['total_credit'] = $data['amount'];

                            break;
                        case AccountType::Expense->value:
                            $data['total_debit'] = $data['amount'];
                            $data['total_credit'] = 0;

                            break;
                    }
                }
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $data = $this->record;
        $data->entries()->update([
            'account_id' => $data->category->account_id,
            'type' => $data->total_debit > 0 ? 'debit' : 'credit',
            'amount' => $data->total_debit > 0 ? $data->total_debit : $data->total_credit,
            'description' => $data->description,
        ]);
    }
}

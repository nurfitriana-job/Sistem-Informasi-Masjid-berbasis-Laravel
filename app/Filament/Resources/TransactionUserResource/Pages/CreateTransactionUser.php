<?php

namespace App\Filament\Resources\TransactionUserResource\Pages;

use App\Actions\ManageTransaction;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Filament\Resources\TransactionUserResource;
use App\Helpers\Format;
use App\Models\Category;
use App\Models\Journal;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTransactionUser extends CreateRecord
{
    protected static string $resource = TransactionUserResource::class;

    protected static bool $canCreateAnother = true;

    // customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $auth = User::find(Auth::id());
        $data['user_id'] = $auth->hasRole('admin') ? $data['user_id'] : $auth->id;
        $data['created_by'] = Auth::id();
        $data['transaction_date'] = $auth->hasRole('admin') ? $data['transaction_date'] : now();
        $data['journal_number'] = Journal::generateJournalNumber();
        $data['is_user_transaction'] = true;
        $data['type'] = TransactionType::INCOME->value;

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
                $data['account_id'] = $account->id;
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $data = $this->record;
        $data = $data->toArray();
        $data['amount'] = Format::removeSeparator($this->data['amount'], 2, '.', '');
        $data['type'] = TransactionType::INCOME->value;

        $storeModal = new ManageTransaction($data);
        $storeModal->store();
    }
}

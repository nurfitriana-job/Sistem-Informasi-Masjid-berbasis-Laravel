<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Actions\ManageTransaction;
use App\Enums\TransactionStatus;
use App\Filament\Resources\TransactionResource;
use App\Helpers\Format;
use App\Models\Journal;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected static bool $canCreateAnother = true;

    // customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();
        $data['journal_number'] = Journal::generateJournalNumber();
        $data['status'] = TransactionStatus::APPROVED;
        $data['is_user_transaction'] = false;

        return $data;
    }

    protected function afterCreate(): void
    {
        $data = $this->record;
        $data = $data->toArray();
        $data['amount'] = Format::removeSeparator($this->data['amount'], 2, '.', '');

        $storeModal = new ManageTransaction($data);
        $storeModal->store();
    }
}

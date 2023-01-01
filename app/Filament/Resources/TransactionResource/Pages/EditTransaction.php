<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Actions\ManageTransaction;
use App\Filament\Resources\TransactionResource;
use App\Helpers\Format;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

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
        $data['amount'] = $data['type'] === 'income' ? $data['total_credit'] : $data['total_debit'];

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        $this->data['amount'] = Format::removeSeparator($data['amount'], 2, '.', '');

        $update = new ManageTransaction($this->data);
        $update->update($record);

        return $record;
    }
}

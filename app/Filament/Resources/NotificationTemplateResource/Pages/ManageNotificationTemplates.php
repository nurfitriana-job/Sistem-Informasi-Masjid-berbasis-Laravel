<?php

namespace App\Filament\Resources\NotificationTemplateResource\Pages;

use App\Filament\Imports\NotificationTemplateImporter;
use App\Filament\Resources\NotificationTemplateResource;
use App\Settings\GeneralSetting;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ManageRecords;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;

class ManageNotificationTemplates extends ManageRecords
{
    protected static string $resource = NotificationTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make()
            //     ->mutateFormDataUsing(function (array $data): array {
            //         $data['subject'] = str_replace(
            //             ['{masjid}'],
            //             [
            //                 app(GeneralSetting::class)->site_name,
            //             ],
            //             $data['subject']
            //         );
            //         return $data;
            //     }),
            ActionGroup::make([
                ImportAction::make('importNotificationTemplates')
                    ->label('Import')
                    ->importer(NotificationTemplateImporter::class),
                ExportAction::make(),
            ])
                ->button(),
        ];
    }
}

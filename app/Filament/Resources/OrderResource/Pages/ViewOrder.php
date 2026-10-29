<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            OrderResource::setShippingAction(Actions\Action::class),
            OrderResource::sendInvoiceAction(Actions\Action::class),
            Actions\EditAction::make()->label('Update Status'),
            Actions\Action::make('cetak_nota')
                ->label('🖨️ Cetak Nota')
                ->color('success')
                ->url(fn() => route('admin.nota', $this->record->id))
                ->openUrlInNewTab(),
        ];
    }
}

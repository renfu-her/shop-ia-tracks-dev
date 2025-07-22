<?php

namespace App\Filament\Resources\CouponCodeResource\Pages;

use App\Filament\Resources\CouponCodeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCouponCode extends EditRecord
{
    protected static string $resource = CouponCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

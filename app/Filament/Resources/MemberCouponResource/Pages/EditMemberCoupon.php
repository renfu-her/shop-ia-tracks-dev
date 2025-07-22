<?php

namespace App\Filament\Resources\MemberCouponResource\Pages;

use App\Filament\Resources\MemberCouponResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMemberCoupon extends EditRecord
{
    protected static string $resource = MemberCouponResource::class;

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

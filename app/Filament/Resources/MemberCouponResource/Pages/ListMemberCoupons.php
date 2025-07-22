<?php

namespace App\Filament\Resources\MemberCouponResource\Pages;

use App\Filament\Resources\MemberCouponResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMemberCoupons extends ListRecords
{
    protected static string $resource = MemberCouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

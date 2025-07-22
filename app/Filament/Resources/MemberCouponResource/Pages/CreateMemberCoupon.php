<?php

namespace App\Filament\Resources\MemberCouponResource\Pages;

use App\Filament\Resources\MemberCouponResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMemberCoupon extends CreateRecord
{
    protected static string $resource = MemberCouponResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

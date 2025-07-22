<?php

namespace App\Filament\Resources\CouponCodeResource\Pages;

use App\Filament\Resources\CouponCodeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCouponCode extends CreateRecord
{
    protected static string $resource = CouponCodeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

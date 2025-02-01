<?php

namespace Siteman\Cms\Resources\UserResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Siteman\Cms\Resources\UserResource;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}

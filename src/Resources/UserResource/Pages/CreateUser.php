<?php

namespace Siteman\Cms\Resources\UserResource\Pages;

use Siteman\Cms\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}

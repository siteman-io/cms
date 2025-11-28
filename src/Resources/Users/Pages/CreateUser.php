<?php

namespace Siteman\Cms\Resources\Users\Pages;

use Filament\Resources\Pages\CreateRecord;
use Siteman\Cms\Resources\Users\UserResource;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}

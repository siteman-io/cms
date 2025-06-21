<?php

namespace Siteman\Cms\Resources\Roles\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('siteman::role.table.columns.name')),
                TextColumn::make('permissions_count')
                    ->badge()
                    ->label(__('siteman::role.table.columns.permissions_count'))
                    ->counts('permissions')
                    ->alignCenter(),
                TextColumn::make('users_count')
                    ->badge()
                    ->label(__('siteman::role.table.columns.users_count'))
                    ->counts('users')
                    ->alignCenter(),
                TextColumn::make('updated_at')
                    ->label(__('siteman::role.table.columns.updated_at'))
                    ->alignRight()
                    ->dateTimeTooltip()
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->recordActions(ActionGroup::make([
                EditAction::make(),
                DeleteAction::make()->color('gray'),
            ]))
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

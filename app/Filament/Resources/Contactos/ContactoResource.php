<?php

namespace App\Filament\Resources\Contactos;

use App\Filament\Resources\Contactos\Pages\EditContacto;
use App\Filament\Resources\Contactos\Pages\ListContactos;
use App\Filament\Resources\Contactos\Schemas\ContactoForm;
use App\Filament\Resources\Contactos\Tables\ContactosTable;
use App\Models\Contacto;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ContactoResource extends Resource
{
    protected static ?string $model = Contacto::class;

    protected static ?string $slug = 'cotizaciones-y-contacto';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $navigationLabel = 'Cotizaciones y Contacto';

    protected static ?string $modelLabel = 'Cotización';

    protected static ?string $pluralModelLabel = 'Cotizaciones y Contacto';

    protected static string|UnitEnum|null $navigationGroup = 'CRM & Atención';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        try {
            $count = static::getModel()::where(function ($q) {
                $q->where('estado', 'nuevo')->orWhere('estado', 'pendiente');
            })->count();
            return $count > 0 ? (string) $count : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return ContactoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactosTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactos::route('/'),
            'edit' => EditContacto::route('/{record}/edit'),
        ];
    }
}


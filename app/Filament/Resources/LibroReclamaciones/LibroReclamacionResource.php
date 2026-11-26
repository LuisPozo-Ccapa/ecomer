<?php

namespace App\Filament\Resources\LibroReclamaciones;

use App\Filament\Resources\LibroReclamaciones\Pages\EditLibroReclamacion;
use App\Filament\Resources\LibroReclamaciones\Pages\ListLibroReclamaciones;
use App\Filament\Resources\LibroReclamaciones\Schemas\LibroReclamacionForm;
use App\Filament\Resources\LibroReclamaciones\Tables\LibroReclamacionesTable;
use App\Models\LibroReclamacion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class LibroReclamacionResource extends Resource
{
    protected static ?string $model = LibroReclamacion::class;

    protected static ?string $slug = 'libro-de-reclamaciones';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = 'Libro de Reclamaciones';

    protected static ?string $modelLabel = 'Hoja de Reclamación';

    protected static ?string $pluralModelLabel = 'Libro de Reclamaciones';

    protected static string|UnitEnum|null $navigationGroup = 'Mis sitios';

    protected static ?int $navigationSort = 4;

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canEdit(Model $record): bool
    {
        return true;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return LibroReclamacionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LibroReclamacionesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLibroReclamaciones::route('/'),
            'edit' => EditLibroReclamacion::route('/{record}/edit'),
        ];
    }
}

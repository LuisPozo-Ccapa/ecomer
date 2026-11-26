<?php

namespace App\Filament\Resources\Marcas;

use App\Filament\Resources\Marcas\Pages\CreateMarca;
use App\Filament\Resources\Marcas\Pages\EditMarca;
use App\Filament\Resources\Marcas\Pages\ListMarcas;
use App\Filament\Resources\Marcas\Schemas\MarcaForm;
use App\Filament\Resources\Marcas\Tables\MarcasTable;
use App\Models\Marca;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MarcaResource extends Resource
{
    protected static ?string $model = Marca::class;

    protected static ?string $recordTitleAttribute = 'titulo';

    protected static int $globalSearchResultsLimit = 5;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Marcas';

    protected static string|UnitEnum|null $navigationGroup = 'Catálogo & Almacén';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return MarcaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MarcasTable::configure($table);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['titulo', 'slug'];
    }

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    {
        /** @var Marca $record */
        return $record->titulo;
    }

    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getGlobalSearchEloquentQuery()
            ->withoutGlobalScopes([\Illuminate\Database\Eloquent\SoftDeletingScope::class])
            ->withCount('productos');
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        /** @var Marca $record */
        return array_filter([
            'Slug' => $record->slug,
            'Productos' => $record->productos_count ? "{$record->productos_count} productos" : 'Sin productos',
            'Estado' => $record->activa ? 'Activa' : 'Inactiva',
        ]);
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
            'index' => ListMarcas::route('/'),
            'create' => CreateMarca::route('/create'),
            'edit' => EditMarca::route('/{record}/edit'),
        ];
    }
}

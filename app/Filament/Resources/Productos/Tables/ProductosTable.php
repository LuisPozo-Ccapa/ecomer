<?php

namespace App\Filament\Resources\Productos\Tables;

use App\Models\Producto;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagen')
                    ->label('Foto')
                    ->disk('public')
                    ->square()
                    ->size(40)
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover shadow-sm', 'loading' => 'lazy'])
                    ->toggleable(),
                TextColumn::make('codigo_referencia')
                    ->label('Cód. Ref.')
                    ->formatStateUsing(fn ($state, Producto $record) => $state ?: 'PROD-'.str_pad((string) $record->id, 5, '0', STR_PAD_LEFT))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->fontFamily('mono')
                    ->toggleable(),
                TextColumn::make('nombre')
                    ->label('Producto')
                    ->weight('bold')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Producto $record): ?string => $record->marca?->nombre ? "Marca: {$record->marca->nombre}" : null),
                TextColumn::make('almacenes_con_stock')
                    ->label('Almacenes')
                    ->state(function (Producto $record): array {
                        if ($record->productoTiendas->isEmpty()) {
                            return ['Sin almacén'];
                        }

                        return $record->productoTiendas->map(function ($pt) {
                            $nombre = $pt->tienda?->nombre ?? 'Almacén';
                            $stockStr = $pt->stock !== null ? "{$pt->stock} u." : 'Ilimitado';

                            return "{$nombre} ({$stockStr})";
                        })->toArray();
                    })
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('categoria_id')
                    ->label('Categoría')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (Producto $record): ?string => $record->subcategoria?->nombre ?? $record->categoria?->nombre),
                TextColumn::make('precio')
                    ->label('Precio')
                    ->weight('bold')
                    ->formatStateUsing(function (Producto $record): string {
                        $simbolo = $record->tiendas->first()?->moneda?->simbolo ?? 'S/';

                        return $simbolo.' '.number_format((float) $record->precio, 2);
                    }),
                TextColumn::make('stock')
                    ->label('Stock Total')
                    ->state(function (Producto $record): string {
                        if ($record->productoTiendas->isNotEmpty()) {
                            $tieneIlimitado = $record->productoTiendas->contains(fn ($pt) => is_null($pt->stock));
                            if ($tieneIlimitado) {
                                return 'Ilimitado';
                            }

                            return (string) $record->productoTiendas->sum('stock');
                        }

                        return $record->stock !== null ? (string) $record->stock : 'Ilimitado';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state === '0' || $state === 'Agotado' => 'danger',
                        is_numeric($state) && (int) $state <= 5 => 'warning',
                        default => 'success',
                    })
                    ->sortable(),
                ToggleColumn::make('activo')
                    ->label('En Tienda')
                    ->onColor('success')
                    ->offColor('gray'),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'categoria',
                'subcategoria',
                'marca',
                'tiendas.moneda',
                'productoTiendas.tienda',
            ])->withCount('variantes'))
            ->filters([
                SelectFilter::make('tiendas')
                    ->label('Almacén')
                    ->relationship('tiendas', 'nombre')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('categoria_id')
                    ->label('Categoría')
                    ->relationship('categoria', 'nombre')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('subcategoria_id')
                    ->label('Subcategoría')
                    ->relationship('subcategoria', 'nombre')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('activo')
                    ->label('Estado')
                    ->placeholder('Todos')
                    ->trueLabel('Activos')
                    ->falseLabel('Inactivos'),
                TernaryFilter::make('destacado')
                    ->label('Destacados')
                    ->trueLabel('Solo destacados')
                    ->falseLabel('No destacados'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('ver_web')
                    ->label('Ver Web')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->color('gray')
                    ->url(fn (Producto $record) => url('/producto/' . ($record->slug ?: $record->id)), shouldOpenInNewTab: true),
                EditAction::make(),
                DeleteAction::make()
                    ->label('Borrar'),
                RestoreAction::make()
                    ->label('Restaurar'),
                ForceDeleteAction::make()
                    ->label('Purgar Definitivo'),
            ]);
    }
}

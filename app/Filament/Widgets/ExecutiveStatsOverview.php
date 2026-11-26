<?php

namespace App\Filament\Widgets;

use App\Models\Categoria;
use App\Models\Contacto;
use App\Models\LibroReclamacion;
use App\Models\Producto;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExecutiveStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        try {
            $totalProductos = Producto::count();
            $totalCategorias = Categoria::count();

            $stockCritico = Producto::where(function ($q) {
                $q->where('stock', '<=', 5)->orWhereNull('stock');
            })->count();

            $contactosPendientes = Contacto::where(function ($q) {
                $q->where('estado', 'nuevo')->orWhere('estado', 'pendiente');
            })->count();

            $reclamosPendientes = LibroReclamacion::where('estado', 'pendiente')->count();
        } catch (\Throwable $e) {
            $totalProductos = 0;
            $totalCategorias = 0;
            $stockCritico = 0;
            $contactosPendientes = 0;
            $reclamosPendientes = 0;
        }

        return [
            Stat::make('Catálogo Activo', (string) $totalProductos)
                ->description("En {$totalCategorias} categorías registradas")
                ->descriptionIcon(Heroicon::OutlinedShoppingBag)
                ->chart([8, 12, 16, 22, 28, 35, max($totalProductos, 10)])
                ->color('primary'),

            Stat::make('Alertas de Inventario', (string) $stockCritico)
                ->description($stockCritico > 0 ? "{$stockCritico} productos con stock crítico" : 'Stock en nivel saludable')
                ->descriptionIcon($stockCritico > 0 ? Heroicon::OutlinedExclamationTriangle : Heroicon::OutlinedCheckCircle)
                ->color($stockCritico > 0 ? 'warning' : 'success'),

            Stat::make('Cotizaciones & Contacto', (string) $contactosPendientes)
                ->description($contactosPendientes > 0 ? "{$contactosPendientes} pendientes de atención" : 'Todas las solicitudes atendidas')
                ->descriptionIcon(Heroicon::OutlinedChatBubbleLeftRight)
                ->chart([2, 5, 4, 7, 6, 8, max($contactosPendientes, 3)])
                ->color('primary'),

            Stat::make('Libro de Reclamaciones', (string) $reclamosPendientes)
                ->description($reclamosPendientes > 0 ? 'Hojas pendientes de respuesta' : 'Al día, sin reclamos abiertos')
                ->descriptionIcon($reclamosPendientes > 0 ? Heroicon::OutlinedExclamationCircle : Heroicon::OutlinedShieldCheck)
                ->color($reclamosPendientes > 0 ? 'danger' : 'success'),
        ];
    }
}

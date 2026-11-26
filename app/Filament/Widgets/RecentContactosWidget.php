<?php

namespace App\Filament\Widgets;

use App\Models\Contacto;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentContactosWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Últimas Solicitudes y Cotizaciones Recibidas';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Contacto::query()->latest()->limit(5)
            )
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('nombre')
                    ->label('Cliente')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->copyable(),
                TextColumn::make('empresa')
                    ->label('Empresa')
                    ->placeholder('Particular')
                    ->limit(25),
                TextColumn::make('mensaje')
                    ->label('Mensaje / Solicitud')
                    ->limit(45),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'nuevo' => 'primary',
                        'pendiente' => 'warning',
                        'atendido' => 'success',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon(Heroicon::OutlinedChatBubbleLeftEllipsis)
                    ->color('success')
                    ->url(function (Contacto $record): ?string {
                        if (! $record->telefono) {
                            return null;
                        }
                        $clean = preg_replace('/[^0-9]/', '', $record->telefono);
                        if (! str_starts_with($clean, '51') && strlen($clean) === 9) {
                            $clean = '51' . $clean;
                        }
                        $msg = urlencode("Hola {$record->nombre}, te saludamos de " . request()->getHost() . " respecto a tu consulta.");
                        return "https://wa.me/{$clean}?text={$msg}";
                    }, shouldOpenInNewTab: true),
            ])
            ->paginated(false);
    }
}

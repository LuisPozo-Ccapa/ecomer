<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickServerActionsWidget extends Widget
{
    protected static ?int $sort = 2;

    protected string $view = 'filament.widgets.quick-server-actions';

    protected int | string | array $columnSpan = 'full';
}

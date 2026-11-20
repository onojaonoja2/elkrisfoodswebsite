<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Elkris Content Studio';

    protected static ?string $navigationLabel = 'Overview';

    public function getColumns(): int|array
    {
        return 2;
    }
}

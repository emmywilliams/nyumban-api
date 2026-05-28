<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Property;
use App\Models\User;
use App\Models\Booking;
use App\Models\Role as Roles;


class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Properties', Property::count())
                ->description('All listings')
                ->icon('heroicon-o-building-office')
                ->color('primary'),

            Stat::make('Active Properties', Property::where('status', 'active')->count())
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Pending Verification', Property::where('status', 'under_verification')->count())
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Users', User::count())
                ->icon('heroicon-o-users')
                ->color('info'),

            Stat::make('Bookings', Booking::count())
                ->icon('heroicon-o-calendar')
                ->color('danger'),

            Stat::make('Roles', Roles::count())
                ->icon('heroicon-o-shield-check')
                ->color('secondary'),
        ];
    }
}

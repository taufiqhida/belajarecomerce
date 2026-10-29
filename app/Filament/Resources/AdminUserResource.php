<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminUserResource\Pages;
use App\Models\AdminUser;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class AdminUserResource extends Resource
{
    protected static ?string $model = AdminUser::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Manajemen Admin';
    protected static ?string $modelLabel = 'Admin';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Akun')->schema([
                TextInput::make('name')->label('Nama Lengkap')->required(),
                TextInput::make('email')->label('Email')->email()->required()->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),
                Select::make('role')
                    ->label('Role')
                    ->options(['super_admin' => '👑 Super Admin', 'admin' => '🛡️ Admin'])
                    ->default('admin')
                    ->live()
                    ->required(),
                Toggle::make('is_active')->label('Aktif')->default(true),
            ])->columns(2),
            Section::make('Izin Akses (hanya berlaku untuk role Admin)')
                ->description('Super Admin otomatis memiliki semua akses. Centang izin untuk Admin biasa.')
                ->schema([
                    CheckboxList::make('permissions')
                        ->label('Permissions')
                        ->options([
                            'manage_products' => '🛍️ Kelola Produk & Kategori',
                            'manage_orders' => '📦 Kelola Pesanan',
                            'manage_payments' => '💳 Kelola Metode Pembayaran',
                            'manage_shipping' => '🚚 Kelola Zona Ongkir',
                            'manage_discounts' => '🏷️ Kelola Kode Diskon',
                            'manage_testimonials' => '⭐ Moderasi Testimoni',
                            'manage_flash_sales' => '⚡ Kelola Flash Sale',
                            'manage_articles' => '📝 Kelola Artikel/Blog',
                            'manage_admins' => '👥 Kelola Admin (hati-hati)',
                            'manage_settings' => '⚙️ Pengaturan Toko',
                        ])
                        ->columns(2),
                ])
                ->visible(fn($get) => $get('role') === 'admin'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('email')->label('Email')->searchable()->copyable(),
                TextColumn::make('role')->label('Role')
                    ->badge()
                    ->color(fn($state) => $state === 'super_admin' ? 'warning' : 'info')
                    ->formatStateUsing(fn($state) => $state === 'super_admin' ? '👑 Super Admin' : '🛡️ Admin'),
                ToggleColumn::make('is_active')->label('Aktif'),
                TextColumn::make('last_login_at')->label('Login Terakhir')->since()->sortable(),
                TextColumn::make('created_at')->label('Dibuat')->since()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => !$record->isSuperAdmin() || AdminUser::where('role', 'super_admin')->count() > 1),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdminUsers::route('/'),
            'create' => Pages\CreateAdminUser::route('/create'),
            'edit' => Pages\EditAdminUser::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        $admin = auth('admin')->user();
        return $admin && $admin->hasPermission('manage_admins');
    }
}

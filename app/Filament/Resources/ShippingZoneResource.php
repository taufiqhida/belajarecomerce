<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingZoneResource\Pages;
use App\Models\ShippingZone;
use App\Support\Wilayah;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ShippingZoneResource extends Resource
{
    protected static ?string $model = ShippingZone::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Zona Ongkir';
    protected static ?string $modelLabel = 'Zona Ongkir';
    protected static ?string $pluralModelLabel = 'Zona Ongkir';
    protected static ?string $navigationGroup = 'Toko';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->description('Pilih kota/kabupaten dari data wilayah Indonesia dan isi tarif flat-nya. Pembeli di kota itu otomatis dikenai tarif ini. Kota yang belum ada di sini: pembeli diminta menunggu, lalu Anda menentukan ongkir per pesanan dan mengirim invoice. "Ambil di tempat" selalu gratis.')
                ->schema([
                    Select::make('province_code')->label('Provinsi')
                        ->options(fn() => Wilayah::options('provinces'))
                        ->searchable()->live()->required()->dehydrated(false)
                        ->afterStateHydrated(function (Select $component, Get $get) {
                            // saat edit: provinsi diturunkan dari kode kabupaten
                            if (!$component->getState() && $get('regency_code')) {
                                $component->state(substr($get('regency_code'), 0, 2));
                            }
                        })
                        ->afterStateUpdated(function (Set $set) {
                            $set('regency_code', null);
                            $set('district_code', null);
                        }),
                    Select::make('regency_code')->label('Kabupaten / Kota')
                        ->options(fn(Get $get) => Wilayah::options('regencies', $get('province_code')))
                        ->searchable()->live()->required()
                        ->afterStateUpdated(fn(Set $set) => $set('district_code', null))
                        ->rules([
                            fn(Get $get, ?ShippingZone $record) => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                $exists = ShippingZone::where('regency_code', $value)
                                    ->where('district_code', $get('district_code') ?: null)
                                    ->when($record, fn($q) => $q->whereKeyNot($record->id))
                                    ->exists();
                                if ($exists) {
                                    $fail('Zona untuk wilayah ini sudah ada. Edit zona yang sudah ada.');
                                }
                            },
                        ]),
                    Select::make('district_code')->label('Kecamatan (opsional)')
                        ->options(fn(Get $get) => Wilayah::options('districts', $get('regency_code')))
                        ->searchable()->placeholder('Semua kecamatan (satu tarif untuk seluruh kota)')
                        ->helperText('Isi hanya kalau satu kecamatan punya tarif sendiri yang berbeda dari tarif kotanya.'),
                    TextInput::make('fee')->label('Ongkir (flat)')->numeric()->prefix('Rp')->default(0)->required(),
                    TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
                    Toggle::make('is_active')->label('Aktif')->default(true),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Wilayah')->searchable(),
                TextColumn::make('fee')->label('Ongkir')->money('IDR')->sortable(),
                TextColumn::make('sort_order')->label('Urutan')->sortable(),
                ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShippingZones::route('/'),
            'create' => Pages\CreateShippingZone::route('/create'),
            'edit' => Pages\EditShippingZone::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        $admin = auth('admin')->user();
        return $admin && $admin->hasPermission('manage_shipping');
    }
}

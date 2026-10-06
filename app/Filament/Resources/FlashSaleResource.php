<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlashSaleResource\Pages;
use App\Models\FlashSale;
use App\Models\ProductVariant;
use Filament\Forms\Components\DateTimePicker;
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

class FlashSaleResource extends Resource
{
    protected static ?string $model = FlashSale::class;
    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    protected static ?string $navigationLabel = 'Flash Sale';
    protected static ?string $modelLabel = 'Flash Sale';
    protected static ?string $navigationGroup = 'Toko';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Select::make('product_id')
                    ->label('Produk')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()                          // reactive: update varian saat produk berubah
                    ->afterStateUpdated(fn($set) => $set('product_variant_id', null)),

                Select::make('product_variant_id')
                    ->label('Varian')
                    ->options(function ($get) {
                        $productId = $get('product_id');
                        if (!$productId) return [];
                        return \App\Models\ProductVariant::where('product_id', $productId)
                            ->where('is_active', true)
                            ->orderBy('sort_order')
                            ->pluck('name', 'id');
                    })
                    ->required(fn($get) => \App\Models\ProductVariant::where('product_id', $get('product_id'))->where('is_active', true)->exists())
                    ->placeholder(fn($get) => $get('product_id') ? 'Pilih varian...' : '← Pilih produk dulu')
                    ->live()
                    ->disabled(fn($get) => !$get('product_id')),

                TextInput::make('flash_price')->label('Harga Flash Sale (Rp)')->numeric()->prefix('Rp')->required(),
                TextInput::make('flash_stock')->label('Kuota Flash Sale')->suffix('kg')->helperText('Jumlah kg yang boleh dijual dengan harga flash. 0 = habis.')->numeric()->default(0),
                DateTimePicker::make('starts_at')->label('Mulai')->required(),
                DateTimePicker::make('ends_at')->label('Selesai')->required(),
                Toggle::make('is_active')->label('Aktif')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')->label('Produk')->searchable(),
                TextColumn::make('variant.name')->label('Varian'),
                TextColumn::make('flash_price')->label('Harga Flash')->money('IDR'),
                TextColumn::make('flash_stock')->label('Stok'),
                TextColumn::make('starts_at')->label('Mulai')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('ends_at')->label('Selesai')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('status_label')->label('Status')
                    ->badge()
                    ->state(function (FlashSale $record): string {
                        if (!$record->is_active)
                            return 'Nonaktif';
                        if (now() < $record->starts_at)
                            return 'Belum Mulai';
                        if (now() > $record->ends_at)
                            return 'Selesai';
                        return 'Aktif';
                    })
                    ->color(fn($state) => match ($state) {
                        'Aktif' => 'success',
                        'Belum Mulai' => 'info',
                        'Selesai' => 'gray',
                        'Nonaktif' => 'danger',
                        default => 'gray',
                    }),
                ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFlashSales::route('/'),
            'create' => Pages\CreateFlashSale::route('/create'),
            'edit' => Pages\EditFlashSale::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        $admin = auth('admin')->user();
        return $admin && $admin->hasPermission('manage_flash_sales');
    }
}

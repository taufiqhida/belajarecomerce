<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Produk';
    protected static ?string $modelLabel = 'Produk';
    protected static ?string $navigationGroup = 'Toko';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Produk')->schema([
                TextInput::make('name')
                    ->label('Nama Produk')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn($state, $set) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->label('Slug URL')
                    ->required()
                    ->unique(ignoreRecord: true),

                Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Select::make('badge')
                    ->label('Badge')
                    ->options([
                        'none' => 'Tidak Ada',
                        'best_seller' => 'Best Seller',
                        'new' => 'Baru',
                        'promo' => 'Promo',
                        'limited' => 'Limited',
                    ])
                    ->default('none'),

                RichEditor::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
            ])->columns(2),

            Section::make('Gambar')->schema([
                FileUpload::make('image')
                    ->label('Gambar Utama')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->helperText('Maks. 2MB · JPG, PNG, WebP'),

                FileUpload::make('images')
                    ->label('Gambar Tambahan')
                    ->image()
                    ->disk('public')
                    ->multiple()
                    ->directory('products')
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->reorderable()
                    ->helperText('Maks. 2MB per file · JPG, PNG, WebP'),
            ])->columns(2),

            Section::make('Harga & Stok')->schema([
                TextInput::make('base_price')
                    ->label('Harga Dasar (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),

                TextInput::make('modal_price')
                    ->label('Harga Modal (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),

                TextInput::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->default(0),

                TextInput::make('sort_order')
                    ->label('Urutan Tampil')
                    ->numeric()
                    ->default(0),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ])->columns(3),

            Section::make('Varian Produk')
                ->description('Tambahkan varian harga (warna, ukuran, dll)')
                ->schema([
                    Repeater::make('variants')
                        ->relationship()
                        ->schema([
                            TextInput::make('name')
                                ->label('Nama Varian')
                                ->required()
                                ->placeholder('Merah - L'),

                            Select::make('type')
                                ->label('Tipe')
                                ->options([
                                    'default' => 'Default',
                                    'color' => 'Warna',
                                    'size' => 'Ukuran',
                                    'weight' => 'Berat',
                                    'other' => 'Lainnya',
                                ])
                                ->default('default'),

                            TextInput::make('price')
                                ->label('Harga Jual (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->required(),

                            TextInput::make('modal_price')
                                ->label('Harga Modal (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0),

                            Placeholder::make('sold_count')
                                ->label('Terjual (Pesanan Selesai)')
                                ->content(function ($record): HtmlString {
                                    if (!$record || !$record->id) {
                                        return new HtmlString('<span style="color:#94a3b8">—</span>');
                                    }
                                    $sold = $record->soldCount();
                                    return new HtmlString(
                                        '<strong style="color:#16a34a;font-size:1.1rem">' . $sold . '</strong> unit'
                                    );
                                }),

                            Toggle::make('is_active')
                                ->label('Aktif')
                                ->default(true),
                        ])
                        ->columns(3)
                        ->orderColumn('sort_order')
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn(array $state): ?string => $state['name'] ?? null),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->label('Foto')->circular(),
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Kategori')->badge(),
                TextColumn::make('badge')->label('Badge')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'best_seller' => 'success',
                        'new' => 'info',
                        'promo' => 'warning',
                        'limited' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('base_price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('stock')->label('Stok')->sortable(),
                ToggleColumn::make('is_active')->label('Aktif'),
                TextColumn::make('created_at')->label('Dibuat')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')->relationship('category', 'name')->label('Kategori'),
                SelectFilter::make('badge')->options([
                    'best_seller' => 'Best Seller',
                    'new' => 'Baru',
                    'promo' => 'Promo',
                    'limited' => 'Limited',
                ])->label('Badge'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        $admin = auth('admin')->user();
        return $admin && $admin->hasPermission('manage_products');
    }
}

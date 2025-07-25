<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChapterResource\Pages;
use App\Models\Chapter;
use App\Models\Manga;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Str;

class ChapterResource extends Resource
{
    protected static ?string $model = Chapter::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Chapters';

    protected static ?string $pluralLabel = 'Chapters';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Chapter Information')
                    ->schema([
                        Select::make('manga_id')
                            ->label('Manga')
                            ->relationship('manga', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $manga = Manga::find($state);
                                    if ($manga) {
                                        $set('slug', Str::slug($manga->title . '-chapter-'));
                                    }
                                }
                            }),

                        TextInput::make('number')
                            ->label('Chapter Number')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*_-+=';
                                    $random = '';
                                    for ($i = 0; $i < 10; $i++) {
                                        $random .= $chars[random_int(0, strlen($chars) - 1)];
                                    }

                                    $slug = '*' . $random;
                                    $set('slug', $slug);
                                }
                            }),


                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(Chapter::class, 'slug', ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('URL-friendly version of the chapter title'),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'fixed' => 'Fixed',
                            ])
                            ->default('draft')
                            ->nullable()
                            ->helperText('Leave empty if not required'),
                    ])
                    ->columns(2),

                Section::make('Chapter Images')
                    ->schema([
                        FileUpload::make('chapter_images')
                            ->label('Chapter Images')
                            ->multiple()
                            ->image()
                            ->directory('chapters')
                            ->visibility('public')
                            ->reorderable()
                            ->appendFiles()
                            ->helperText('Upload chapter images in reading order. You can drag to reorder them.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Preview')
                    ->schema([
                        Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (Chapter $record): ?string => $record->created_at?->diffForHumans()),

                        Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn (Chapter $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columns(2)
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('preview_image')
                    ->label('Preview')
                    ->circular()
                    ->size(40)
                    ->state(function (Chapter $record) {
                        // Gunakan accessor yang sudah dibuat di model
                        return $record->first_image_url;
                    })
                    ->defaultImageUrl(asset('images/no-image.png')),

                TextColumn::make('manga.title')
                    ->label('Manga')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('number')
                    ->label('Chapter')
                    ->sortable()
                    ->prefix('#'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->limit(40)
                    ->copyable()
                    ->copyMessage('Slug copied')
                    ->copyMessageDuration(1500),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'published',
                        'warning' => 'fixed',
                        'gray' => fn ($state): bool => $state === null,
                    ])
                    ->formatStateUsing(fn ($state): string => $state ? ucfirst($state) : 'Not Set'),

                TextColumn::make('image_count')
                    ->label('Images')
                    ->state(function (Chapter $record) {
                        // Gunakan accessor yang sudah dibuat di model
                        $count = $record->image_count;
                        return $count > 0 ? $count . ' images' : 'No images';
                    })
                    ->badge()
                    ->color(function (Chapter $record) {
                        return $record->image_count > 0 ? 'success' : 'gray';
                    }),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('manga_id')
                    ->label('Manga')
                    ->relationship('manga', 'title')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'fixed' => 'Fixed',
                    ])
                    ->placeholder('All statuses'),
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChapters::route('/'),
            'create' => Pages\CreateChapter::route('/create'),
            'edit' => Pages\EditChapter::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['manga']);
    }
}
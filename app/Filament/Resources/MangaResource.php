<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MangaResource\Pages;
use App\Models\Manga;
use App\Models\Genre;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Set;

class MangaResource extends Resource
{
    protected static ?string $model = Manga::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        $set('slug', Str::slug($state));
                                    }),

                                TextInput::make('alternative_title')
                                    ->label('Alternative Title')
                                    ->maxLength(255),

                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->rules(['alpha_dash']),
                            ]),

                        Textarea::make('description')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('author')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('artist')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                    ]),

                Section::make('Classification')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('genres')
                                    ->label('Genres')
                                    ->relationship('genres', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                    ])
                                    ->required()
                                    ->helperText('Select one or more genres'),

                                Select::make('user_id')
                                    ->label('Created By')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->default(auth()->id()),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->options([
                                        'ongoing' => 'Ongoing',
                                        'completed' => 'Completed',
                                        'hiatus' => 'Hiatus',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->required()
                                    ->default('ongoing'),

                                Select::make('type')
                                    ->options([
                                        'manga' => 'Manga',
                                        'manhwa' => 'Manhwa',
                                        'manhua' => 'Manhua',
                                        'webtoon' => 'Webtoon',
                                    ])
                                    ->required()
                                    ->default('manga'),
                            ]),
                    ]),

                Section::make('Cover Image')
                    ->schema([
                        FileUpload::make('cover_image')
                            ->image()
                            ->directory('manga-covers')
                            ->disk('public')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('3:4')
                            ->imageResizeTargetWidth('600')
                            ->imageResizeTargetHeight('800')
                            ->maxSize(32768)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Upload cover image (max 32mb, recommended 600x800px)')
                            ->columnSpanFull()
                            ->preserveFilenames()
                            ->moveFiles(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('Cover')
                    ->height(60)
                    ->width(45)
                    ->defaultImageUrl(url('/images/no-cover.png')),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('author')
                    ->searchable()
                    ->sortable()
                    ->limit(20),

                TextColumn::make('artist')
                    ->searchable()
                    ->sortable()
                    ->limit(20),

                TextColumn::make('genres.name')
                    ->label('Genres')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->tooltip(function ($record) {
                        return $record->genres->pluck('name')->join(', ');
                    }),
                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'completed',
                        'warning' => 'ongoing',
                        'danger' => 'cancelled',
                        'secondary' => 'hiatus',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'completed',
                        'heroicon-o-clock' => 'ongoing',
                        'heroicon-o-x-circle' => 'cancelled',
                        'heroicon-o-pause-circle' => 'hiatus',
                    ]),

                BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'manga',
                        'success' => 'manhwa',
                        'warning' => 'manhua',
                        'info' => 'webtoon',
                    ]),

                TextColumn::make('user.name')
                    ->label('Created By')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('genres')
                    ->label('Genre')
                    ->relationship('genres', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->options([
                        'ongoing' => 'Ongoing',
                        'completed' => 'Completed',
                        'hiatus' => 'Hiatus',
                        'cancelled' => 'Cancelled',
                    ]),

                SelectFilter::make('type')
                    ->options([
                        'manga' => 'Manga',
                        'manhwa' => 'Manhwa',
                        'manhua' => 'Manhua',
                        'webtoon' => 'Webtoon',
                    ]),

                SelectFilter::make('user_id')
                    ->label('Created By')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
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
            ->defaultSort('created_at', 'desc')
            ->searchable()
            ->striped()
            ->emptyStateHeading('No manga found')
            ->emptyStateDescription('Once you add manga, they will appear here.')
            ->emptyStateIcon('heroicon-o-book-open');
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
            'index' => Pages\ListMangas::route('/'),
            'create' => Pages\CreateManga::route('/create'),
            'edit' => Pages\EditManga::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->title;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Author' => $record->author,
            'Genres' => $record->genres->pluck('name')->join(', '),
            'Status' => ucfirst($record->status),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'author', 'artist', 'genres.name'];
    }
}
<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Arr;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Override;

class UserProfile extends BaseEditProfile
{

    #[Override]
    public static function getLabel(): string
    {
        return 'My Account';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->icon(Heroicon::UserCircle)
                    ->description('Keep your account information up to date to ensure account security')
                    ->collapsible()
                    ->columnSpan(2)
                    ->components([

                        FileUpload::make('avatar')
                            ->disk('public')
                            ->directory('avatars')
                            ->imageEditor()
                            ->imageAspectRatio('1:1')
                            ->maxSize(5120) // 5 mb
                            ->image(),

                        $this->getNameFormComponent(),

                        $this->getEmailFormComponent(),

                        $this->getPasswordFormComponent(),


                        $this->getPasswordConfirmationFormComponent()
                            ->visible(fn($get) => filled($get('password'))),
                    ]),

                Section::make('Customization')
                    ->description('Make your panel align with the theme you like')
                    ->icon(Heroicon::PaintBrush)
                    ->collapsible()
                    ->schema([]),
                Section::make('Security')
                    ->description('Manage how you sign in and protect your account from unauthorized access')
                    ->icon(Heroicon::LockClosed)
                    ->collapsible()
                    ->columnSpan(2)
                    ->components([
                        ...Arr::wrap($this->getMultiFactorAuthenticationContentComponent()),

                    ])
            ])
            ->columns([
                'sm' => 1,
                'xl' => 3
            ]);
    }

    #[Override]
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Profile Updated')
            ->body('Your changes have been saved successfully')
            ->duration(1500)
            ->send();
    }

    #[Override]
    protected function getRedirectUrl(): ?string
    {
        return parent::getRedirectUrl();
    }
}

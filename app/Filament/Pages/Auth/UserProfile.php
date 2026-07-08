<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
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
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent()
                    ->visible(true),
                Select::make('currency')
                    ->options(['EUR', 'PHP'])
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

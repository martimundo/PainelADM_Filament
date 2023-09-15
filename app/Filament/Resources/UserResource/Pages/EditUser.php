<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Faker\Provider\ar_EG\Text;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\Rules\Password as RulesPassword;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('change_password')->label("Trocar Senha")
            ->form([
                TextInput::make('password')
                ->label(__('Senha'))
                    ->required()
                    ->password()
                    ->rule(RulesPassword::default()),
                TextInput::make('password_confirmation')
                ->label(__('Confirmar Senha'))
                    ->required()
                    ->password()
                    ->same('password')
                    ->rule(RulesPassword::default()),
            ])
            ->action(function(array $data){
                $this->record->update([
                    'password'=>bcrypt($data['password'])
                ]);
                $this->notify('success','Senha atualizada com sucesso');
            }),
            Actions\DeleteAction::make()
            
        ];
    }
}

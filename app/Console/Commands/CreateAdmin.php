<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'make:admin';
    protected $description = 'Créer un utilisateur administrateur';

    public function handle()
    {
        $name = $this->ask('Nom');
        $email = $this->ask('Email');
        $phone = $this->ask('Téléphone (optionnel)', '');
        $password = $this->secret('Mot de passe');

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($password),
            'role' => 'admin',
        ]);

        $this->info('Administrateur créé avec succès !');
        $this->table(['ID', 'Nom', 'Email', 'Rôle'], [[$user->id, $user->name, $user->email, $user->role]]);
    }
}

<?php

namespace Backstage\Laravel\Users\Commands;

use Backstage\Laravel\Users\Eloquent\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class LaravelUsersCommand extends Command
{
    protected $signature = 'make:user 
        {--name= : The name of the user}
        {--email= : The email address}
        {--password= : The user password}
        {--role= : The role to assign}';

    protected $description = 'Create a new user via CLI with optional prompts.';

    public function handle(): int
    {
        $this->info('Laravel Users Command');

        $name = $this->option('name') ?: text('Name', placeholder: 'John Doe', required: true);
        $email = $this->option('email') ?: text('Email', placeholder: 'john@doe.nl', required: true);
        $password = $this->option('password') ?: password('Password', placeholder: 'secret', required: true);

        $roleClass = config('permissions.models.role', Role::class);
        $userClass = config('users.eloquent.user.model', User::class);

        // Only fetch roles with the correct guard
        $roles = $roleClass::where('guard_name', 'web')->pluck('name')->toArray();
        $selectedRole = null;

        if (empty($roles)) {
            if ($this->confirm('No roles found. Do you want to create a new role?', true)) {
                $roleName = text('Role name', placeholder: 'admin');
                $selectedRole = $roleClass::create([
                    'name' => $roleName,
                    'guard_name' => 'web',
                ]);
                $this->info("Role {$roleName} created.");
            } else {
                $this->warn('No roles found. Proceeding without assigning a role.');
            }
        } else {
            $roleInput = $this->option('role') ?: select('Role', options: $roles);
            $selectedRole = $roleInput;
        }

        /** @var \Illuminate\Database\Eloquent\Model $user */
        $user = new $userClass;
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->save();

        // Assign role after user is saved
        if ($selectedRole instanceof Role) {
            $user->assignRole($selectedRole->name);
        } elseif (is_string($selectedRole)) {
            $user->assignRole($selectedRole);
        }

        $this->info('User created successfully!');
        $this->line("User ID: {$user->id}");
        $this->line("Name: {$user->name}");
        $this->line("Email: {$user->email}");
        $this->line('Role: '.($selectedRole instanceof Role ? $selectedRole->name : $selectedRole));
        $this->line("Password: {$password}");

        return Command::SUCCESS;
    }
}

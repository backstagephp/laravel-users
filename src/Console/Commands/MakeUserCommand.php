<?php

namespace Backstage\Laravel\Users\Console\Commands;

use Backstage\Laravel\Users\Eloquent\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

use function Laravel\Prompts\error;
use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;

class MakeUserCommand extends Command
{
    protected $signature = 'make:user 
        {--name= : The name of the user}
        {--email= : The email address}
        {--password= : The user password}
        {--role= : The role to assign}';

    protected $description = 'Create a new user via CLI with optional prompts.';

    public function handle(): int
    {
        $this->info('Creating a new user...');
        $this->line('You can provide the user details via command line options or prompts.');

        $name = $this->option('name') ?: text('Name', placeholder: 'John Doe', required: true);
        $email = $this->validateColumn($this->option('email') ?: text('Email', placeholder: 'john@doe.nl', required: true), 'email');
        $password = $this->option('password') ?: password('Password', placeholder: 'secret', required: true);
        $verified = $this->confirm('Is the user verified?', true);

        $roleClass = config('permission.models.role', Role::class);
        $userClass = config('users.eloquent.user.model', User::class);
        $guard = config('auth.defaults.guard', 'web');

        $roles = $roleClass::where('guard_name', $guard)->pluck('name')->toArray();
        $selectedRole = null;

        if (empty($roles)) {
            if ($this->confirm('No roles found. Do you want to create a new role?', true)) {
                $roleName = text('Role name', placeholder: 'admin');
                $selectedRole = $roleClass::query()->create([
                    'name' => $roleName,
                    'guard_name' => $guard,
                ]);

                $selectedRole = $selectedRole->refresh();

                $this->info("Role {$roleName} created.");
            } else {
                $this->warn('No roles found. Proceeding without assigning a role.');
            }
        } else {
            $roleInput = $this->option('role') ?: select('Role', options: $roles);

            if ($roleInput && $this->option('role')) {
                $exists = $roleClass::where('name', $roleInput)->exists();
                if (! $exists) {
                    error("The role '{$roleInput}' does not exist.");
                    exit;
                }
            }

            $selectedRole = $roleInput;
        }

        /**
         * @var User $userClass
         */
        $user = new $userClass;
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);

        if ($verified) {
            $user->email_verified_at = now();
        }

        $user->save();

        if ($selectedRole instanceof Role) {
            $user->assignRole($selectedRole);
        } elseif (is_string($selectedRole)) {
            $user->assignRole($selectedRole);
        }

        table(
            ['User ID', 'Name', 'Email', 'Role'],
            [
                [
                    $user->id,
                    $user->name,
                    $user->email,
                    $selectedRole instanceof Role ? $selectedRole->name : $selectedRole,
                ],
            ]
        );

        info('User created successfully!');
        $this->line("User ID: {$user->id}");
        $this->line("Name: {$user->name}");
        $this->line("Email: {$user->email}");
        if ($selectedRole) {
            $this->line('Role: '.($selectedRole instanceof Role ? $selectedRole->name : $selectedRole));
        }
        $this->line("Password: {$password}");

        return Command::SUCCESS;
    }

    protected function validateColumn($value, $column)
    {
        if (empty($value)) {
            return $value;
        }

        $userClass = config('users.eloquent.user.model', User::class);
        $exists = DB::table((new $userClass)->getTable())->where($column, $value)->exists();

        if ($exists) {
            error("The {$column} '{$value}' already exists.");
            exit;
        }

        return $value;
    }
}

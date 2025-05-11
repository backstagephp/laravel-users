<?php

namespace Backstage\Laravel\Users\Commands;

use function Termwind\ask;
use Illuminate\Console\Command;
use function Laravel\Prompts\info;

use function Laravel\Prompts\text;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use function Laravel\Prompts\error;
use function Laravel\Prompts\table;
use function Laravel\Prompts\search;
use function Laravel\Prompts\select;
use Spatie\Permission\Traits\HasRoles;
use function Laravel\Prompts\multisearch;
use Spatie\Permission\Traits\HasPermissions;
use Backstage\Laravel\Users\Eloquent\Models\User;

class ListUsersCommand extends Command
{
    protected $signature = 'users:list {--edit}';

    protected $description = 'List all users in the system.';

    public function handle(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            error('No users found.');
            return;
        }

        info('Found ' . $users->count() . ' user(s):');

        $this->renderTable($users);

        if (!$this->option('edit')) {
            $editingUsers = $this->confirm('Do you want to edit any user?', false);

            if (!$editingUsers) {
                return;
            }
        }

        $userId = search(
            label: 'Search for the user that should receive the mail',
            options: fn(string $value) => strlen($value) > 0
                ? User::whereLike('name', "%{$value}%")->pluck('name', 'id')->map(fn($name, $id) => $name . ' (ID: ' . $id . ')')->toArray()
                : []
        );

        /**
         * @var HasRoles $user
         */
        $user = User::find($userId);

        if (!$user) {
            error('User not found!');
            return;
        }

        info('User selected:');
        $this->renderTable(collect([$user]));

        $changableFields = ['name', 'email', 'roles'];

        $fieldToChange = $this->choice('Which field do you want to change?', $changableFields);

        switch ($fieldToChange) {
            case 'name':
                $newName = text('Enter the new name:', required: true);
                $user->name = $newName;
                break;

            case 'email':
                $newEmail = text('Enter the new email:', required: true);
                $user->email = $newEmail;
                break;

            case 'roles':
                $attachingRoles = $this->confirm('Do you want to attach roles to the user?', false);

                if (!$attachingRoles) {
                    $detachingRoles = $this->confirm('Do you want to detach roles from the user?', false);

                    $contaningRoles = $user->getRoleNames();

                    if ($contaningRoles->isEmpty()) {
                        error('User has no roles to detach.');
                        return;
                    }

                    $selectedRoles = multisearch(
                        label: 'Select the roles to detach from the user',
                        options: fn(string $value) => strlen($value) > 0
                            ? $contaningRoles->values()->all()
                            : [],
                        required: true,
                    );

                    $collectedSeletedRoles = collect($selectedRoles);

                    if ($collectedSeletedRoles->isEmpty()) {
                        error('No roles selected.');
                        return;
                    }

                    foreach ($collectedSeletedRoles->toArray() as $role) {
                        $user->removeRole($role);
                    }

                    info('Roles detached from the user.');

                    return;
                }

                $roleClass = config('permission.models.role', Role::class);
                $allRoles = $roleClass::pluck('name');
                $currentUserRoles = $user->getRoleNames();

                $diffedRoles = $allRoles->diff($currentUserRoles);

                if ($diffedRoles->isEmpty()) {
                    error('No roles available to assign to the user.');
                    return;
                }

                $selectedRoles = multisearch(
                    label: 'Select the roles to assign to the user',
                    options: fn(string $value) => strlen($value) > 0
                        ? $diffedRoles->values()->all()
                        : [],
                    required: true,
                );

                $collectedSeletedRoles = collect($selectedRoles);

                if ($collectedSeletedRoles->isEmpty()) {
                    error('No roles selected.');
                    return;
                }

                $user->syncRoles($collectedSeletedRoles->toArray());
        }

        $user->save();
    }

    protected function renderTable(Collection $users)
    {
        table([
            'ID',
            'Name',
            'Email',
            'Verified',
            'Role(s)',
            'Created At'
        ], $users->map(fn(User $user) => [
            $user->id,
            $user->name,
            $user->email,
            $user->hasVerifiedEmail() ? 'Yes' : 'No',
            $user->getRoleNames()->implode(', ') ?: 'No roles',
            $user->created_at->format('Y-m-d H:i:s') . ' (' . $user->created_at->diffForHumans() . ')',
        ])->toArray());
    }
}

<?php

namespace Backstage\Laravel\Users\Commands;

use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\error;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\warning;

class DeleteUser extends Command
{
    protected $signature = 'users:delete {--force-delete}';

    protected $description = 'List all users in the system.';

    public function handle()
    {
        if ($this->option('force-delete') && posix_geteuid() !== 0) {
            error('This command must be run as root. Try: sudo php artisan '.str($this->signature)->replace(['{', '}'], '')->toString());

            return Command::FAILURE;
        }

        $userCollection = User::all();

        if ($userCollection->isEmpty()) {
            warning('No users found. Please create a user first.');

            return Command::FAILURE;
        }

        $users = multiselect(
            label: 'Select the user(s) to delete',
            options: $userCollection->pluck('name', 'id')->map(fn ($name, $id) => $name.' (ID: '.$id.')')->toArray(),
            required: true,
        );

        if (empty($users)) {
            error('No users selected.');

            return Command::FAILURE;
        }

        $users = User::whereIn('id', $users)->get();

        if ($users->isEmpty()) {
            error('No users found.');

            return Command::FAILURE;
        }

        $this->info('Deleting users...');
        foreach ($users as $user) {
            if ($this->option('force-delete')) {
                $user->forceDelete();
            } else {
                $user->delete();
            }

            $this->info($this->option('force-delete') ? 'Force deleted' : 'Deleted'.' user: '.$user->name);
        }
    }
}

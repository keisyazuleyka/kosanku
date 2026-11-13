<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateAdminCommand extends Command
{
    protected $signature = 'kosanku:create-admin {--email=} {--name=Admin Kosanku} {--password=}';

    protected $description = 'Membuat atau mengangkat akun admin pertama secara aman.';

    public function handle(): int
    {
        $email = $this->option('email') ?: env('KOSANKU_ADMIN_EMAIL') ?: text('Email admin');
        $name = $this->option('name') ?: 'Admin Kosanku';
        $plain = $this->option('password') ?: env('KOSANKU_ADMIN_PASSWORD') ?: password('Password admin (minimal 10 karakter)');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Email tidak valid.');

            return self::FAILURE;
        }

        if (strlen((string) $plain) < 10) {
            $this->error('Password minimal 10 karakter.');

            return self::FAILURE;
        }

        $user = User::query()->firstOrNew(['email' => $email]);
        $user->name = $user->exists ? $user->name : $name;
        $user->password = Hash::make($plain);
        $user->role = UserRole::Admin;
        $user->is_active = true;
        $user->save();

        $this->info("Admin siap: {$user->email}");

        return self::SUCCESS;
    }
}

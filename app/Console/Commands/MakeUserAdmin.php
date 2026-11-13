<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeUserAdmin extends Command
{
    protected $signature = 'user:make-admin {email : Email của tài khoản cần cấp quyền quản trị}';

    protected $description = 'Cấp quyền quản trị cho một tài khoản người dùng';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (!$user) {
            $this->error('Không tìm thấy tài khoản với email này.');

            return self::FAILURE;
        }

        $user->forceFill(['is_admin' => true])->save();
        $this->info("Đã cấp quyền quản trị cho {$user->email}.");

        return self::SUCCESS;
    }
}

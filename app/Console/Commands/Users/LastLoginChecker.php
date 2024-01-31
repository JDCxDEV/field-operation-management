<?php

namespace App\Console\Commands\Users;

use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class LastLoginChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:last-login-checker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check users last login and disable if inactive for 30 days';

    /**
     * Set user status to disable if inactive for 30 days.
     *
     * @return int
     */
    public function handle()
    {

        $exceptions = [User::SUPER_ADMIN, User::ADMIN, User::COMPANY_COORDINATOR, User::MARKET_COORDINATOR];
        $activeUsers = User::whereNotIn("role_type", $exceptions)->where(["status" => User::USER_ACTIVE, "blacklisted" => 0])->get();

        $now = Carbon::now();

        foreach($activeUsers as $user) {
            $dateToCompare = $user->last_login ? $user->last_login : $user->created_at;
            $diffDays = $dateToCompare->diff($now)->days;
            if($diffDays >= 30) {
                $user->update(["status" => User::USER_INACTIVE]);
            }
        }

        return Command::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Console\Command;

class ExpireTrials extends Command
{
    protected $signature = 'trials:expire';

    public function handle()
    {
        // Expirer les trials gratuits
        $freeExpired = Subscription::where('plan', 'free')
            ->where('status', 'active')
            ->where('trial_ends_at', '<', now())
            ->whereNotNull('trial_ends_at')
            ->get();

        foreach ($freeExpired as $sub) {
            $sub->update(['status' => 'expired']);
            User::where('id', $sub->user_id)->update(['plan' => 'free']);
        }

        // Expirer les abonnements payants
        $paidExpired = Subscription::where('plan', '!=', 'free')
            ->where('status', 'active')
            ->where('ends_at', '<', now())
            ->whereNotNull('ends_at')
            ->get();

        foreach ($paidExpired as $sub) {
            $sub->update(['status' => 'expired']);
            User::where('id', $sub->user_id)->update(['plan' => 'free', 'trial_ends_at' => null]);
        }

        $this->info($freeExpired->count() . ' trials gratuits expirés.');
        $this->info($paidExpired->count() . ' abonnements payants expirés.');
    }
}

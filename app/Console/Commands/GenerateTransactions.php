<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Models\SupplierTransaction;

class GenerateTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate automated transactions based on frequency';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $transactions = SupplierTransaction::all();
        
        foreach ($transactions as $transaction) {
            $shouldRun = false;

            if ($transaction->frequency == 'minutely') {
                $shouldRun = true;
            } else if ($transaction->frequency == 'hourly') {
                $shouldRun = $transaction->created_at->diffInHours($now) >= 1;
            } else if ($transaction->frequency == 'daily') {
                $shouldRun = $transaction->created_at->diffInDays($now) >= 1;
            } else if ($transaction->frequency == 'weekly') {
                $shouldRun = $transaction->created_at->diffInWeeks($now) >= 1;
            } else if ($transaction->frequency == 'monthly') {
                $shouldRun = $transaction->created_at->diffInMonths($now) >= 1;
            }

            if ($shouldRun) {
                $newTransaction = $transaction->replicate();
                $newTransaction->tran_id = mt_rand(100000, 999999);
                $newTransaction->date = $now->toDateString();
                $newTransaction->created_at = $now;
                $newTransaction->save();
                $this->info('Generated new transaction for user ID ' . $transaction->user_id);
            }
        }
    }
}
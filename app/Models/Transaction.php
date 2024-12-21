<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    public function equityHolder()
    {
        return $this->belongsTo(EquityHolder::class, 'share_holder_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}

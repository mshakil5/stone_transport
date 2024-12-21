<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignRequest extends Model
{
    use HasFactory;

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function campaignRequestProducts()
    {
        return $this->hasMany(CampaignRequestProduct::class, 'campaign_request_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}

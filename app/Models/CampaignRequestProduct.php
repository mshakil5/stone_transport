<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignRequestProduct extends Model
{
    use HasFactory;

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function campaignRequest()
    {
        return $this->belongsTo(CampaignRequest::class, 'campaign_request_id');
    }
}

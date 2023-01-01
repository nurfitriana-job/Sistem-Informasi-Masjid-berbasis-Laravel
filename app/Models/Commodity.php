<?php

namespace App\Models;

use App\Enums\Condition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commodity extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'condition' => Condition::class,
    ];

    /**
     * Get the commodity location associated with the commodity.
     */
    public function commodityLocation()
    {
        return $this->belongsTo(CommodityLocation::class);
    }

    /**
     * Get the commodity acquisition associated with the commodity.
     */
    public function commodityAcquisition()
    {
        return $this->belongsTo(CommodityAcquisition::class);
    }
}

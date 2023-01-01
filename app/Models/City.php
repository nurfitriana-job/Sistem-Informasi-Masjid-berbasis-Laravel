<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class City extends Model
{
    public $incrementing = false;

    // add fillable
    protected $fillable = [];

    // add guaded
    protected $guarded = ['id'];

    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    public static function getRows()
    {
        $cities = Http::get('https://api.myquran.com/v2/sholat/kota/semua')->json();

        $cities = Arr::map($cities['data'], function ($item) {
            return Arr::only(
                $item,
                [
                    'id',
                    'lokasi',
                ]
            );
        });

        foreach ($cities as $city) {
            City::updateOrCreate(
                [
                    'uid' => (int) $city['id'],
                ],
                [
                    'uid' => (int) $city['id'],
                    'name' => $city['lokasi'],
                ]
            );
        }

        return $cities;
    }
}

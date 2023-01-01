<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PrayerTime extends Model
{
    // add fillable
    protected $fillable = [];

    // add guaded
    protected $guarded = ['id'];

    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'date' => 'date',
        'imsak' => 'datetime:H:i',
        'subuh' => 'datetime:H:i',
        'terbit' => 'datetime:H:i',
        'dhuha' => 'datetime:H:i',
        'dzuhur' => 'datetime:H:i',
        'ashar' => 'datetime:H:i',
        'maghrib' => 'datetime:H:i',
        'isya' => 'datetime:H:i',
    ];

    public static function getRows($cityId)
    {
        $prayerTimesRaw = Http::get('https://api.myquran.com/v2/sholat/jadwal/' . $cityId . date('/Y') . date('/m'))->json();

        $prayerTimes = Arr::map($prayerTimesRaw['data']['jadwal'], function ($item) {
            return Arr::only(
                $item,
                [
                    'date',
                    'imsak',
                    'subuh',
                    'terbit',
                    'dhuha',
                    'dzuhur',
                    'ashar',
                    'maghrib',
                    'isya',
                ]
            );
        });

        DB::transaction(function () use ($prayerTimes, $cityId, $prayerTimesRaw) {
            foreach ($prayerTimes as $prayerTime) {
                PrayerTime::updateOrCreate(
                    [
                        'city_id' => $cityId,
                        'name' => $prayerTimesRaw['data']['daerah'] ?? $cityId,
                        'date' => $prayerTime['date'],
                        'imsak' => $prayerTime['imsak'],
                        'subuh' => $prayerTime['subuh'],
                        'terbit' => $prayerTime['terbit'],
                        'dhuha' => $prayerTime['dhuha'],
                        'dzuhur' => $prayerTime['dzuhur'],
                        'ashar' => $prayerTime['ashar'],
                        'maghrib' => $prayerTime['maghrib'],
                        'isya' => $prayerTime['isya'],
                    ]
                );
            }
        });

        return $prayerTimes;
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'uid');
    }
}

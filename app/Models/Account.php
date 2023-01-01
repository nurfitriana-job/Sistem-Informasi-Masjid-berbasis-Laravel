<?php

namespace App\Models;

use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Account extends Model
{
    use LogsActivity;

    // add fillable
    protected $fillable = [];

    // add guaded
    protected $guarded = ['id'];

    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'type' => AccountType::class,
    ];

    public static function getNextAccountCode($type, $subType)
    {
        $account = Account::where('category_id', $type)
            ->where('sub_category_id', $subType)
            ->orderByRaw("CAST(SUBSTRING_INDEX(code, '-', -1) AS UNSIGNED) DESC")
            ->value('code');

        [$prefix, $number] = explode('-', $account);

        return $prefix . '-' . ($number + 1);
    }

    public function category()
    {
        return $this->belongsTo(Category::class)
            ->where('parent_id', null)
            ->orderBy('created_at', 'asc');
    }

    public function subCategory()
    {
        return $this->belongsTo(Category::class, 'sub_category_id')
            ->where('parent_id', '!=', null)
            ->orderBy('created_at', 'asc');
    }

    public function accountBalances()
    {
        return $this->hasMany(AccountBalance::class, 'account_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}

<?php

namespace App\Models;

use App\Enums\CategoryType;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TransactionUser extends Model implements HasMedia
{
    use InteractsWithMedia;
    use LogsActivity;

    protected $table = 'journals';

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
        'status' => TransactionStatus::class,
        'type' => TransactionType::class,
        'total_debit' => 'int',
        'total_credit' => 'int',
    ];

    protected $appends = [
        'amount',
    ];

    public function entries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function category()
    {
        $user = User::find(Auth::id());

        return $this->belongsTo(Category::class)
            ->where('type', CategoryType::TRANSACTION)
            ->when(! $user->hasRole('admin'), function ($query) use ($user) {
                return $query->where('created_by', $user->id);
            });
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function paymentAccount()
    {
        return $this->belongsTo(Account::class, 'payment_account_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getAmountAttribute()
    {
        return $this->type === TransactionType::INCOME
            ? $this->total_credit
            : $this->total_debit;
    }

    public static function generateJournalNumber()
    {
        $currentDate = Carbon::now();

        $period = $currentDate->format('ym');

        $year = $currentDate->format('Y');
        $month = $currentDate->format('m');

        // Memperbaiki query untuk menghindari error
        $lastAccount = Journal::select(DB::raw('MAX(RIGHT(journal_number, 4)) as result'))
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->whereRaw('DATE_FORMAT(transaction_date, "%y%m") = ?', [$period])
            ->orderBy('created_at', 'desc')
            ->first();

        // Menentukan nomor berikutnya
        $nextNumber = $lastAccount ? $lastAccount->result + 1 : 1;

        // Format nomor jurnal menjadi 4 digit
        $formattedNumber = sprintf('%04d', $nextNumber);

        return 'TRX' . $period . $formattedNumber;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function scopeUserTransaction($query)
    {
        return $query->where('is_user_transaction', true);
    }

    public function scopeNotUserTransaction($query)
    {
        return $query->where('is_user_transaction', false);
    }
}

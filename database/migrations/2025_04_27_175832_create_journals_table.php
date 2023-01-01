<?php

use App\Enums\TransactionStatus;
use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->string('journal_number')->unique();
            $table->date('transaction_date');
            $table->text('description');
            $table->enum('type', ['expense', 'income'])->default('expense');
            $table->enum('status', TransactionStatus::values())->default(TransactionStatus::REVIEW->value);
            $table->foreignIdFor(Category::class, 'category_id')
                ->nullable()
                ->constrained('categories')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('payment_account_id')->nullable()->constrained('accounts')->cascadeOnDelete();

            // Total
            $table->decimal('total_debit', 15, 2)->default(0);
            $table->decimal('total_credit', 15, 2)->default(0);

            // Informasi tambahan
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};

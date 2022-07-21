<?php

declare(strict_types=1);

use Bavix\Wallet\Models\Transaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up(): void
    {
        Schema::create($this->table(), function (Blueprint $table) {
            $table->id();
            $table->morphs('payable');
            $table->unsignedBigInteger('wallet_id');
            $table->enum('type', ['deposit', 'withdraw'])->index();
            $table
                ->enum(
                    'action_type',
                    ['action_by_admin', 'recharge', 'payment', 'refund', 'cash_back']
                )
                ->default('action_by_admin');
            $table->unsignedBigInteger('order_id', 20, 0);
            $table->unsignedBigInteger('cash_back_id', 20, 0);
            $table->decimal('allow_uses_cash_back', 16, 0);
            $table
            ->enum(
                'allow_uses_cash_back_type',
                ['fixed_amount', 'percentage']
            )
            ->default('fixed_amount');
            $table->decimal('amount', 64, 0);
            $table->boolean('confirmed');
            $table->json('meta')->nullable();
            $table->uuid('uuid')->unique();
            $table->timestamps();

            $table->index(['payable_type', 'payable_id'], 'payable_type_payable_id_ind');
            $table->index(['payable_type', 'payable_id', 'type'], 'payable_type_ind');
            $table->index(['payable_type', 'payable_id', 'confirmed'], 'payable_confirmed_ind');
            $table->index(['payable_type', 'payable_id', 'type', 'confirmed'], 'payable_type_confirmed_ind');
        });
    }

    public function down(): void
    {
        Schema::drop($this->table());
    }

    private function table(): string
    {
        return (new Transaction())->getTable();
    }
}

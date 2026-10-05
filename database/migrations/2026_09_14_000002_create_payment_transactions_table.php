<?php

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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('gateway')->default('cod'); // 'cod' | 'momo'
            $table->string('gateway_order_id')->nullable()->comment('orderId gửi sang MoMo / referenceId');
            $table->string('transaction_id')->nullable()->comment('transId trả về từ MoMo sau thanh toán thành công');
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('status')->default('pending')->comment('pending | paid | failed | cancelled');
            $table->integer('result_code')->nullable()->comment('resultCode từ MoMo (0 = thành công)');
            $table->string('message')->nullable()->comment('message từ MoMo hoặc ghi chú thủ công');
            $table->json('request_payload')->nullable()->comment('Toàn bộ dữ liệu gửi sang MoMo (debug/đối soát)');
            $table->json('response_payload')->nullable()->comment('Toàn bộ dữ liệu nhận về từ MoMo (debug/đối soát)');
            $table->timestamp('paid_at')->nullable()->comment('Thời điểm thanh toán thành công (theo IPN/callback MoMo)');
            $table->timestamps();

            // Đảm bảo mỗi gateway_order_id chỉ được dùng 1 lần trên mỗi gateway
            $table->unique(['gateway', 'gateway_order_id']);
            // Index giúp tìm giao dịch theo đơn hàng và trạng thái
            $table->index(['order_id', 'status']);
            // Index tìm theo transaction_id từ MoMo
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFeeFieldsToPaymentsTable extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('service_fee', 8, 2)->nullable()->after('amount');
            $table->decimal('processing_fee', 8, 2)->nullable()->after('service_fee');
            $table->decimal('total_charged', 8, 2)->nullable()->after('processing_fee');
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['service_fee', 'processing_fee', 'total_charged']);
        });
    }
}


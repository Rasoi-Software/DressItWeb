<?php

// database/migrations/xxxx_xx_xx_create_cities_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsCitiesTable extends Migration
{
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->string('admin_code1')->nullable();
            $table->decimal('lng', 10, 6)->nullable();
            $table->string('distance')->nullable();
            $table->bigInteger('geoname_id')->nullable();
            $table->string('toponym_name')->nullable();
            $table->bigInteger('country_id')->nullable();
            $table->string('fcl')->nullable();
            $table->bigInteger('population')->nullable();
            $table->string('country_code')->nullable();
            $table->string('fcl_name')->nullable();
            $table->string('admin_code_iso')->nullable(); // from adminCodes1.ISO3166_2
            $table->string('country_name')->nullable();
            $table->string('fcode_name')->nullable();
            $table->string('admin_name1')->nullable();
            $table->decimal('lat', 10, 6)->nullable();
            $table->string('fcode')->nullable();
        });
    }

    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('admin_code1');
        });
    }
}

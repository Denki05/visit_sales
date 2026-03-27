<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_photos', function (Blueprint $table) {

            $table->bigIncrements('id');

            // ✅ SESUAIKAN TIPE
            $table->string('customer_id')->nullable();
            $table->string('customer_other_address_id')->nullable();

            $table->string('file');
            $table->string('type')->nullable();

            $table->decimal('gps_latitude', 10, 7)->nullable();
            $table->decimal('gps_longitude', 10, 7)->nullable();

            $table->timestamp('taken_at')->nullable();

            $table->unsignedBigInteger('created_by');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_photos');
    }
}

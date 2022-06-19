<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->text("address");
            $table->boolean("checked");
            $table->text("description")->nullable();
            $table->text("interest")->nullable();
            $table->string("date_of_birth")->nullable();
            $table->string("email");
            $table->string("account");
            $table->json("credit_card");
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
        Schema::dropIfExists('people');
    }
};

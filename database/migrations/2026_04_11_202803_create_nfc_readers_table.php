<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nfc_readers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('usb'); // usb, network, mobile
            $table->string('connection_type')->default('wired'); // wired, wireless, network
            $table->string('com_port')->nullable();
            $table->string('baud_rate')->nullable();
            $table->string('ip_address')->nullable();
            $table->integer('port')->nullable();
            $table->string('protocol')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('last_seen')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('type');
            $table->index('connection_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfc_readers');
    }
};

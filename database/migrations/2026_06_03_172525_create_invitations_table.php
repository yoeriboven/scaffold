<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->string('public_id', 10)->unique();

            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('email');

            $table->timestamps();

            $table->unique(['team_id', 'email']);
        });
    }
};

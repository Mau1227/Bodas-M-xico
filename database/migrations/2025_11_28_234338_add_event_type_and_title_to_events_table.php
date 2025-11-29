<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Tipo de evento: wedding, birthday, xv, baby_shower, corporate, other...
            $table->string('event_type')->default('wedding')->after('user_id');

            // Título visible del evento (ej. "Cumpleaños de Sofía")
            $table->string('event_title')->nullable()->after('event_type');

            // Nombre genérico de anfitriones (para eventos que no son boda)
            $table->string('host_names')->nullable()->after('event_title');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['event_type', 'event_title', 'host_names']);
        });
    }
};

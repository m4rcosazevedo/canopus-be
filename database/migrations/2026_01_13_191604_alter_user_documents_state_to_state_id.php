<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Adiciona a nova coluna state_id
        Schema::table('user_documents', function (Blueprint $table) {
            $table->foreignId('state_id')
                ->nullable()
                ->after('issuer')
                ->constrained('states');
        });

        // 2. Migra os dados de UF -> states.id
        DB::table('user_documents')
            ->whereNotNull('state')
            ->update([
                'state_id' => DB::raw(
                    "(SELECT id FROM states WHERE states.abbr = user_documents.state LIMIT 1)"
                )
            ]);

        // 3. Remove a coluna antiga
        Schema::table('user_documents', function (Blueprint $table) {
            $table->dropColumn('state');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Recria a coluna antiga
        Schema::table('user_documents', function (Blueprint $table) {
            $table->string('state', 2)->nullable()->after('issuer');
        });

        // 2. Volta os dados de states.abbr
        DB::table('user_documents')
            ->whereNotNull('state_id')
            ->update([
                'state' => DB::raw(
                    "(SELECT abbr FROM states WHERE states.id = user_documents.state_id LIMIT 1)"
                )
            ]);

        // 3. Remove a FK e a coluna state_id
        Schema::table('user_documents', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->dropColumn('state_id');
        });
    }
};

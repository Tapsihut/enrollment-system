<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('district')->nullable()->after('division');
            $table->string('barangay')->nullable()->after('municipality');
            $table->string('sector')->nullable()->after('address');
            $table->string('school_subclassification')->nullable()->after('sector');
            $table->string('curricular_offering')->nullable()->after('school_subclassification');

            $table->index('district');
            $table->index('barangay');
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropIndex(['district']);
            $table->dropIndex(['barangay']);

            $table->dropColumn([
                'district',
                'barangay',
                'sector',
                'school_subclassification',
                'curricular_offering',
            ]);
        });
    }
};
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('machines', function (Blueprint $table) {

            if (!Schema::hasColumn('machines', 'brand')) {
                $table->string('brand')->nullable();
            }
            if (!Schema::hasColumn('machines', 'model')) {
                $table->string('model')->nullable();
            }
            if (!Schema::hasColumn('machines', 'serial')) {
                $table->string('serial')->nullable();
            }
            if (!Schema::hasColumn('machines', 'invoice_cd')) {
                $table->string('invoice_cd')->nullable();
            }
            if (!Schema::hasColumn('machines', 'year')) {
                $table->string('year')->nullable();
            }
            if (!Schema::hasColumn('machines', 'country_of_origin')) {
                $table->string('country_of_origin')->nullable();
            }

            if (!Schema::hasColumn('machines', 'stock_in_raw')) {
                $table->string('stock_in_raw')->nullable();
            }
            if (!Schema::hasColumn('machines', 'location_text')) {
                $table->string('location_text')->nullable();
            }
            if (!Schema::hasColumn('machines', 'ngay_vao_kho_raw')) {
                $table->string('ngay_vao_kho_raw')->nullable();
            }
            if (!Schema::hasColumn('machines', 'ngay_ra_kho_raw')) {
                $table->string('ngay_ra_kho_raw')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            // Tuỳ bạn: có thể drop nếu muốn rollback
            // Nhưng rollback ít dùng trong case này
        });
    }
};

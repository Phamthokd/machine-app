<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            // Các cột “thông tin máy”
            
            $table->string('brand')->nullable()->after('ten_thiet_bi');    // Brand
            $table->string('model')->nullable()->after('brand');          // Model
            $table->string('serial')->nullable()->after('model');         // Serial
            $table->string('invoice_cd')->nullable()->after('serial');    // Invoice/CD
            $table->string('year')->nullable()->after('invoice_cd');      // Year (để string vì có N/A)
            $table->string('country')->nullable()->after('year');         // Country
    
            // Ngày tháng (để nullable vì Excel có thể trống)
            $table->date('stock_in_date')->nullable()->after('country');  // Stock-in date
            $table->string('vi_tri_text')->nullable()->after('stock_in_date'); // Vị trí (Department...) dạng text
            $table->date('ngay_vao_kho')->nullable()->after('vi_tri_text');
            $table->date('ngay_ra_kho')->nullable()->after('ngay_vao_kho');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            //
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Machine;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all machines to scan and fix Excel format corruption
        $machines = Machine::all();
        foreach ($machines as $m) {
            $updated = false;

            // 1. Repair scientific notation in invoice_cd (e.g. 1.06508E+11 -> 106508000000)
            if ($m->invoice_cd && preg_match('/^\d+(\.\d+)?[eE]\+\d+$/', $m->invoice_cd)) {
                $floatVal = (float)$m->invoice_cd;
                $m->invoice_cd = sprintf('%.0f', $floatVal);
                $updated = true;
            }

            if ($m->year && preg_match('/^\d+[\/\-]\d+[\/\-]\d+$/', $m->year)) {
                try {
                    $vNormalized = str_replace('-', '/', $m->year);
                    $parts = explode('/', $vNormalized);
                    
                    $d = 0; $mVal = 0; $y = 0;
                    if (count($parts) === 3) {
                        $val1 = (int)$parts[0];
                        $val2 = (int)$parts[1];
                        $val3 = (int)$parts[2];
                        
                        if ($val3 >= 1900 && $val3 <= 1910) {
                            $y = $val3;
                            if ($val1 <= 12 && $val2 <= 31) {
                                $mVal = $val1;
                                $d = $val2;
                            } elseif ($val1 <= 31 && $val2 <= 12) {
                                $d = $val1;
                                $mVal = $val2;
                            }
                        }
                    }
                    
                    if ($y > 0 && $mVal > 0 && $d > 0) {
                        $dt = new \DateTime();
                        $dt->setDate($y, $mVal, $d);
                        $base = new \DateTime('1899-12-30');
                        $diff = $base->diff($dt);
                        $m->year = (string)$diff->days;
                        $updated = true;
                    }
                } catch (\Exception $e) {
                    // Ignore parsing issues
                }
            }

            if ($updated) {
                $m->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse action needed
    }
};

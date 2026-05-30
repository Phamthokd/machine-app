<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Machine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MachineCsvImportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin role
        Role::firstOrCreate(['name' => 'admin']);

        // Create admin user
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_admin_can_download_csv_import_template(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/machines/import-csv/template');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        
        $content = $response->streamedContent();
        
        // Assert headers are present
        $this->assertStringContainsString('ma_thiet_bi,ten_thiet_bi,to_hien_tai', $content);
        $this->assertStringContainsString('invoice_cd', $content);
        $this->assertStringContainsString('country', $content);
        $this->assertStringContainsString('stock_in_date', $content);
        $this->assertStringContainsString('vi_tri_text', $content);
        $this->assertStringContainsString('ngay_vao_kho', $content);
        $this->assertStringContainsString('ngay_ra_kho', $content);
        $this->assertStringContainsString('warranty_period', $content);
        
        // Assert samples are present
        $this->assertStringContainsString('00-MM-0001-KXD', $content);
        $this->assertStringContainsString('12 tháng', $content);
        $this->assertStringContainsString('24 tháng', $content);
    }

    public function test_admin_can_import_valid_csv(): void
    {
        // Define CSV content
        $csvContent = "ma_thiet_bi,ten_thiet_bi,to_hien_tai,brand,model,serial,invoice_cd,year,country,stock_in_date,vi_tri_text,ngay_vao_kho,ngay_ra_kho,warranty_period\n" .
                      "MA-999,Máy test import,Tổ Kiểm Thử,BrandTest,ModelTest,S12345,INV-1234,2026,Vietnam,29/07/2023,Khu Test,29/07/2023,30/07/2023,36 tháng";

        // Create dummy CSV file
        $file = UploadedFile::fake()->createWithContent('import_test.csv', $csvContent);

        $response = $this->actingAs($this->admin)
            ->post('/machines/import-csv', [
                'file' => $file,
            ]);

        $response->assertRedirect();
        
        // Assert machine was created in database
        $this->assertDatabaseHas('machines', [
            'ma_thiet_bi' => 'MA-999',
            'ten_thiet_bi' => 'Máy test import',
            'brand' => 'BrandTest',
            'model' => 'ModelTest',
            'serial' => 'S12345',
            'invoice_cd' => 'INV-1234',
            'year' => '2026',
            'country' => 'Vietnam',
            'stock_in_date' => '2023-07-29',
            'vi_tri_text' => 'Khu Test',
            'ngay_vao_kho' => '2023-07-29',
            'ngay_ra_kho' => '2023-07-30',
            'warranty_period' => '36 tháng',
        ]);

        // Assert department was created
        $this->assertDatabaseHas('departments', [
            'name' => 'Tổ Kiểm Thử',
        ]);
    }
}

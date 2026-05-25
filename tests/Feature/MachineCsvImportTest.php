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
        // Assert samples are present
        $this->assertStringContainsString('Máy may JUKI DDL-8700', $content);
    }

    public function test_admin_can_import_valid_csv(): void
    {
        // Define CSV content
        $csvContent = "ma_thiet_bi,ten_thiet_bi,to_hien_tai,brand,model,serial,year,department\n" .
                      "MA-999,Máy test import,Tổ Kiểm Thử,BrandTest,ModelTest,S12345,2026,Khu Test";

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
            'year' => '2026',
            'vi_tri_text' => 'Khu Test',
        ]);

        // Assert department was created
        $this->assertDatabaseHas('departments', [
            'name' => 'Tổ Kiểm Thử',
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AuditTemplate;
use App\Models\AuditCriterion;

class BtpAuditCriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find or create the BTP Audit Template
        $template = AuditTemplate::firstOrCreate(
            ['department_name' => 'BTP'],
            ['name' => 'Chi tiết đánh giá bộ phận BTP (Bán thành phẩm)', 'is_active' => true]
        );

        $criteria = [
            'Sau khi nhận bán thành phẩm đã in/thêu về, có kiểm lại 100% không?',
            'Với các lỗi nhẹ có thể chấp nhận được, có tiêu chuẩn được ký duyệt bởi khách hoặc quản lý QA không?',
            'Có báo số lượng lên cho đơn hàng để cắt bù bán thành phẩm lỗi không? Nếu không cắt bù, có xác nhận giảm số lượng của đơn hàng không?',
            'Trước khi in mác/ép mex/dập mác, có đầy đủ tài liệu, tiêu chuẩn để làm không? (Tài liệu kỹ thuật, mẫu thẻ xanh từ KHSX, dưỡng từ bộ phận kỹ thuật và mẫu tiêu chuẩn từ đơn hàng)',
            'Đối với cắt viền, có tài liệu về canh cắt, mặt trái, mặt phải, lực kéo vải và độ co không?',
            'Có cắt thử viền chuyển chuyền may xác nhận trước khi cắt đại trà không?',
            'Các tài liệu, báo cáo, tiêu chuẩn có đầy đủ thông tin, chữ ký, đóng dấu của bộ phận phụ trách không?',
            'Số liệu, thông tin trên các tài liệu, bảng biểu có đúng và khớp với thực tế không?',
            'Có đầy đủ các SOP của bộ phận và được cập nhật khi có thay đổi không?',
            'Có loại bỏ những tài liệu quá hạn theo quy định về thời gian lưu trữ không?',
        ];

        // Ensure these criteria exist for the BTP template, without duplicating
        foreach ($criteria as $index => $content) {
            AuditCriterion::firstOrCreate([
                'audit_template_id' => $template->id,
                'content' => $content,
            ], [
                'order_num' => $index + 1,
            ]);
        }
        
        $this->command->info("Thêm thành công 10 tiêu chí cho bộ phận BTP.");
    }
}

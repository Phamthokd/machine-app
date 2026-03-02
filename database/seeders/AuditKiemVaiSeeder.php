<?php

namespace Database\Seeders;

use App\Models\AuditCriterion;
use App\Models\AuditTemplate;
use Illuminate\Database\Seeder;

class AuditKiemVaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $template = AuditTemplate::firstOrCreate(
            ['name' => 'Đánh giá bộ phận Kiểm vải'],
            [
                'department_name' => 'Kiểm vải',
                'is_active' => true,
            ]
        );

        $criteria = [
            'Có nhận đủ packing list từ bộ phận kho vải trước khi tiến hành kiểm vải không?',
            'Có rút tỷ lệ kiểm đúng theo Tiêu chuẩn kiểm tra nguyên liệu không?',
            'Có đánh giá điểm đúng theo tiêu chuẩn 4 điểm không?',
            'Có kiểm tra màu sắc cây vải và dyelot tiêu chuẩn của khách không? (Kiểm tra 9 miếng)',
            'Với hàng Muji, có kiểm ánh màu 100% trừ màu Black, Navy không?',
            'Có dán nhãn đỏ cho cây vải lỗi và nhãn xanh cho cây vải đạt tiêu chuẩn để dễ phân biệt không?',
            'Có cắt vải mẫu đầu cây ghi rõ PO/màu sắc/số lot/số lượng để lưu trữ và so sánh màu sắc các cây và với dyelot không?',
            'Có làm báo cáo kiểm vải, ghi đầy đủ thông tin và báo kịp thời các mục không đạt tiêu chuẩn cho đơn hàng khiếu nại nhà cung cấp không?',
            'Những cây vải đạt chất lượng đã được tích OK để kho vải phát hàng cho sản xuất chưa? Những cây vải không đạt, có đánh NG, ghi chú ý lỗi nặng nhất không?',
            'Có đầy đủ các SOP của bộ phận và được cập nhật khi có thay đổi không?',
            'Kế hoạch kiểm vải có được thiết lập và thực hiện?',
            'Các tài liệu, báo cáo, tiêu chuẩn có đầy đủ thông tin, chữ ký, đóng dấu của bộ phận phụ trách không?',
            'Số liệu, thông tin trên các tài liệu, bảng biểu có đúng và khớp với thực tế không?',
            'Có loại bỏ những tài liệu quá hạn theo quy định về thời gian lưu trữ không?',
        ];

        foreach ($criteria as $index => $content) {
            AuditCriterion::firstOrCreate(
                [
                    'audit_template_id' => $template->id,
                    'content' => $content
                ],
                [
                    'order_num' => $index + 1
                ]
            );
        }
    }
}

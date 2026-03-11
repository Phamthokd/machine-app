<?php

namespace Database\Seeders;

use App\Models\AuditCriterion;
use App\Models\AuditTemplate;
use Illuminate\Database\Seeder;

class AuditTheuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $template = AuditTemplate::firstOrCreate(
            ['name' => 'Đánh giá bộ phận Thêu'],
            [
                'department_name' => 'Thêu',
                'is_active' => true,
            ]
        );

        $criteria = [
            'Có đủ tài liệu như tác nghiệp, bảng màu, dưỡng,… khi thêu không?',
            'Có đối chiếu số lượng bán thành phẩm với phiếu xuất không?',
            'Có kiểm tra máy thêu, kim thêu định kỳ không?',
            'Trước khi sản xuất đại trà, có test mẫu không? Mẫu test có được kiểm tra theo mẫu duyệt do đơn hàng xác nhận không?',
            'Bộ phận cắt chỉ hình thêu có báo cáo lại chất lượng hình thêu và ghi chép báo cáo hàng ngày không?',
            'Có đầy đủ các SOP của bộ phận và được cập nhật khi có thay đổi không?',
            'Kế hoạch sản xuất có được thiết lập và thực hiện?',
            'Có trả lại nguyên phụ liệu + BTP về cho kho sau khi sản xuất xong không?',
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

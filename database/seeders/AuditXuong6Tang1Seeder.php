<?php

namespace Database\Seeders;

use App\Models\AuditCriterion;
use App\Models\AuditTemplate;
use Illuminate\Database\Seeder;

class AuditXuong6Tang1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $template = AuditTemplate::firstOrCreate(
            ['name' => 'Đánh giá Xưởng 6 tầng 1'],
            [
                'department_name' => 'Xưởng 6 tầng 1',
                'is_active' => true,
            ]
        );

        $criteria = [
            'Có đầy đủ tài liệu, mẫu trước khi sản xuất không?',
            'Phụ liệu sau khi nhận có được sắp xếp và ghi chép đầy đủ không?',
            'Có đầy đủ mockup các công đoạn may theo quy định không?',
            'Thống kê bộ phận may/là/đóng gói có nhập hàng/giao hàng đúng số lượng và gửi báo cáo theo đúng quy định không?',
            'Tổ trưởng có kiểm tra chất lượng các công đoạn không?',
            'Tổ trưởng có dò kim theo quy định không?',
            'Đổi kim có thực hiện theo quy định không?',
            'Với hàng hóa trả lại từ bộ phận KTHT, chuyền may có kiểm tra và bàn giao lại cho KTHT theo quy định không?',
            'Chuẩn bị trước khi là hàng đại trà có được thực hiện đúng không?',
            'Công nhân là hàng đại trà có thực hiện theo đúng quy định',
            'Tổ trưởng là có thực hiện kiểm tra xác suất theo đúng quy định không?',
            'Đóng gói có đầy đủ tài liệu/mẫu trước khi tiến hành đóng gói không',
            'Phụ liệu đóng gói có được lưu trữ và ghi chép đúng quy định',
            'Sau khi tiếp nhận thành phẩm, có kiểm tra, phân loại theo đúng quy định không?',
            'Kiểm kim thành phẩm và xử lý hàng kim reo có thực hiện đúng quy định không?',
            'Máy kiểm kim có được kiểm tra theo đúng quy định không?',
            'Đóng gói và đóng thùng có thực hiện theo đúng hướng dẫn đóng gói và PL không?',
            'Nguyên phụ liệu sau khi sản xuất xong có trả về cho kho theo đúng quy định không?',
            'Có đầy đủ các SOP của bộ phận và được cập nhật khi có thay đổi không?',
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

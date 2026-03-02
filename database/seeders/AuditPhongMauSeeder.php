<?php

namespace Database\Seeders;

use App\Models\AuditCriterion;
use App\Models\AuditTemplate;
use Illuminate\Database\Seeder;

class AuditPhongMauSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $template = AuditTemplate::firstOrCreate(
            ['name' => 'Đánh giá bộ phận Phòng mẫu'],
            [
                'department_name' => 'Phòng mẫu',
                'is_active' => true,
            ]
        );

        $criteria = [
            'Dưỡng mẫu làm ra có được chủ quản phụ trách ký xác nhận không? Ký lên dưỡng thân trước, bên phải',
            'Có điền đầy đủ, chính xác bảng theo dõi phân công người làm dưỡng không?',
            'Ngay sau khi nhận được tài liệu, nhà cắt có tiến hành lấy vải từ kho để xử lý trước không?',
            'Thời gian làm mẫu có đảm bảo theo đúng quy định không?',
            "Hỏi QC kiểm tra trước là và sau là xem có biết quy trình kiểm bao gồm những mục nào không?\n- Kiểm trước là: So sánh mẫu với tác nghiệp, kiểm tra chi tiết công đoạn, kiểm phụ liệu, riêng đối với hàng Muji đo căng cổ 100%\n- Kiểm sau là: Kiểm tra thông số, kiểm ngoại quan, cần so sánh đối chiếu mẫu may ra với tất cả tài liệu trên",
            'Các báo cáo của QA có được thực hiện đúng theo lưu trình không? Hàng Muji đo độ căng cổ 100%. QA kiểm 100% cấu trúc, cách may, thông số và ngoại quan, chất lượng sản phẩm theo đúng tài liệu và yêu cầu của khách hàng',
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

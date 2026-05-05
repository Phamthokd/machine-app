<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SevenSChecklist;

class SevenSMayLapTrinhSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data for this department
        SevenSChecklist::where('department', 'May lập trình')->delete();

        $items = [
            // S1 & S2 & S3
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 1,
                'content'    => 'Số liệu trên báo cáo chính xác, loại bỏ các báo cáo tài liệu cũ, không liên quan hay chưa ( thu phát nguyên phụ liệu & hóa chất, thu phát dụng cụ sắc nhọn, sổ ghi chép an toàn xưởng, ...)',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 2,
                'content'    => 'Máy móc, nguyên phụ liệu, hàng hóa có được sắp xếp gọn gàng và trong trạng thái sạch sẽ không ( dầu máy, bụi bẩn, băng keo...Hàng hóa phân màu và không để dưới đất,...)',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 3,
                'content'    => 'Lối đi có sạch sẽ không, rác có được phân loại không',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 4,
                'content'    => 'Phòng cắt dưỡng may lập trình có để gọn gàng không? Ghi chép đầy đủ dụng cụ làm việc, biểu ghi chép dưỡng đã đầy đủ thông tin chưa?',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 5,
                'content'    => 'Phòng sửa máy tài liệu ,đồ dùng cá nhân, vật dụng sửa chữa đã để gọn gàng không ?',
            ],

            // S4 & S5
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 6,
                'content'    => 'Các báo cáo, tài liệu cũ rách, hỏng hóc có được thay thế chưa',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 7,
                'content'    => 'Toàn bộ công nhân viên có tuân thủ theo quy định chưa ( Đeo gang tay đối với hàng sáng màu, dụng cụ sắc nhọn phải được cố định, ...)',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 8,
                'content'    => 'Dụng cụ có đủ mã CNV chưa, máy móc thiết bị có đủ mã tài sản chưa, bảng ghi chép bảo dưỡng máy móc có đầy đủ không',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 9,
                'content'    => 'Thước dây hoặc các thiết bị khác đã được hiệu chuẩn đúng thời hạn',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 10,
                'content'    => 'Mockup đã được cập nhật, đóng dấu,đầy đủ chữ ký của QA? Biểu kiểm tra chất lượng máy may đã được kiểm tra?',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 11,
                'content'    => 'Máy lập trình có biểu ghi chép kiểm tra máy thường xuyên không? Người vận hành máy có đúng with thực tế phụ trách máy đó?',
            ],

            // S6 & S7
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 12,
                'content'    => 'Lối đi, thiết bị phòng cháy có bị chắn không',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 13,
                'content'    => 'Danh sách nhân viên thu phát hóa chất có được cập nhật kịp thời',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 14,
                'content'    => 'Có vật dụng cá nhân bằng kim loại trong xưởng không',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 15,
                'content'    => 'Dây điện, dây hơi có nguy cơ tiềm ẩn nào không?',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 16,
                'content'    => 'Máy móc, điện, quạt...có được tắt khi không sử dụng không? Có dùng điện ngoài mục đích sản xuất không?(ấm đun nước,,)',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 17,
                'content'    => 'Nguyên phụ liệu sử dụng cho mã hàng trước đã được trả lại kho chưa',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 18,
                'content'    => 'Mái nhà xưởng có tình trạng rột nước không? Quạt điện để tránh gần với hàng hóa hay không?',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 19,
                'content'    => 'Có sử dụng tấm chắn kim khi thao tác trên máy may lập trình không?',
            ],
        ];

        foreach ($items as $item) {
            SevenSChecklist::create([
                'department' => 'May lập trình',
                'section'    => $item['section'],
                'sort_order' => $item['sort_order'],
                'content'    => $item['content'],
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SevenSChecklist;

class SevenSIESeeder extends Seeder
{
    public function run(): void
    {
        // Xóa toàn bộ dữ liệu cũ để cập nhật lại sạch
        SevenSChecklist::where('department', 'IE')->delete();

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
                'content'    => 'Không để vật dụng cá nhân (điện thoại, túi xách, đồ ăn...) trên bàn hoặc các vị trí trong chuyền',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 3,
                'content'    => 'Lối đi có sạch sẽ không, rác có được phân loại không',
            ],

            // S4 & S5
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 4,
                'content'    => 'Các báo cáo, tài liệu cũ rách, hỏng hóc có được thay thế chưa',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 5,
                'content'    => 'Toàn bộ công nhân viên có tuân thủ theo quy định chưa (Đeo thẻ, áo đồng phục, dụng cụ làm việc có mã công nhân,...)',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 6,
                'content'    => 'Định mức chỉ trên ERP đã cập nhật chưa? (mã mới, hoặc thay đổi) định mức trên ERP có khớp với excel theo dõi không? Bảng Quy trình công đoạn có chữ kí của quản lí xưởng hay không?',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 7,
                'content'    => 'Tính sản lượng mục tiêu công đoạn chuyền may đã cập nhật trên hệ thống chưa? Nếu có thay đổi đã sửa chưa?',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 8,
                'content'    => 'Tính đơn giá sản phẩm từng công đoạn có đúng ko ?',
            ],

            // S6 & S7
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 9,
                'content'    => 'Lối đi, thiết bị phòng cháy có bị chắn không',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 10,
                'content'    => 'Có vật dụng cá nhân bằng kim loại trong xưởng không',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 11,
                'content'    => 'Thiết bị PCCC có được kiểm tra đầy đủ không?',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 12,
                'content'    => 'Máy móc có đầy đủ SOP, cảnh báo bằng Tiếng việt không? SOP, cảnh báo có bị mờ rách không?',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 13,
                'content'    => 'Hàng hóa, nguyên phụ liệu có để gần tủ điện, công tắc điện không? (tối thiểu 0,5m)',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 14,
                'content'    => 'Các tủ điện có cảnh báo nguy hiểm điện giật không? Có thảm cách điện không?',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 15,
                'content'    => 'Máy móc, điện, quạt...có được tắt khi không sử dụng không',
            ],
        ];

        foreach ($items as $item) {
            SevenSChecklist::create([
                'department' => 'IE',
                'section'    => $item['section'],
                'sort_order' => $item['sort_order'],
                'content'    => $item['content'],
            ]);
        }
    }
}

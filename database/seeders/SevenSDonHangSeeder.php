<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SevenSChecklist;

class SevenSDonHangSeeder extends Seeder
{
    public function run(): void
    {
        // Xóa toàn bộ dữ liệu cũ để cập nhật lại sạch
        SevenSChecklist::where('department', 'Đơn hàng')->delete();

        $items = [
            // S1 & S2 & S3
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 1,
                'content'    => 'Số liệu trên báo cáo chính xác chưa (báo cáo thu phát áo mẫu,...), loại bỏ mẫu vải, phụ liệu cũ hoặc đã hủy đơn chưa',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 2,
                'content'    => 'Bàn làm việc có được sắp xếp gọn gàng và trong trạng thái sạch sẽ không',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 3,
                'content'    => 'Lối đi, khu vực làm việc có sạch sẽ không, không để giấy tờ, vật dụng cá nhân lên xến trên bàn',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 4,
                'content'    => 'Mẫu vải/phụ liệu có tem nhãn, phân loại theo mã đơn hàng hoặc khách hàng',
            ],

            // S4 & S5
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 5,
                'content'    => 'Các báo cáo, tài liệu quan trọng có được quản lí cẩn thận không',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 6,
                'content'    => 'Toàn bộ công nhân viên có tuân thủ theo quy định chưa ( Đeo thẻ nhân viên, mặc đồng phục, đi làm đúng giờ ...)',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 7,
                'content'    => 'Có duy trì thói quen dọn dẹp, sắp xếp mỗi ngày cuối giờ',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 8,
                'content'    => 'Có cập nhật hệ thống tiến độ mẫu theo đúng quy định không',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 9,
                'content'    => 'Có cập nhật bảng tiến độ đơn hàng đúng thời hạn và đúng biểu mẫu chung',
            ],

            // S6 & S7
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 10,
                'content'    => 'Hàng hóa có để chắn lối đi, thiết bị phòng cháy không',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 11,
                'content'    => 'Máy móc, điện, quạt...có được tắt khi không sử dụng không',
            ],
        ];

        foreach ($items as $item) {
            SevenSChecklist::create([
                'department' => 'Đơn hàng',
                'section'    => $item['section'],
                'sort_order' => $item['sort_order'],
                'content'    => $item['content'],
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SevenSChecklist;

class SevenSXnkSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // S1&S2&S3 — Sàng lọc, Sắp xếp, Sạch sẽ
            [
                'section' => 'S1&S2&S3 — Sàng lọc, Sắp xếp, Sạch sẽ',
                'sort_order' => 1,
                'content' => 'Số liệu trên báo cáo chính xác, loại bỏ các báo cáo tài liệu cũ, không liên quan hay chưa'
            ],
            [
                'section' => 'S1&S2&S3 — Sàng lọc, Sắp xếp, Sạch sẽ',
                'sort_order' => 2,
                'content' => 'Lưu trình khai báo hải quan, làm thủ tục khai báo gia công lại, Nhập khẩu, Xuất khẩu, Xin CO, Báo cáo cân bằng hải quan, Tính định mức XK, 
Kiểm soát mã liệu nhập khẩu, Thêm mã phụ liệu mới có thực hiện đúng lưu trình chưa'
            ],

            // S4&S5 — Săn sóc, Sẵn sàng
            [
                'section' => 'S4&S5 — Săn sóc, Sẵn sàng',
                'sort_order' => 3,
                'content' => 'Các báo cáo, tài liệu cũ rách, hỏng hóc có được thay thế chưa'
            ],
            [
                'section' => 'S4&S5 — Săn sóc, Sẵn sàng',
                'sort_order' => 4,
                'content' => 'Toàn bộ công nhân viên có tuân thủ theo quy định chưa ( Mặc đồng phục, đeo thẻo NV đúng quy định…'
            ],
            [
                'section' => 'S4&S5 — Săn sóc, Sẵn sàng',
                'sort_order' => 5,
                'content' => 'Báo cáo cân bằng hải quan có chính xác không'
            ],

            // S6&S7 — An toàn, Tích cực
            [
                'section' => 'S6&S7 — An toàn, Tích cực',
                'sort_order' => 6,
                'content' => 'Có vật dụng nguy hiểm trong khu vực làm việc không'
            ],
            [
                'section' => 'S6&S7 — An toàn, Tích cực',
                'sort_order' => 7,
                'content' => 'In tài liệu nhưng không lấy, tạo lệnh in trên máy in rất nhiều nhưng không in ra để sử dụng dẫn đến quá tải'
            ],
            [
                'section' => 'S6&S7 — An toàn, Tích cực',
                'sort_order' => 8,
                'content' => 'Tái chế giấy 1  mặt'
            ],
        ];

        foreach ($items as $item) {
            SevenSChecklist::firstOrCreate(
                ['department' => 'XNK', 'section' => $item['section'], 'sort_order' => $item['sort_order']],
                ['content' => $item['content']]
            );
        }
    }
}

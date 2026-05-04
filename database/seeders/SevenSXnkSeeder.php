<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SevenSChecklist;

class SevenSXnkSeeder extends Seeder
{
    public function run(): void
    {
        // Xóa toàn bộ dữ liệu cũ của XNK (kể cả dữ liệu có nội dung VI/CN lẫn lộn)
        SevenSChecklist::where('department', 'XNK')->delete();

        $items = [
            // S1 & S2 & S3
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 1,
                'content'    => 'Số liệu trên báo cáo chính xác, loại bỏ các báo cáo tài liệu cũ, không liên quan hay chưa',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 2,
                'content'    => 'Lưu trình khai báo hải quan, làm thủ tục khai báo gia công lại, Nhập khẩu, Xuất khẩu, Xin CO, Báo cáo cân bằng hải quan, Tính định mức XK, Kiểm soát mã liệu nhập khẩu, Thêm mã phụ liệu mới có thực hiện đúng lưu trình chưa',
            ],

            // S4 & S5
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 3,
                'content'    => 'Các báo cáo, tài liệu cũ rách, hỏng hóc có được thay thế chưa',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 4,
                'content'    => 'Toàn bộ công nhân viên có tuân thủ theo quy định chưa ( Mặc đồng phục, đeo thẻ NV đúng quy định....',
            ],

            // S6 & S7
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 5,
                'content'    => 'Máy móc, điện, quạt...có được tắt khi không sử dụng không',
            ],
        ];

        foreach ($items as $item) {
            SevenSChecklist::create([
                'department' => 'XNK',
                'section'    => $item['section'],
                'sort_order' => $item['sort_order'],
                'content'    => $item['content'],
            ]);
        }
    }
}

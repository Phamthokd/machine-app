<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SevenSChecklist;

class SevenSQAKTDCFQCSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data for this department
        SevenSChecklist::where('department', 'QA, KTDC, FQC')->delete();

        $items = [
            // S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 1,
                'content'    => 'Số liệu trên báo cáo chính xác, loại bỏ các báo cáo tài liệu cũ, không liên quan hay chưa ( thu phát nguyên phụ liệu & hóa chất, thu phát dụng cụ sắc nhọn, sổ ghi chép an toàn xưởng, ...)',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 2,
                'content'    => 'Khu vực kiểm hàng không có vật dụng không liên quan.',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 3,
                'content'    => 'Lối đi có sạch sẽ không, rác có được phân loại không',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 4,
                'content'    => 'Mẫu kiểm, mẫu chuẩn được để riêng, có nhãn nhận dạng.',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 5,
                'content'    => 'Không để hàng lỗi lẫn với hàng đạt.',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 6,
                'content'    => 'Hàng đạt, chờ kiểm, chờ đóng gói tách biệt, có phân cách',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 7,
                'content'    => 'Hàng hóa đặt ngay ngắn, phân màu sắc, không chồng cao hoặc để dưới đất.',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 8,
                'content'    => 'Bảng tin tại bộ phận có được sắp xếp theo tiêu chuẩn',
            ],

            // S4 & S5 : Săn sóc & sẵn sàng
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 9,
                'content'    => 'Các báo cáo, tài liệu cũ rách, hỏng hóc có được thay thế chưa',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 10,
                'content'    => 'Toàn bộ công nhân viên có tuân thủ theo quy định chưa ( Đeo gang tay đối với hàng sáng màu, dụng cụ sắc nhọn phải được cố định, ...)',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 11,
                'content'    => 'Dụng cụ có hiệu chuẩn đầy đủ chưa',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 12,
                'content'    => 'Dụng cụ có đủ mã CNV chưa, máy móc thiết bị có đủ mã tài sản chưa, bảng ghi chép bảo dưỡng máy móc có đầy đủ không',
            ],

            // S6 & S7 : An toàn & Tiết kiệm
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 13,
                'content'    => 'Lối đi, thiết bị phòng cháy có bị chắn không',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 14,
                'content'    => 'Có vật dụng cá nhân bằng kim loại trong xưởng không',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 15,
                'content'    => 'Thiết bị PCCC có được kiểm tra đầy đủ không?',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 16,
                'content'    => 'Máy móc có đầy đủ SOP, cảnh báo bằng Tiếng việt không? SOP, cảnh báo có bị mờ rách không?',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 17,
                'content'    => 'Hàng hoá, nguyên phụ liệu có để gần tủ điện, công tắc điện không? (tối thiểu 0,5m)',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 18,
                'content'    => 'Các tủ điện có cảnh báo nguy hiểm điện giật không? Có thảm cách điện không?',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 19,
                'content'    => 'Máy móc, điện, quạt...có được tắt khi không sử dụng không',
            ],
        ];

        foreach ($items as $item) {
            SevenSChecklist::create([
                'department' => 'QA, KTDC, FQC',
                'section'    => $item['section'],
                'sort_order' => $item['sort_order'],
                'content'    => $item['content'],
            ]);
        }
    }
}

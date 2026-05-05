<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SevenSChecklist;

class SevenSXuong6Tang1Seeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data for this department to avoid duplicates
        SevenSChecklist::where('department', 'Xưởng 6 tầng 1')->delete();

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
                'content'    => 'Lối đi có sạch sẽ không',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 4,
                'content'    => 'Người vận hành ngồi 1 ghế, không ngồi chung, không lấn vạch thoát hiểm',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 5,
                'content'    => 'Thùng rác để đúng vị trí quy định, không để đồ dùng cá nhân trong thùng rác.',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 6,
                'content'    => 'BTP sắp xếp lộn xộn, nhiều chi tiết/ cỡ khác nhau',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 7,
                'content'    => 'để hàng trong các xe đẩy quá cao',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 8,
                'content'    => 'Gá dưỡng/ mẫu sắp xếp không gọn gàng ngăn nắp/ gá mã cũ không trả lại nơi cung cấp',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 9,
                'content'    => 'đồ cá nhân, đồ ăn phải được để đúng nơi quy định (tủ đồ), không để trong gầm bàn máy, tủ điện, trên mặt bàn làm việc và các khoang giá để hàng',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 10,
                'content'    => 'Máy móc, quạt trần, máng điện, sàn nhà phải được vệ sinh sạch sẽ, vệ sinh định kì theo quy định, không có chỉ tại chân máy, chân ghế ngồi',
            ],
            [
                'section'    => 'S1 & S2 & S3 : Sắp xếp & sàng lọc, sạch sẽ',
                'sort_order' => 11,
                'content'    => 'Bảng tin tại bộ phận có được sắp xếp theo tiêu chuẩn',
            ],

            // S4 & S5
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 12,
                'content'    => 'Các báo cáo, tài liệu cũ rách, hỏng hóc có được thay thế chưa',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 13,
                'content'    => 'Toàn bộ công nhân viên có tuân thủ theo quy định chưa (Đeo gang tay đối với hàng sáng màu, dụng cụ sắc nhọn phải được cố định, ...)',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 14,
                'content'    => 'Dụng cụ có đủ mã CNV chưa, máy móc thiết bị có đủ mã tài sản chưa, bảng ghi chép bảo dưỡng máy móc có đầy đủ không',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 15,
                'content'    => 'Có tuân thủ quy định kiểm soát kim loại không',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 16,
                'content'    => 'Công nhân may có làm theo đúng quy định vệ sinh/kiểm tra máy móc sạch sẽ trước khi làm hàng',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 17,
                'content'    => 'Các công đoạn đều có đầy đủ mockup',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 18,
                'content'    => 'Bộ phận là có làm đầy đủ báo cáo trước là và sau là không',
            ],
            [
                'section'    => 'S4 & S5: Săn sóc & sẵn sàng',
                'sort_order' => 19,
                'content'    => 'Đóng gói có đầy đủ mẫu gấp gói được duyệt không',
            ],

            // S6 & S7
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 20,
                'content'    => 'Lối đi, thiết bị phòng cháy có bị chắn không',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 21,
                'content'    => 'Hóa chất có được quản lý thu phát đúng không',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 22,
                'content'    => 'Thiết bị PCCC có được kiểm tra đầy đủ không?',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 23,
                'content'    => 'Máy móc có đầy đủ SOP, cảnh báo bằng Tiếng việt không? SOP, cảnh báo có bị mờ rách không?',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 24,
                'content'    => 'Hàng hóa, nguyên phụ liệu có để gần tủ điện, công tắc điện không? (tối thiểu 0,5m)',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 25,
                'content'    => 'Các tủ điện có cảnh báo nguy hiểm điện giật không? Có thảm cách điện không?',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 26,
                'content'    => 'Công nhân có sử dụng bảo hộ lao động khi làm việc không?',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 27,
                'content'    => 'Máy móc, điện, quạt...có được tắt khi không sử dụng không',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 28,
                'content'    => 'Nguyên phụ liệu sử dụng cho mã hàng trước đã được trả lại kho chưa',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 29,
                'content'    => 'Hóa chất chứa trong chai nhựa quy định có hiển thị Tên hóa chất, ID + tên công nhân được sử dụng.',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 30,
                'content'    => 'Không sử dụng kim khâu tay trên chuyền may',
            ],
            [
                'section'    => 'S6 & S7: An toàn & Tiết kiệm',
                'sort_order' => 31,
                'content'    => 'Không sạc điện thoại, sử dụng điện thoại chơi game, nghe nhạc, đeo tai nghe tại nơi làm việc, không nhận để điện thoại trên bàn máy',
            ],
        ];

        foreach ($items as $item) {
            SevenSChecklist::create([
                'department' => 'Xưởng 6 tầng 1',
                'section'    => $item['section'],
                'sort_order' => $item['sort_order'],
                'content'    => $item['content'],
            ]);
        }
    }
}

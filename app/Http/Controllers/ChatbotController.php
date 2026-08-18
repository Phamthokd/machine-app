<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Machine;
use App\Models\RepairTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function chatBBB(Request $request)
    {
        $request->validate([
            'message'        => 'required|string|max:2000',
            'history'        => 'nullable|array',
            'history.*.role' => 'in:user,model,assistant',
            'history.*.parts' => 'nullable|array',
            'provider'       => 'nullable|string|in:auto,gemini,openai,local',
        ]);

        $userMessage = trim($request->input('message'));
        $provider = $request->input('provider', 'auto');
        $history = $request->input('history', []);

        // Prepare system context
        $systemPrompt = $this->getSystemPrompt();

        // Tier 1 / Selected Provider
        if ($provider === 'openai' || ($provider === 'auto' && config('services.openai.key'))) {
            $reply = $this->callOpenAI($userMessage, $systemPrompt, $history);
            if ($reply) {
                return response()->json(['reply' => $reply, 'provider' => 'openai']);
            }
        }

        if ($provider === 'gemini' || ($provider === 'auto' && config('services.gemini.key'))) {
            $reply = $this->callGemini($userMessage, $systemPrompt, $history);
            if ($reply) {
                return response()->json(['reply' => $reply, 'provider' => 'gemini']);
            }
        }

        // Fallback / Local Rule Engine
        $reply = $this->callLocalEngine($userMessage);
        return response()->json(['reply' => $reply, 'provider' => 'local']);
    }

    private function getSystemPrompt(): string
    {
        $totalMachines = Machine::count();
        $pendingRepairs = RepairTicket::where('status', 'pending')->count();
        $inProgressRepairs = RepairTicket::where('status', 'in_progress')->count();
        $totalCandidates = Candidate::count();

        return <<<PROMPT
Bạn là **VIVA Assistant** - Trợ lý AI thông minh tích hợp trong Hệ thống Quản lý Thiết bị & Ứng viên VIVA (Công ty TNHH May Mặc Việt Thiên / 富华制衣产品有限公司).

## BỐI CẢNH DỮ LIỆU HIỆN TẠI:
- Tổng số thiết bị trong hệ thống: {$totalMachines} máy
- Số phiếu sửa chữa chờ xử lý: {$pendingRepairs}
- Số phiếu đang sửa chữa: {$inProgressRepairs}
- Tổng số ứng viên phỏng vấn đã đăng ký: {$totalCandidates}

## CÁC TÍNH NĂNG CHÍNH CỦA HỆ THỐNG:

### 1. QUẢN LÝ THIẾT BỊ MÁY MÓC
- Quản lý danh sách thiết bị (/machines)
- Quét mã QR dán trên máy để xem thông tin, vị trí, lịch sử di chuyển và lịch sử sửa chữa
- Di chuyển máy giữa các bộ phận (/machines/movement)
- Nhập dữ liệu hàng loạt bằng CSV/Excel (/machines/import-csv)

### 2. PHIẾU YÊU CẦU SỬA CHỮA (CƠ ĐIỆN)
- Tạo phiếu sửa chữa (/repairs/create)
- Tiếp nhận và xử lý phiếu (/repairs/requests)
- Xem lịch sử sửa chữa (/repairs/history)
- Phê duyệt báo cáo hoàn thành (/repairs/approvals)

### 3. PHIẾU YÊU CẦU CÔNG TRÌNH & BOK
- Tạo phiếu yêu cầu Công trình (sửa điện, nước, hạ tầng)
- Tạo phiếu BOK (sửa chữa thiết bị phụ trợ)
- Xem và xử lý danh sách phiếu BOK (/repairs/bok-index)

### 4. SỬA CHỮA IT
- Báo sự cố máy tính, mạng, phần mềm IT (/it-repairs/create)
- Quản lý danh sách sự cố IT (/it-repairs)

### 5. KIỂM TRA 7S VÀ BÁO CÁO MÔI TRƯỜNG
- Đánh giá 7S theo từng bộ phận (/seven-s/create)
- Đánh giá 7S Kho thành phẩm, Phòng mẫu, Kiểm vải, Nhãn quyền
- Xem lịch sử 7S (/seven-s) và kế hoạch cải thiện
- Báo cáo môi trường / an toàn lao động (/environment-reports)

### 6. QUẢN LÝ ỨNG VIÊN PHỎNG VẤN (CANDIDATES)
- **Trang công khai**: `/apply` (không cần đăng nhập, ứng viên điền phiếu ứng tuyển)
- **Trang quản lý**: `/candidates` (Dành cho Admin, HR, Senior Manager)
- **Quy trình xử lý phiếu ứng tuyển**:
  1. Ứng viên quét mã QR hoặc truy cập `/apply` → Điền thông tin phiếu ứng tuyển (hỗ trợ Tiếng Việt, Tiếng Trung, Tiếng Anh).
  2. Ứng viên có thể chỉnh sửa lại phiếu vừa điền trong vòng 30 phút (trước khi HR chuyển đơn).
  3. HR và tài khoản phân quyền xem danh sách phiếu phỏng vấn được tạo trong vòng 1 tiếng gần nhất.
  4. HR xem danh sách → Chuyển đơn phỏng vấn tới Quản lý cao cấp (Senior Manager).
  5. Quản lý cao cấp nhận xét + điền kết quả tuyển dụng (Đồng ý / Không tuyển / Chờ xem xét) + các thông tin: Lương đề xuất, Ngày bắt đầu, Thời gian thử việc, Bộ phận phân công, Ghi chú thêm.
  6. In phiếu phỏng vấn / Xuất file để lưu trữ hồ sơ (/candidates/{id}/print).

### 7. PHÂN QUYỀN NGƯỜI DÙNG
- **admin**: Toàn quyền hệ thống
- **warehouse**: Quản lý kho máy móc
- **repair_tech**: Thợ cơ điện sửa máy
- **bok**: Nhận và xử lý phiếu BOK
- **contractor**: Nhận và xử lý phiếu Công trình
- **it_staff**: Xử lý sự cố IT
- **team_leader**: Tổ trưởng di chuyển máy, báo hỏng máy
- **hr**: Quản lý phiếu phỏng vấn ứng viên, chuyển đơn cho quản lý
- **senior_manager**: Đánh giá & duyệt kết quả ứng viên phỏng vấn được chuyển

### 8. NGÔN NGỮ & GIAO DIỆN
- Hỗ trợ 3 ngôn ngữ: 🇻🇳 Tiếng Việt (VN), 🇨🇳 Tiếng Trung (CN), 🇬🇧 Tiếng Anh (EN)
- Chuyển đổi ngôn ngữ bằng nút chuyển đổi trên thanh điều hướng

## HƯỚNG DẪN TRẢ LỜI:
- Trả lời rõ ràng, thân thiện bằng tiếng Việt (hoặc theo ngôn ngữ người dùng hỏi).
- Đưa ra đường dẫn cụ thể (URL) khi hướng dẫn người dùng tới trang tương ứng.
- Sử dụng format Markdown và Emoji sinh động để người dùng dễ đọc.
PROMPT;
    }

    private function callOpenAI(string $message, string $systemPrompt, array $history): ?string
    {
        $apiKey = config('services.openai.key');
        if (!$apiKey) return null;

        try {
            $messages = [['role' => 'system', 'content' => $systemPrompt]];

            foreach ($history as $h) {
                $role = $h['role'] === 'model' || $h['role'] === 'assistant' ? 'assistant' : 'user';
                $content = $h['parts'][0]['text'] ?? $h['content'] ?? '';
                if ($content) {
                    $messages[] = ['role' => $role, 'content' => $content];
                }
            }

            $messages[] = ['role' => 'user', 'content' => $message];

            $response = Http::withToken($apiKey)
                ->timeout(10)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'       => config('services.openai.model', 'gpt-4o-mini'),
                    'messages'    => $messages,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }
        } catch (\Exception $e) {
            Log::warning('OpenAI API Call Failed: ' . $e->getMessage());
        }

        return null;
    }

    private function callGemini(string $message, string $systemPrompt, array $history): ?string
    {
        $apiKey = config('services.gemini.key');
        if (!$apiKey) return null;

        try {
            $contents = [];
            foreach ($history as $h) {
                $role = $h['role'] === 'user' ? 'user' : 'model';
                $text = $h['parts'][0]['text'] ?? $h['content'] ?? '';
                if ($text) {
                    $contents[] = [
                        'role'  => $role,
                        'parts' => [['text' => $text]],
                    ];
                }
            }
            $contents[] = [
                'role'  => 'user',
                'parts' => [['text' => $message]],
            ];

            $response = Http::timeout(10)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}",
                [
                    'contents'          => $contents,
                    'systemInstruction' => [
                        'parts' => [['text' => $systemPrompt]],
                    ],
                ]
            );

            if ($response->successful()) {
                return $response->json('candidates.0.content.parts.0.text');
            }
        } catch (\Exception $e) {
            Log::warning('Gemini API Call Failed: ' . $e->getMessage());
        }

        return null;
    }

    private function callLocalEngine(string $message): string
    {
        $lower = mb_strtolower($message, 'UTF-8');

        if (str_contains($lower, 'ứng viên') || str_contains($lower, 'phỏng vấn') || str_contains($lower, 'tuyển dụng') || str_contains($lower, 'phiếu phỏng vấn') || str_contains($lower, 'ứng tuyển')) {
            return "📋 **Quản lý Ứng viên Phỏng vấn (Candidates)**:\n\n" .
                "• **Ứng viên nộp phiếu phỏng vấn**: Truy cập [Trang điền phiếu ứng tuyển](/apply).\n" .
                "• **Chỉnh sửa phiếu vừa điền**: Ứng viên có thể sửa thông tin trong vòng **30 phút** trước khi HR chuyển đơn.\n" .
                "• **HR & Quản lý xem phiếu**: Truy cập [Danh sách ứng viên](/candidates). Quyền tạo phiếu được xem trong vòng **1 tiếng**.\n" .
                "• **Quản lý cấp cao duyệt phiếu**: Nhận xét kết quả tuyển dụng (Đồng ý/Không tuyển), điền lương đề xuất, ngày bắt đầu và bộ phận phân công.";
        }

        if (str_contains($lower, 'sửa máy') || str_contains($lower, 'báo hỏng') || str_contains($lower, 'sự cố')) {
            return "🔧 **Hướng dẫn Báo sự cố / Sửa máy**:\n\n" .
                "• Báo hỏng máy may / thiết bị xưởng: [Tạo phiếu sửa chữa](/repairs/create)\n" .
                "• Báo sự cố máy tính / IT: [Tạo phiếu sửa IT](/it-repairs/create)\n" .
                "• Báo sự cố điện nước / công trình: [Tạo phiếu Công trình](/repairs/create?type=contractor)\n" .
                "• Tiếp nhận & Xử lý phiếu: [Danh sách phiếu yêu cầu](/repairs/requests)";
        }

        if (str_contains($lower, '7s') || str_contains($lower, 'môi trường') || str_contains($lower, 'kiểm tra')) {
            return "🧹 **Chức năng 7S & Báo cáo Môi trường**:\n\n" .
                "• Thực hiện đánh giá 7S: [Tạo phiếu kiểm tra 7S](/seven-s/create)\n" .
                "• Xem lịch sử & Kế hoạch cải thiện: [Lịch sử 7S](/seven-s)\n" .
                "• Báo cáo sự cố an toàn / môi trường: [Báo cáo môi trường](/environment-reports)";
        }

        if (str_contains($lower, 'máy') || str_contains($lower, 'thiết bị') || str_contains($lower, 'di chuyển')) {
            return "🏭 **Quản lý Máy móc & Thiết bị**:\n\n" .
                "• Xem danh sách thiết bị: [Danh sách máy](/machines)\n" .
                "• Quét mã QR trên thiết bị để xem thông tin và lịch sử.\n" .
                "• Di chuyển máy giữa các bộ phận: [Lịch sử di chuyển](/machines/movement)\n" .
                "• Nhập máy từ file CSV/Excel: [Nhập dữ liệu](/machines/import-csv)";
        }

        return "🤖 **VIVA Assistant**: Em có thể giúp anh/chị tra cứu thông tin hệ thống, quản lý máy móc, tạo phiếu sửa chữa, báo cáo 7S và **Quản lý phiếu ứng viên phỏng vấn**.\n\n" .
            "Anh/Chị có thể hỏi em các nội dung như:\n" .
            "• *Quy trình nộp và duyệt phiếu phỏng vấn ứng viên là gì?*\n" .
            "• *Làm sao để tạo phiếu báo hỏng máy?*\n" .
            "• *Hướng dẫn thực hiện kiểm tra 7S?*";
    }
}

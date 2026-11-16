# Dự Án Android WebView — Viva MachineApp

Dự án ứng dụng Android Native hoàn chỉnh dùng để đóng gói hệ thống Web App thành file cài đặt APK độc lập cho máy tính bảng và điện thoại Android của nhà máy.

---

## 🚀 Tính Năng Nổi Bật Được Tích Hợp Sẵn:
1. **Truy cập Camera & Quét mã QR**: Cấu hình sẵn quyền Camera (`WebChromeClient.onPermissionRequest` và FileChooser) phục vụ quét mã QR thiết bị, chụp ảnh báo hỏng IT, chụp ảnh kiểm tra 7S, ảnh hồ sơ ứng viên.
2. **Chọn ảnh & Tải tệp lên**: Hỗ trợ đầy đủ `<input type="file">` mở cả Camera trực tiếp lẫn Thư viện ảnh.
3. **Kéo xuống để tải lại (Pull to Refresh)**: Sử dụng `SwipeRefreshLayout` vuốt từ trên xuống để cập nhật lại trang web.
4. **Xử lý phím Quay lại (Back Button)**: Nhấn nút Back trên điện thoại sẽ lùi trang web trước khi nhấn đúp để thoát app.
5. **Hỗ trợ HTTP & HTTPS (Cleartext Traffic)**: Đã cấu hình `network_security_config.xml` cho phép truy cập cả mạng nội bộ IP lẫn domain HTTPS.
6. **Không quảng cáo, không phụ thuộc bên thứ 3**: Mã nguồn 100% của bạn, an toàn và bảo mật thông tin nội bộ công ty.

---

## 🛠️ Hướng Dẫn Mở & Xuất File APK Bằng Android Studio:

### Bước 1: Mở dự án trong Android Studio
1. Mở phần mềm **Android Studio** trên máy tính (nếu chưa có, tải tại: [developer.android.com/studio](https://developer.android.com/studio)).
2. Chọn **Open** (hoặc **File > Open**).
3. Trỏ tới thư mục:
   `c:\Users\thopx\Desktop\QR code\machine-app\android`
4. Chờ 1–2 phút để Android Studio tải Gradle và đồng bộ thư viện (Sync Project).

### Bước 2: Đổi URL website (Nếu cần)
- Nếu muốn đổi địa chỉ web trỏ tới máy chủ khác, mở file:
  `app > res > values > strings.xml`
- Sửa giá trị tại thẻ `<string name="web_url">`:
  ```xml
  <string name="web_url">https://vivagarment.com.vn</string>
  ```

### Bước 3: Xuất file APK (Chỉ 1 click)
1. Trên thanh menu trên cùng của Android Studio, chọn:
   **Build ➔ Build Bundle(s) / APK(s) ➔ Build APK(s)**
2. Sau khi build xong (khoảng 30 giây), ở góc dưới bên phải màn hình sẽ hiện thông báo:
   *`APK(s) generated successfully for module 'app'`*
3. Bấm vào chữ **locate** màu xanh trên thông báo đó để mở thư mục chứa file APK:
   Đường dẫn file: `android\app\build\outputs\apk\debug\app-debug.apk`

---

## 📱 Cài Đặt Lên Điện Thoại / Máy Tính Bảng:
1. Đổi tên file `app-debug.apk` thành `VivaMachineApp.apk` (tùy ý).
2. Gửi file qua Zalo, USB hoặc Google Drive vào máy Android.
3. Bấm cài đặt và chọn **Cho phép cài đặt từ nguồn này**.
4. Khi mở app lần đầu, chọn **Cho phép quyền Camera** để quét mã QR và chụp ảnh.


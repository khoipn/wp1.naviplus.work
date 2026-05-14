# Hướng dẫn cài đặt và sử dụng Navi+ Menu Builder trên WordPress (tối ưu mobile)

**Tên đầy đủ:** Naviplus Menu Builder  
**Tên gọn trong tài liệu:** Navi+ Menu Builder (Navi+)

Plugin chính thức trên WordPress.org: [Naviplus Menu Builder — tải và cài đặt](https://wordpress.org/plugins/naviplus-menu-builder/)

Tài liệu này giúp bạn cài plugin, bật menu toàn site hoặc nhúng theo vị trí, và tận dụng các layout phù hợp **điện thoại** (thanh tab, menu trượt / hamburger, nút nổi).

---

## 1. Yêu cầu

- WordPress **5.8 trở lên** (khuyến nghị dùng phiên bản WordPress mới nhất mà host của bạn hỗ trợ).
- Quyền quản trị để cài plugin và chỉnh **Giao diện (Appearance)**.
- Kết nối internet: menu được thiết kế trên dịch vụ Navi+ và hiển thị qua script tải từ `https://live.naviplus.app/start.js` (xem mục [Dịch vụ & quyền riêng tư](#8-dịch-vụ-bên-ngoài--quyền-riêng-tư)).

---

## 2. Cài đặt plugin

### Cách A — Cài từ thư viện plugin WordPress (khuyến nghị)

1. Đăng nhập **Quản trị WordPress**.
2. Vào **Plugin → Cài mới**.
3. Tìm **“Naviplus Menu Builder”** hoặc **“Navi+”**.
4. Chọn **Cài đặt**, sau đó **Kích hoạt**.

Trang plugin trên WordPress.org (mô tả, phiên bản, changelog):  
https://wordpress.org/plugins/naviplus-menu-builder/

### Cách B — Tải file ZIP từ WordPress.org

1. Trên trang plugin WordPress.org, chọn **Download** để tải file `.zip`.
2. Trong WordPress: **Plugin → Cài mới → Tải plugin lên**.
3. Chọn file ZIP vừa tải, **Cài đặt**, rồi **Kích hoạt**.

### Cách C — Tải lên qua FTP / quản lý file

1. Giải nén gói plugin; thư mục gốc trong zip thường là `naviplus-menu-builder/` (tương thích chuẩn WordPress.org).
2. Tải thư mục đó lên `wp-content/plugins/` trên máy chủ.
3. Trong **Plugin → Đã cài đặt**, **Kích hoạt** **Naviplus Menu Builder**.

---

## 3. Bắt đầu sau khi kích hoạt

1. Vào **Giao diện (Appearance) → Naviplus Menu Builder**.
2. Tạo menu đầu tiên theo hướng dẫn trên màn hình (plugin có thể tự thiết lập kết nối site khi bạn tạo menu lần đầu — **không bắt buộc** phải chuẩn bị tài khoản Navi+ riêng trước khi cài plugin).
3. Thiết kế layout trong **trình soạn trực quan Navi+ Menu Builder** (mở từ bảng điều khiển WordPress).

Menu (cấu trúc, kiểu hiển thị) được lưu trên **dịch vụ Navi+**; WordPress lưu **định danh site** để nhận diện website của bạn — đây **không phải** mật khẩu WordPress.

---

## 4. Tối ưu cho mobile: nên dùng layout nào?

Navi+ hỗ trợ nhiều kiểu menu; với **khách truy cập trên điện thoại**, thường phù hợp:

| Kiểu | Gợi ý sử dụng |
|------|----------------|
| **Thanh tab (tab bar)** | Điều hướng nhanh 3–5 mục chính, cố định gần cạnh dưới hoặc theo thiết kế bạn chọn trong editor. |
| **Menu trượt / hamburger** | Nhiều mục, danh mục sâu; tiết kiệm không gian màn hình nhỏ. |
| **Mega menu** | Nội dung phong phú trên desktop; trên mobile cần kiểm tra trải nghiệm cuộn và kích thước chạm — chỉnh trong editor cho dễ bấm. |
| **Lưới (grid)** | Danh mục, ô shortcut — hợp trang chủ hoặc hub điều hướng. |
| **Nút nổi (floating)** | Mở menu hoặc hành động nhanh (ví dụ hỗ trợ, liên hệ) mà không chiếm toàn bộ header. |

Sau khi chỉnh layout, **luôn xem thử trên điện thoại thật** hoặc chế độ responsive của trình duyệt.

---

## 5. Hiển thị menu trên toàn website

- Trong bảng điều khiển Navi+ trên WordPress, bạn có thể **bật** hiển thị menu **toàn site** (global embed), tùy phiên bản và tùy chọn hiện có trên màn hình cài đặt.
- Nếu chỉ muốn menu xuất hiện ở một vài trang (ví dụ chỉ landing mobile), hãy **tắt** nhúng toàn site và dùng **shortcode** (mục 6).

---

## 6. Nhúng menu vào trang, bài viết hoặc widget (shortcode)

Dùng shortcode **khuyến nghị**:

```text
[naviwp embed_id="YOUR-EMBED-ID"]
```

Thay `YOUR-EMBED-ID` bằng **embed ID** của menu (lấy trong dashboard Navi+ / màn hình quản lý embed tương ứng).

**Shortcode cũ** vẫn được hỗ trợ để tương thích ngược:

```text
[naviplus embed_id="YOUR-EMBED-ID"]
```

### Trình soạn khối (Gutenberg)

- Thêm block **Shortcode** và dán `[naviwp embed_id="..."]`, **hoặc**
- Dán shortcode trong đoạn **Paragraph** — plugin có thể nhận diện `[naviwp]` / `[naviplus]` trong đoạn văn.

### Elementor / builder khác

- Dùng widget **Shortcode** (hoặc tương đương) và dán cùng cú pháp ở trên.

---

## 7. Gỡ cài đặt hoặc tạm thời ẩn menu

- **Tắt plugin:** script nhúng không còn tải theo cơ chế của plugin.
- **Gỡ shortcode:** xóa khỏi nội dung trang / widget nếu bạn đã nhúng thủ công.
- Layout menu trên dịch vụ Navi+ vẫn có thể được giữ lại nếu sau này bạn cài lại plugin và dùng lại embed.

---

## 8. Dịch vụ bên ngoài & quyền riêng tư

Plugin kết nối tới dịch vụ Navi+ Menu Builder để tạo và **render** menu. Dữ liệu có thể gồm (không giới hạn ở): **tên miền website**, **cấu hình menu**, và **dữ liệu sử dụng tối thiểu** phục vụ hiển thị.

- Chính sách quyền riêng tư: https://naviplus.io/privacy  
- Script loader: `https://live.naviplus.app/start.js`

---

## 9. Liên kết hữu ích

| Nội dung | Địa chỉ |
|----------|---------|
| Trang plugin WordPress.org | https://wordpress.org/plugins/naviplus-menu-builder/ |
| Diễn đàn hỗ trợ (WordPress.org) | https://wordpress.org/support/plugin/naviplus-menu-builder/ |
| Trang chủ Naviplus | https://naviplus.io |

---

## 10. Tóm tắt nhanh

1. **Cài & kích hoạt** từ WordPress.org hoặc file ZIP.  
2. **Giao diện → Naviplus Menu Builder** → tạo menu và thiết kế trong editor Navi+.  
3. Chọn layout **tab bar** / **hamburger** / **nút nổi** cho trải nghiệm mobile tốt.  
4. **Toàn site** hoặc **`[naviwp embed_id="..."]`** tùy nhu cầu.  
5. Kiểm tra trên **thiết bị thật** trước khi xuất bản.

*Bản tài liệu này có thể được cập nhật khi phát hành phiên bản plugin mới; thông tin phiên bản và changelog chính thức nằm trên trang WordPress.org của plugin.*

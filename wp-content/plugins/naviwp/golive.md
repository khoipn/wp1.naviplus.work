# Go-live checklist — Naviplus Menu Builder WordPress plugin

**Tên sản phẩm:** Naviplus Menu Builder. **Alias gọn trong UI/readme:** Navi+ Menu Builder.

Tài liệu nội bộ trước khi tag bản hoặc upload WordPress.org / triển khai. File này **không** đưa vào zip phát hành (đã loại khi build).

---

## 1. Đồng bộ phiên bản

Cùng một số ở mọi chỗ (WordPress.org sẽ so `Stable tag` với header plugin):

| Vị trí | Ghi chú |
|--------|---------|
| `naviwp.php` — header `Version:` | Chuỗi hiển thị trong Plugins |
| `Naviwp_Plugin::get_version()` | Cache-bust `start.js`, script handles |
| `readme.txt` — `Stable tag:` | Bắt buộc khớp version |
| `readme.txt` — `= X.Y.Z =` trong Changelog | Thêm mục mới mỗi lần release |

Sau khi sửa: `grep -r "1\.2\." .` trong thư mục plugin để không sót bản cũ.

---

## 2. Quy tắc tích hợp Navi+ Menu Builder (đừng tự ý đổi)

- Shortcode: `#%embed_id%-container` rồi **ngay sau đó** cấu hình + `start.js`.
- Dùng **`wp_register_script` → `wp_add_inline_script( …, 'before' )` → `wp_enqueue_script` → `wp_print_scripts()`** tại chỗ shortcode — **không** in thẻ `<script>` / `wp_get_script_tag()` trong PHP (Plugin Check: `NonEnqueuedScript`).
- Global embed: enqueue trên `wp_enqueue_scripts`, in trong `<head>` (`in_footer` = false), **không** nhét loader vào `wp_footer` trừ khi có spec từ chủ sản phẩm / Navi+ Menu Builder.
- Payload `_navi_setting`: khóa phải khớp `NAVIWP_EMBED_*` trong `includes/init.php` và loader `https://live.naviplus.app/start.js`.
- Mỗi lần chạy `start.js` chỉ xử lý **một** phần tử queue — nhiều embed trên cùng trang ⇒ nhiều handle / nhiều lần in script (đã xử lý bằng handle `navi-mnb-embed-{n}`).

Chi tiết thêm: `.cursor/rules/naviwp-naviplus-embed.mdc` và docblock đầu `includes/class-naviwp-frontend.php`.

---

## 3. Plugin Check & chuẩn mã

- Chạy **Plugin Check** (plugin trên site staging hoặc WP-CLI tương đương) sau mỗi thay đổi front/embed.
- Ưu tiên sửa lỗi **ERROR**; cân nhắc WARNING theo policy WordPress.org.
- Prefix: hàm/hằng/class AJAX dùng `naviwp_` / `NAVIWP_` / `Naviwp_` — không dùng tên global trùng core.

---

## 4. Đóng gói zip sạch (WordPress.org / gửi khách)

**Không** đưa vào zip:

- Thư mục `.git` hoặc bất kỳ `.git*` / `.DS_Store` / file ẩn tùy tiện
- `golive.md` (checklist nội bộ)
- `terminal.txt`, nháp `.md` cá nhân, `.env`

**Cấu trúc zip** (slug thư mục gốc):

```text
navi-menu-navigation-builder/
  naviwp.php
  readme.txt
  LICENSE
  includes/
  assets/
```

**Build** (từ máy dev, trong thư mục chứa `naviwp/`):

```bash
# Hoặc dùng script Python một lần như trong quy trình dự án:
# zip root = navi-menu-navigation-builder, nguồn = naviwp/, exclude .git + golive.md + file .*
```

Sau khi zip: `unzip -l navi-menu-navigation-builder.zip` — đếm file, xác nhận không có đường dẫn lạ.

---

## 5. Kiểm thử tay (tối thiểu)

- [ ] Kích hoạt plugin, mở **Appearance → Naviplus Menu Builder** (slug `naviwp-app`), đăng nhập / tạo menu nếu cần.
- [ ] **Global embed** bật: menu hiển thị, không lỗi console kiểu queue `_navi_setting` còn sót.
- [ ] **Global tắt** + shortcode `[naviwp embed_id="…"]` trên page: đúng vị trí, không trùng / thiếu script.
- [ ] Paragraph block chỉ chứa text shortcode (không Shortcode block): vẫn render.
- [ ] Một trang có **hai** shortcode khác `embed_id`: cả hai chạy.
- [ ] Theme có `wp_head` / `the_content` chuẩn (không strip script do lỗi của theme — hiếm nhưng đáng thử).

---

## 6. Readme & compliance

- `readme.txt`: mục **External Services** vẫn mô tả `live.naviplus.app/start.js` và privacy URL nếu có thay đổi domain.
- `Tested up to:` cập nhật theo WP mới khi đã smoke-test.
- Không commit file ẩn bị SVN WordPress.org từ chối (`.gitignore` trong gói zip, v.v.).

---

## 7. Sau khi release

- Tag git / ghi release note nội bộ khớp Changelog.
- Giữ bản zip và checksum (optional) cho support.

---

*Cập nhật lần cuối cùng bản plugin: **1.2.3**.*

---
name: naviwp-pack-release
description: >-
  Builds a WordPress.org–ready zip of the naviwp plugin (folder slug
  navi-menu-navigation-builder), excludes dev-only files, and optionally
  publishes under wp-content/uploads for HTTPS download. Use when the user
  asks to package naviwp, build a release zip, WordPress plugin directory zip,
  or download link for wp1.naviplus.work.
disable-model-invocation: true
---

# Đóng gói plugin Naviwp (Naviplus Menu Builder)

## Nguồn và slug

- **Mã nguồn:** `wp-content/plugins/naviwp/`
- **Thư mục gốc bên trong zip (bắt buộc cho SVN / .org):** `navi-menu-navigation-builder/` — khớp `Text Domain` trong header plugin, không đổi tùy tiện.

## Không đưa vào zip

- `golive.md` (checklist nội bộ)
- Mọi file/thư mục tên bắt đầu bằng `.` (`.git`, `.cursor`, v.v.)
- Không thêm file ngoài tree plugin

## Tên file zip giao cho người dùng

- Quy ước: `naviplus-menu-builder-YYYY-MM-DD-HHMMSS.zip` (giờ máy chủ, timezone server).

## Cách đúc (máy không có lệnh `zip`)

Chạy từ workspace (điều chỉnh `stamp` nếu cần):

```bash
python3 << 'PY'
import os, zipfile, pathlib, datetime
src = pathlib.Path("wp-content/plugins/naviwp")
slug = "navi-menu-navigation-builder"
stamp = datetime.datetime.now().strftime("%Y-%m-%d-%H%M%S")
out = pathlib.Path("wp-content/uploads/naviplus-plugin-builds")
out.mkdir(parents=True, exist_ok=True)
out = out / f"naviplus-menu-builder-{stamp}.zip"

def skip(rel):
    if rel.name == "golive.md":
        return True
    return any(p.startswith(".") for p in rel.parts)

files = []
for root, dirs, names in os.walk(src, topdown=True):
    dirs[:] = [d for d in dirs if not d.startswith(".")]
    for name in names:
        if name.startswith("."):
            continue
        p = pathlib.Path(root) / name
        rel = p.relative_to(src)
        if skip(rel):
            continue
        files.append(p)
files.sort()
with zipfile.ZipFile(out, "w", zipfile.ZIP_DEFLATED) as zf:
    for p in files:
        zf.write(p, (pathlib.Path(slug) / p.relative_to(src)).as_posix())
print(out.resolve())
PY
```

## Link tải qua site (wp1.naviplus.work)

- **Đừng** dựa vào mở file zip trong thư mục `plugins/` từ IDE (binary / không phải URL).
- **Nên** copy hoặc ghi zip trực tiếp vào:
  `wp-content/uploads/naviplus-plugin-builds/<tên-file>.zip`
- URL công khai (thay đúng tên file):
  `https://wp1.naviplus.work/wp-content/uploads/naviplus-plugin-builds/naviplus-menu-builder-YYYY-MM-DD-HHMMSS.zip`
- Thư mục `naviplus-plugin-builds/` có thể trả **403** khi duyệt danh sách; **URL đầy đủ tới file .zip** phải trả **200** và `Content-Type: application/zip`.

## Sau khi đóng gói

- `unzip -l <file.zip>` — kiểm tra đúng prefix `navi-menu-navigation-builder/`, không có `golive.md`, đủ `naviwp.php`, `readme.txt`, `LICENSE`, `includes/`, `assets/`.

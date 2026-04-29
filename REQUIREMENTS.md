# Requirements

## 시스템

- **PHP** 8.2 이상 (PDO, pdo_mysql, mbstring, session 확장)
- **MySQL** 8.0 이상 (utf8mb4 / utf8mb4_unicode_ci)
- **Apache** (mod_rewrite, .htaccess 허용)

권장 환경: **Laragon** (위 3종 + HeidiSQL 번들)

## PHP 확장

```
pdo
pdo_mysql
mbstring
session
json
```

## 외부 의존성 (런타임 CDN, 설치 불필요)

- Tailwind CSS — `cdn.tailwindcss.com`
- Alpine.js 3.x — `cdn.jsdelivr.net/npm/alpinejs`
- Swiper 11 — `cdn.jsdelivr.net/npm/swiper`
- Pretendard 폰트 — `cdn.jsdelivr.net/gh/orioncactus/pretendard`

## DB 설정

`includes/config.php` 기본값 (Laragon 기본):

```
host: 127.0.0.1   port: 3306
user: root        pass: (빈 문자열)
db:   webpage     charset: utf8mb4
```

DB 이름·계정 변경 시 이 파일만 수정.

## 외부 시연 (선택)

- **cloudflared** — 임시 공개 URL 발급용
  ```
  cloudflared tunnel --url http://localhost
  ```

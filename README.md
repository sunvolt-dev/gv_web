# Webpage — 썬볼트 스타일 배터리 쇼핑몰 데모

순수 PHP + MySQL로 구현한 상품 전시·장바구니 데모. 결제 미구현.
gitignore, 로그인 기능 없음


## 폴더 구조

```
includes/   DB·헬퍼·공통 헤더/푸터 (웹 노출 차단)
  config.php       DB·사이트 설정
  db.php           PDO 싱글톤 + db_all/db_one/db_exec
  functions.php    카테고리·상품·장바구니·출력 헬퍼
  header.php / footer.php
public/     웹 루트 (Apache document root)
  index.php        메인
  shop/list.php    상품 목록 (?ca_id, ?sort, ?page, ?q)
  shop/item.php    상품 상세 (?it_id)
  cart.php / cart_action.php
  checkout.php / checkout_complete.php
  admin/           관리자 (상품·카테고리 CRUD)
  assets/          css·js·images
sql/schema.sql     DB 생성 + 테이블 + 시드
```

## DB 테이블

- `categories` — 자기참조 트리 (대분류/중분류)
- `products` — 상품 (가격·재고·이미지 5장·BEST/NEW 플래그)
- `product_options` — 옵션 (타입·값·가산금액)
- `admin_users` — 관리자 계정

장바구니/주문은 DB 안 쓰고 `$_SESSION` 사용.

## 실행

1. Laragon MySQL 실행
2. HeidiSQL에서 `sql/schema.sql` import (DB·시드 자동 생성)
3. Apache document root → `Webpage/public/`
4. 브라우저 접속
5. 어드민: `/admin/login.php` (admin / admin1234)

외부 시연: `cloudflared tunnel --url http://localhost`

## 스택

- PHP 8.2+ / MySQL 8 (utf8mb4) / PDO
- Tailwind CSS + Alpine.js + Swiper.js (전부 CDN, 빌드 없음)

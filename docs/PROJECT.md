# PROJECT — 썬볼트 배터리몰 프로젝트 마스터 문서

> 이 문서는 **살아있는 문서**다. 대화·개발이 진행될 때마다 계속 참조되고 갱신된다.
> 새 세션을 시작하거나 작업을 이어갈 때 **이 문서를 먼저 읽는다.**
> 작업 진행 상황 체크는 [ACTION_PLAN.md](ACTION_PLAN.md)에서 관리한다.

最終 갱신: 2026-04-29

---

## 1. 프로젝트 개요

- **무엇**: 썬볼트 배터리 쇼핑몰 스타일의 PHP 웹 애플리케이션 (데모 → 실사용 확장 중)
- **목적**: 자사 여러 사이트의 베이스가 되는 재사용 가능한 웹 프로젝트
- **참고 사이트**: sunvolt-battery.co.kr (디자인 구조만 참고, 코드는 독자 구현)
- **저장소**: `github.com/sunvolt-dev/gv_web.git` (origin/master)
- **로컬 경로**: `D:/업무/projects/Webpage`

## 2. 기술 스택

| 영역 | 사용 기술 |
|---|---|
| 백엔드 | 순수 PHP 8.2+ (프레임워크 없음), PDO |
| DB | MySQL 8 (utf8mb4_unicode_ci) |
| 프론트 | Tailwind CSS(CDN), Alpine.js, Swiper.js, Quill 에디터, Pretendard 폰트 |
| 로컬 환경 | Laragon (Apache + PHP + MySQL) |
| 빌드 | 없음 — 전부 CDN |

## 3. 폴더 구조

```
Webpage/
├── docs/                  # 프로젝트 문서 (이 폴더)
│   ├── PROJECT.md          # 마스터 문서 (이 파일)
│   └── ACTION_PLAN.md      # 액션 플랜 체크리스트
├── includes/              # 웹 비노출 — 공통 로직
│   ├── config.php          # DB·사이트 설정
│   ├── db.php              # PDO 싱글톤 + db_all/db_one/db_exec
│   ├── functions.php       # 카테고리·상품·장바구니·출력 헬퍼
│   ├── user_auth.php       # 회원 인증 헬퍼
│   ├── header.php          # 공통 헤더 (GNB·메타)
│   └── footer.php          # 공통 푸터
├── public/                # 웹 루트 (Apache document root)
│   ├── index.php           # 메인 (히어로 슬라이더)
│   ├── shop/list.php       # 상품 목록 (?ca_id,?sort,?page,?q)
│   ├── shop/item.php       # 상품 상세 (?it_id)
│   ├── cart.php / cart_action.php / checkout.php / checkout_complete.php
│   ├── auth/               # 회원가입·로그인·로그아웃·이메일중복확인
│   ├── mypage/             # 마이페이지 (정보수정·주문내역)
│   ├── blog/               # 블로그 목록·상세
│   ├── cases/              # 납품사례 목록·상세
│   ├── admin/              # 관리자 (상품·카테고리·블로그·납품사례·배너 CRUD)
│   └── assets/             # css·js·images
├── themes/                # 레이아웃 테마 (과제 4)
│   ├── classic/ modern/ magazine/ bold/ compact/
│   └── 각 테마: theme.php(색상)·header.php·home.php·product_card.php
├── sql/
│   ├── schema.sql          # 전체 스키마 + 시드 (처음 셋업용)
│   └── migrations/         # 증분 마이그레이션 (기존 데이터 보존)
├── README.md / REQUIREMENTS.md
```

테마 전환: `includes/config.php`의 `'theme'` 값, 또는 어드민 → 테마 설정.

## 4. DB 스키마 (DB명: `sunvolt-webpage`)

| 테이블 | 용도 |
|---|---|
| `categories` | 카테고리 자기참조 트리 (대/중분류) |
| `products` | 상품 (가격·재고·이미지5장·BEST/NEW) |
| `product_options` | 상품 옵션 (타입·값·가산금액) |
| `admin_users` | 관리자 계정 (admin / admin1234) |
| `users` | 회원 |
| `orders` / `order_items` | 주문 |
| `posts` | 블로그 글 |
| `case_studies` / `case_images` | 납품사례 + 사진 |
| `banners` | 메인 히어로 슬라이더 배너 |

## 5. 개발 환경

- **로컬**: Laragon. 프로젝트는 정션으로 `C:\laragon\www\webpage` → `D:\...\public` 연결
- **접속**: `http://webpage.test/` (Apache vhost + hosts)
- **PHP CLI**: `C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe`
- **DB 접속**: 127.0.0.1 / root / (빈 비번) / `sunvolt-webpage`
- **어드민**: `/admin/login.php` — admin / admin1234

## 6. 개발 규칙 ⚠️ 반드시 준수

### ✅ 해야 할 것
- 새 페이지는 폴더 구조 규칙 준수 — 웹 노출 파일은 `public/` 아래만
- `includes/`, `sql/`은 웹 루트 밖 유지 (보안)
- DB 접근은 `db_all/db_one/db_exec` 헬퍼 사용, **항상 prepared statement** (SQL 인젝션 방지)
- 출력 시 `h()` 로 이스케이프 (XSS 방지)
- 코드 작성 후 PHP 문법 체크 (`php -l`)
- 작업 단위가 끝나면 **테스트 → 동작 확인 → 다음 단계** (한 번에 몰아서 X)
- DB 구조 변경 시 `sql/migrations/`에 증분 파일 추가 (기존 데이터 보존)
- 변경 사항은 이 문서 + ACTION_PLAN.md에 기록

### ❌ 하면 안 되는 것
- `schema.sql`을 운영 중 함부로 재실행 — 모든 데이터 DROP됨. 변경은 migration으로.
- 그누보드/영카트 등 외부 CMS 코드 직접 가져오기 (디자인만 참고)
- 프레임워크(Laravel 등) 도입 — 순수 PHP 유지가 이 프로젝트 원칙
- 빌드 도구 도입 (npm build 등) — CDN 방식 유지
- 회원 비밀번호 평문 저장 — 항상 `password_hash`
- `includes/config.php`의 DB명 임의 변경 (현재 `sunvolt-webpage` 확정)
- 커밋·푸시는 사용자가 요청할 때만

## 7. 알려진 제약 / 미구현

- 실제 결제 미연동 (페이크 — "주문 완료" 페이지로 끝)
- 이메일 발송 미구현 (인증메일·비번찾기 없음)
- 블로그 댓글·카테고리 없음 (의도적 — 요구사항)
- 검색은 상품만 (블로그·납품사례 검색 없음)
- 조회수는 블로그만, 어드민에서만 노출

## 8. 배포 계획

| 단계 | 방법 |
|---|---|
| 로컬 개발 | Laragon |
| 외부 시연 | Cloudflare Tunnel (무료, 임시 URL) |
| 정식 운영 | 회사 자체 서버 (SSH 접근) 에 배포 검토 중 — 도메인 `137.co.kr` 후보 |
| 호스팅 대안 | hosting.kr 웹호스팅 / 닷홈 무료 |

## 9. 개발 히스토리 (변경 로그)

> 최신이 위. 작업 완료 시마다 한 줄 추가.

- **2026-05-22** — 과제 4 후속: compact 히어로 수정, 전 테마 관리자 링크, 테마 미리보기, 어드민 색상 편집
- **2026-05-22** — 과제 4: 테마 시스템 — 5개 레이아웃(classic/modern/magazine/bold/compact) + 어드민 전환 UI
- **2026-05-22** — 과제 2: 반응형 + 모바일 전용 모드 (디바이스 감지, 모바일 셸·하단 탭바)
- **2026-05-22** — 과제 1: SEO/AEO (robots·sitemap·구조화데이터·OG·FAQ 페이지)
- **2026-04-29** — docs 체계 수립 (PROJECT.md / ACTION_PLAN.md). 5대 과제 착수: SEO/AEO, 모바일 모드, 앱화, 5레이아웃, 멀티사이트 복제
- **2026-04-29** — 회원가입 이메일 중복확인 버튼 + 비밀번호 일치 실시간 표시
- **2026-04-29** — 메인 히어로 배너 슬라이더(Option B) + 어드민 배너 CRUD, 베네핏 스트립 확대, 상단바 고정
- **2026-04-28~29** — 회원/마이페이지, 블로그, 납품사례(Quill 에디터) 기능 추가
- **2026-04-28** — 초기 쇼핑몰 구축 (상품·카테고리·장바구니·결제·어드민)

# Webpage — 썬볼트 스타일 배터리 쇼핑몰

PHP + MySQL 기반 쇼핑몰. **Docker로 구동** (권장). 자사 멀티사이트 베이스 프로젝트.

상세 문서·진행 로그는 [docs/PROJECT.md](docs/PROJECT.md), [docs/ACTION_PLAN.md](docs/ACTION_PLAN.md).

## 빠른 시작 — Docker

```bash
# 1) 환경설정
cp .env.example .env       # 필요시 포트·비번 수정

# 2) 가동 (첫 실행은 빌드로 몇 분 소요)
docker compose up -d --build

# 3) 접속
#    http://localhost:8082          (사이트)
#    http://localhost:8082/admin/   (어드민: admin / admin1234)
```

DB는 컨테이너 첫 부팅 시 `sql/schema.sql`로 자동 시드됨.

### 정지·재시작
```bash
docker compose stop          # 일시 정지
docker compose start         # 재시작
docker compose down          # 컨테이너 제거 (DB 데이터 유지)
docker compose down -v       # 완전 초기화 (DB 데이터 삭제)
```

### 로그
```bash
docker compose logs -f web   # PHP·Apache 로그
docker compose logs -f db    # MySQL 로그
```

## 멀티사이트 — 같은 코드로 다른 사이트 동시 운영

`.env.site2` 같은 별도 환경파일을 만들고:

```bash
docker compose --env-file .env.site2 -p site2 up -d
```

인스턴스마다 다른 값:
- 포트 (`WEB_PORT`, `DB_PORT`)
- DB명 (`DB_NAME`)
- 사이트명·연락처 (`SITE_NAME`, `SITE_PHONE`)
- 테마 (`SITE_THEME` = classic/modern/magazine/bold/compact)

`.env.site2` 예시 파일이 저장소에 포함돼있음.

## 폴더 구조

```
Webpage/
├── docker-compose.yml + Dockerfile + .env / .env.example
├── docker/                Apache·PHP·DB init 설정
├── includes/              DB·헬퍼·공통 헤더/푸터 (웹 비노출)
├── public/                웹 루트 (Apache DocumentRoot)
│   ├── shop/  blog/  cases/  admin/  auth/  mypage/
│   └── assets/            css·js·업로드 이미지
├── themes/                레이아웃 테마 5종 + color_overrides.json
├── sql/                   schema.sql + migrations/
└── docs/                  PROJECT.md (마스터) · ACTION_PLAN.md
```

## 주요 기능

- 상품·카테고리·옵션·장바구니·결제(페이크) — 어드민 CRUD
- 회원가입(이메일 중복확인·비번 일치 실시간)·로그인·마이페이지·주문내역
- 블로그 + 납품사례 (Quill 에디터, 이미지 업로드)
- 메인 히어로 슬라이더 (어드민 배너 CRUD)
- 5개 레이아웃 테마 + 어드민 색상 편집
- PC/모바일 자동 분기 (User-Agent 감지, 하단 탭바)
- SEO/AEO — robots, sitemap, Open Graph, JSON-LD(Product/Article/FAQPage 등)

## 기술 스택

PHP 8.2 · MySQL 8 (utf8mb4) · Apache 2.4 / Tailwind CSS · Alpine.js · Swiper · Quill (전부 CDN)

## 환경 변경 이력

- **~2026-05-22**: Laragon (Windows 데스크탑)
- **2026-05-28~**: **Docker 컨테이너**로 이전 — 회사 내부 Linux 서버 배포 준비.
  Laragon용 fallback도 `config.php` env 폴백으로 살아있음.

기존 Laragon DB 데이터는 `docker exec sunvolt-db mysql < dump.sql` 식으로 이관.

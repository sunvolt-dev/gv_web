# Webpage 프로젝트 종합 정리 (왜 / 어떻게 / 무엇을)

**기간**: 2026-04-28 ~ 2026-05-22 (진행 중)
**저장소**: github.com/sunvolt-dev/gv_web.git
**로컬**: D:\업무\projects\Webpage
**참조 마스터**: [PROJECT.md](PROJECT.md) · [ACTION_PLAN.md](ACTION_PLAN.md)

---

## 1. 왜 만들었나 (목적)

**자사 여러 사이트의 베이스가 되는 재사용 가능한 웹 프로젝트**가 필요했다.

- 현재 회사 자산: sunvolt-battery·korea-tech·koreapack 등 그누보드+eyoom, smart-battery·star-battery는 CreatorLink, good-price는 Cafe24 — **사이트마다 CMS가 달라 일관된 SEO·구조·확장이 어려움**
- 외부 CMS는 우리가 head·구조·sitemap·메타를 자유롭게 못 만짐 → SEO/AEO 풀 컨트롤이 안 됨
- 결제·회원·블로그·납품사례·관리자 어드민까지 **하나의 PHP 코드베이스에 모아두고, 테마와 설정만 바꿔서 멀티사이트로 복제**하는 게 목표

→ 디자인은 sunvolt-battery.co.kr 구조를 참고하되 **코드는 처음부터 독자 구현**. 외부 CMS 의존을 끊고, AEO·다국어·이미지·자동화까지 우리가 100% 제어 가능한 베이스를 확보한다.

## 2. 어떻게 만들었나 (스택·원칙)

### 스택 — 의도적으로 가볍게

| 영역 | 선택 | 이유 |
|---|---|---|
| 백엔드 | 순수 PHP 8.2+ (프레임워크 없음) | Laravel 등 도입 시 학습·배포 부담 ↑. 멀티사이트 복제 단순성 우선 |
| DB | MySQL 8 (utf8mb4) | Laragon·hosting.kr·자체 서버 어디든 동일 |
| 프론트 | Tailwind + Alpine.js + Swiper + Quill 에디터 (전부 CDN) | npm build 도입 시 배포 복잡도 ↑. CDN으로 통일 |
| 로컬 | Laragon | Apache·PHP·MySQL·HeidiSQL 번들 |
| 빌드 | 없음 | 모든 환경에 동일하게 떨어지도록 |

### 강제 원칙 (PROJECT.md §6)

- 웹 노출 파일은 `public/` 아래만 — `includes/`·`sql/`은 웹 루트 밖 (보안)
- DB 접근은 `db_all/db_one/db_exec` 헬퍼 — **항상 prepared statement** (SQL 인젝션 방지)
- 출력은 `h()` 이스케이프 (XSS 방지)
- 코드 작성 후 `php -l` 통과
- DB 구조 변경은 **`sql/migrations/` 증분 파일** — `schema.sql` 재실행 금지(데이터 DROP)
- 회원 비밀번호는 `password_hash` 평문 금지
- 커밋·푸시는 사용자가 요청할 때만

### 폴더 구조

```
includes/   웹 비노출 — DB·헬퍼·공통 헤더/푸터·인증
public/     Apache document root — 메인·상품·장바구니·결제·어드민·블로그·납품사례·FAQ
themes/     5종 테마 (classic·modern·magazine·bold·compact)
sql/        schema.sql + migrations/
docs/       PROJECT.md (마스터) + ACTION_PLAN.md (체크리스트) + 이 파일
```

## 3. 무엇을 만들었나 (진행 히스토리)

### 1단계 — 2026-04-28: 초기 쇼핑몰 구축

- 상품·카테고리(자기참조 트리) 관리
- 장바구니·결제(페이크 — "주문 완료" 페이지로 끝)
- 관리자 어드민 (상품·카테고리 CRUD)

### 2단계 — 2026-04-29: 회원·블로그·납품사례 + docs 체계

- 회원가입·로그인·마이페이지 (이메일 중복확인 + 비밀번호 일치 실시간 표시)
- 블로그 (목록·상세, Quill 에디터)
- 납품사례 (목록·상세, 다중 이미지)
- 메인 히어로 배너 슬라이더(Option B) + 어드민 배너 CRUD
- **docs 체계 수립**: PROJECT.md(마스터) + ACTION_PLAN.md(5대 과제 체크리스트)

### 3단계 — 2026-05-22: 5대 과제 중 3개 완료

대규모 확장 작업을 5대 과제로 쪼개서 진행. 단일 커밋(`7711145`)에 묶어 14파일 / +913 LOC.

| # | 과제 | 상태 |
|---|---|---|
| 1 | SEO / AEO 적용 | ✅ 완료 |
| 2 | 반응형 + 모바일 전용 모드 | ✅ 완료 |
| 3 | 앱(App) 배포 — PWA 1순위 | ⏳ 대기 |
| 4 | 5가지 레이아웃 베리에이션 (테마 시스템) | ✅ 완료 |
| 5 | 프로젝트 복제 / 멀티사이트 자동화 | ⏳ 대기 |

#### 과제 1. SEO / AEO

- `public/robots.php` — 도메인 자동 인식, `.htaccess`로 `/robots.txt` 매핑
- `public/sitemap.php` — 동적 XML, 43개 URL (홈·카테고리·전상품·블로그·납품사례·FAQ)
- `includes/header.php` 메타 확장 — Open Graph + Twitter Card + canonical + robots
- **JSON-LD 구조화 데이터** (functions.php 헬퍼)
  - 전역: `Organization` · `WebSite`(검색박스 포함)
  - 상품 상세: `Product` (가격·재고·이미지)
  - 블로그 상세: `BlogPosting`
  - 상품·블로그·납품사례·FAQ 상세: `BreadcrumbList`
- **`public/faq.php` 신규** + `FAQPage` JSON-LD (질문 10개)

**왜 AEO를 따로 잡았나**: SEO는 Google·Naver 봇 대응, **AEO는 ChatGPT·Perplexity·AI 개요 대응**. AEO 핵심은 **구조화 데이터(Schema.org JSON-LD)** — AI가 페이지 의미를 이해하는 통로. 외부 CMS에서는 못 만지는 부분이라, 우리 PHP 사이트의 가장 큰 차별화 포인트.

#### 과제 2. 반응형 + 모바일 전용 모드

- 요구: **PC = 반응형 / 모바일 = 아예 모바일 모드** (네이버 PC ↔ m.naver처럼 분리된 UX)
- 3가지 접근법 검토 끝에 **B) 디바이스 감지 → 모바일 전용 템플릿** 채택
  - URL 동일·데이터 레이어 공유·뷰만 모바일 전용
  - SEO 안전(Google 공식 dynamic serving 허용) + `Vary: User-Agent` 헤더
- `includes/device.php` — User-Agent 감지 + `?view=mobile/pc` 강제 전환(쿠키 30일)
- 뷰 분기 구조 — `header.php`/`footer.php`가 `is_mobile()`로 셸 분기 (head·SEO는 공유)
- 모바일 헤더 + 전체화면 드로어 + **하단 고정 탭바**(홈·카테고리·장바구니·마이페이지)
- 전 페이지 모바일 셸 적용 (메인·상품 목록/상세·장바구니·결제·마이페이지·블로그·납품사례)

**방식 메모**: 페이지별 완전 분리 템플릿 대신 **"모바일 셸 + 반응형 콘텐츠"** 채택. 15개 페이지 코드 중복 없이 네이버형 모바일 UX 구현.

#### 과제 4. 5가지 레이아웃 베리에이션 (테마 시스템)

- 요구: 자사 여러 사이트용 레이아웃 5종 — 데이터·세팅 방식은 비슷하되 **레이아웃이 달라 "다른 사이트다"** 느낌
- 채택 구조: `config.php`의 `'theme'` 값으로 전환. 각 테마는 헤더/푸터/메인레이아웃/상품카드/CSS 보유. 데이터 레이어는 공유.
- 5종 테마 컨셉:
  - `classic` — 현재 디자인 (기본값)
  - `modern` — 미니멀 (니어블랙 + 블루)
  - `magazine` — 에디토리얼 (딥그린 + 테라코타)
  - `bold` — 강렬 (퍼플 + 옐로우)
  - `compact` — 정보밀집 (차콜 + 틸)
- 어드민 테마 전환 UI (`admin/theme.php`) — 색상 미리보기 + 원클릭 적용 + `color_overrides.json`으로 primary/accent 직접 지정
- 후속 수정 4건: compact 히어로 깨짐 정적배너 교체 / 공유 푸터에 관리자 링크(전 테마) / 미리보기 `?preview_theme=` 방식

## 4. 검증 상태

| 항목 | 결과 |
|---|---|
| 전 파일 `php -l` | ✅ 통과 |
| robots·sitemap CLI 출력 | ✅ 정상 |
| JSON-LD 헬퍼 동작 | ✅ 확인 |
| 모바일/PC UA 양쪽 전 페이지 CLI 렌더 | ✅ Fatal/Warning 없음 |
| PC모드에 모바일 요소 노출 | ✅ 0개 확인 |
| 5종 테마 각각 index.php 렌더 | ✅ Fatal 없음 |
| 사용자 브라우저 확인 | ⏳ 대기 (Rich Results Test 포함) |

## 5. Git 브랜치 현황

- `feature/seo-mobile` — 과제 1(SEO/AEO) + 과제 2(반응형/모바일). push 완료
- `feature/theme-system` — 과제 4(테마 시스템) + 후속 수정. push 완료 (seo-mobile에서 분기)
- master 병합은 사용자 PR 검토 후 진행 예정

## 6. 남은 일

### 과제 3 — PWA (앱 배포)
- `public/manifest.json` (앱 이름·아이콘·테마색·시작URL·display:standalone)
- 앱 아이콘 세트 (192·512px)
- `public/service-worker.js` (기본 캐싱, 오프라인 폴백)
- header.php에 manifest 링크·apple-touch-icon·테마컬러 메타
- "홈 화면에 추가" 안내 배너 (모바일, 1회성)
- 검증: Lighthouse PWA 점수 + 모바일 "홈 화면에 추가" 동작
- **전제**: 과제 2 결과를 바탕으로 진행 → 지금 착수 가능

### 과제 5 — 멀티사이트 자동화
- 인스턴스 가변 요소를 `config.php`로 완전 집약 (잔여 하드코딩 제거)
- `config.sample.php` 템플릿 + 설치 스크립트 (DB 생성·schema import·관리자 계정)
- 이미지 시드 자동 복사 + 기본 이미지 세트
- 멀티 인스턴스 배포 절차 문서화 (서버별 vhost·DB 분리)
- 2번째 인스턴스 띄워 독립 동작 검증

### 정식 운영 배포
- 회사 자체 서버(SSH 접근) 배포 검토 중 — 도메인 `137.co.kr` 후보
- 대안: hosting.kr 웹호스팅 / 닷홈 무료

## 7. 알려진 제약

- 실제 결제 미연동 (페이크)
- 이메일 발송 미구현 (인증메일·비번찾기 없음)
- 블로그 댓글·카테고리 없음 (의도적 — 요구사항)
- 검색은 상품만 (블로그·납품사례 검색 없음)
- 조회수는 블로그만, 어드민에서만 노출

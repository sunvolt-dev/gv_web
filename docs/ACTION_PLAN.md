# ACTION PLAN — 5대 과제

> 각 과제를 **단계로 쪼개고, 하나씩 수행 → 테스트 → 동작 확인 후 다음**으로 진행한다.
> 완료 시 `[ ]` → `[x]`. 막히면 `[!]` 표시 + 사유 기록.
> 상위 맥락은 [PROJECT.md](PROJECT.md) 참조.

상태 범례: `[ ]` 대기 · `[~]` 진행중 · `[x]` 완료·검증됨 · `[!]` 막힘

---

## 과제 1. SEO / AEO 적용

### 검토 결과
- **SEO**(검색엔진최적화) = Google·Naver 봇 대응 / **AEO**(답변엔진최적화) = ChatGPT·Perplexity·AI 개요 대응
- **자유롭게 설정 가능한가? → 100% 가능.** 우리 소유 PHP 코드라 메타·헤더·구조·robots·sitemap 전부 제어. 호스팅 종속 없음.
- 현재 상태: `<title>`·`<meta description>`은 페이지별 있음 / 나머지(robots, sitemap, OG, 구조화 데이터, canonical, FAQ)는 **전부 없음**
- AEO 핵심은 **구조화 데이터(JSON-LD Schema.org)** — Product·Article·FAQPage·BreadcrumbList. AI가 페이지 의미를 이해하게 함.

### 단계
- [x] 1-1. `public/robots.php` 생성 (`.htaccess`로 `/robots.txt` 매핑, 도메인 자동 인식)
- [x] 1-2. `public/sitemap.php` 동적 XML — 홈·카테고리·전상품·블로그·납품사례·FAQ (검증: 43개 URL)
- [x] 1-3. `includes/header.php` 메타 확장 — Open Graph, Twitter Card, canonical, robots 메타
- [x] 1-4. 전역 JSON-LD — Organization, WebSite (검색박스 포함)
- [x] 1-5. 상품 상세 JSON-LD — Product (가격·재고·이미지)
- [x] 1-6. 블로그 상세 JSON-LD — BlogPosting
- [x] 1-7. BreadcrumbList JSON-LD — 상품·블로그·납품사례·FAQ 상세
- [x] 1-8. `public/faq.php` FAQ 페이지 신규 + FAQPage JSON-LD (질문 10개)
- [x] 1-9. GNB·푸터에 FAQ 링크 추가, og:image 기본값 설정
- **테스트**: ✅ 전 파일 `php -l` 통과 / robots·sitemap CLI 출력 정상 / JSON-LD 헬퍼 동작 확인
  - ⏳ 사용자 브라우저 확인 대기 (페이지 소스의 메타·JSON-LD, Google Rich Results Test)

---

## 과제 2. 반응형 + 모바일 전용 모드

### 검토 결과
- 요구: **PC = 반응형**, **모바일 = 아예 모바일 모드** (네이버 PC/m.naver 처럼 명확히 분리된 UX)
- 현재: Tailwind 반응형(모바일우선) — 모바일에서 "작동"은 하나 PC의 축소판 수준
- 접근법 3가지:
  - A) 순수 반응형 강화 — 한 코드, 한계: "모바일 전용 느낌" 약함
  - **B) 디바이스 감지 → 모바일 전용 템플릿 (채택)** — User-Agent 감지, URL 동일, 데이터 레이어 공유, 뷰만 모바일 전용 재구성
  - C) `m.` 서브도메인 분리 — 관리 부담 큼
- **채택: B.** 같은 URL 유지(SEO 안전, dynamic serving — Google 공식 허용), `Vary: User-Agent` 헤더 추가. 모바일은 하단탭바·풀스크린 메뉴 등 앱같은 UX.

### 단계
- [x] 2-1. `includes/device.php` — User-Agent 감지 + `?view=mobile/pc` 강제 전환(쿠키 30일)
- [x] 2-2. 뷰 분기 구조 — `header.php`/`footer.php`가 `is_mobile()`로 셸 분기 (head·SEO는 공유)
- [x] 2-3. 모바일 헤더(다크 바+검색+가로 카테고리칩) + 전체화면 드로어 메뉴 + **하단 고정 탭바**(홈·카테고리·장바구니·마이페이지)
- [x] 2-4. 모바일 메인 — 반응형 콘텐츠(grid-cols-2 등) + 모바일 셸로 처리
- [x] 2-5. 모바일 상품 목록·상세 — 반응형 + 모바일 셸 (렌더 검증 통과)
- [x] 2-6. 모바일 장바구니·결제·마이페이지·블로그·납품사례 — 동일 방식 (렌더 검증 통과)
- [x] 2-7. `Vary: User-Agent` 헤더 + 푸터에 "PC 버전 ↔ 모바일 버전" 토글 링크
- **테스트**: ✅ 모바일/PC UA 양쪽 전 페이지 CLI 렌더 — Fatal/Warning 없음. PC모드에 모바일요소 0개 확인.
  - ⏳ 사용자 브라우저 확인 대기 (실제 모바일/DevTools 기기모드)
- **방식 메모**: 페이지별 완전 분리 템플릿 대신 **"모바일 셸(헤더·드로어·하단탭바·푸터) + 반응형 콘텐츠"** 채택. 15개 페이지 코드 중복 없이 네이버형 모바일 UX 구현. 특정 페이지에 모바일 전용 레이아웃이 더 필요하면 후속 추가 가능.
- ✅ **완료 시 → 새 브랜치 생성 + git push**

---

## 과제 3. 앱(App) 배포 검토

### 검토 결과 — "과제 2와 다른 케이스인가?" → **그렇다, 완전히 다름**
- 과제 2 = 모바일 **브라우저**에서 보는 웹 / 과제 3 = **설치하는 애플리케이션**
- "이 페이지를 앱으로" 만드는 3가지 길:
  - **A) PWA (Progressive Web App)** — `manifest.json` + 서비스워커 추가. 홈화면 설치·오프라인 일부·푸시 가능. 우리 PHP 사이트에 파일 몇 개 추가로 됨. **1순위**
  - **B) WebView 래퍼** — 네이티브 껍데기로 웹을 감쌈 (Capacitor 등). 앱스토어·플레이스토어 등록 가능. 웹 코드 재사용. PWA 후 필요 시.
  - C) 네이티브 재작성 (React Native/Flutter) — API 백엔드 필요, 풀 리라이트. 비권장.
- **전제**: 과제 2(모바일 모드)가 잘 돼있어야 PWA 품질이 좋음. 그래서 과제 2 다음에 진행.

### 단계
- [ ] 3-1. `public/manifest.json` — 앱 이름·아이콘·테마색·시작URL·display:standalone
- [ ] 3-2. 앱 아이콘 세트 (192·512px 등) `assets/icons/`
- [ ] 3-3. `public/service-worker.js` — 기본 캐싱(오프라인 폴백 페이지)
- [ ] 3-4. `header.php`에 manifest 링크·apple-touch-icon·테마컬러 메타
- [ ] 3-5. 서비스워커 등록 스크립트
- [ ] 3-6. "홈 화면에 추가" 안내 배너 (모바일, 1회성)
- [ ] 3-7. WebView 래퍼(B) 경로 문서화 — 실제 스토어 등록은 별도 결정
- **테스트**: Chrome DevTools > Application 탭 (manifest·SW 인식), Lighthouse PWA 점수, 모바일 "홈 화면에 추가" 동작
- ✅ **완료 시 → 새 브랜치 생성 + git push**

---

## 과제 4. 5가지 레이아웃 베리에이션 (테마 시스템)

### 검토 결과
- 요구: 자사 여러 사이트용 레이아웃 5종. 데이터·세팅 방식은 비슷하되 **레이아웃이 달라 "다른 사이트다"** 느낌.
- 접근법:
  - CSS만 교체 → "색만 다른" 수준, 요구 미달
  - **테마 시스템 (채택)** — `themes/` 폴더에 테마별 레이아웃 템플릿+CSS. 데이터 레이어(`functions.php`,`db.php`)는 공유.
- **채택 구조**: `config.php`의 `'theme'` 값으로 전환. 각 테마는 헤더/푸터/메인레이아웃/상품카드/CSS 보유.
- 테마 5종 컨셉(안): `classic`(현재) · `modern`(미니멀) · `magazine`(매거진형) · `bold`(강렬·대형타이포) · `compact`(정보밀집형)

### 단계
- [x] 4-1. 테마 로더 `includes/theme.php` + config에 `theme` 키 + functions.php 연결
- [x] 4-2. 현재 디자인을 `themes/classic/`으로 분리 (header·home·product_card·theme)
- [x] 4-3. header.php/index.php/_product_card.php가 테마 위임하도록 리팩터 (head·모바일셸·푸터는 공유)
- [x] 4-4. `themes/modern/` — 미니멀(니어블랙+블루)
- [x] 4-5. `themes/magazine/` — 에디토리얼(딥그린+테라코타)
- [x] 4-6. `themes/bold/` — 강렬(퍼플+옐로우)
- [x] 4-7. `themes/compact/` — 정보밀집(차콜+틸)
- [x] 4-8. 어드민 테마 전환 UI (`admin/theme.php`) — 색상 미리보기 + 원클릭 적용
- **테스트**: ✅ 20개 테마파일 `php -l` 통과 / 5개 테마 각각 index.php 렌더 — Fatal 없음
  - ⏳ 사용자 브라우저 확인 대기 (어드민 테마설정에서 5종 전환)
- **방식 메모**: `themes/{name}/` 폴더 = theme.php(색상)·header.php·home.php·product_card.php.
  데이터·기능·모바일셸·푸터는 전 테마 공유. Tailwind 색상은 테마별 자동 주입.
- ✅ **완료 시 → git push**

---

## 과제 5. 프로젝트 복제 / 멀티사이트 자동화

### 검토 결과 (Docker 중심으로 재구성)
- 목표: 회사 내부 Linux 서버에서 운영. Laragon은 Windows 데스크탑 전용이라 부적합.
- **방식 확정**: Docker 컨테이너화. PHP-Apache + MySQL을 docker-compose로 묶음.
  - OS 무관(Win/Mac/Linux), 환경 동일성 보장, 한 줄 배포(`docker compose up -d`).
  - 회사 서버 부팅 시 자동 시작(restart 정책), GUI 불필요.
  - 멀티사이트 = 인스턴스별 `.env` 다르게 + 컴포즈 N개.
- 라이선스: Docker Engine(Linux) 무료. Docker Desktop은 소규모 회사 무료.

### 단계
- [x] 5-1. Dockerfile (PHP 8.2 + Apache + pdo_mysql/mbstring/gd + rewrite + DocumentRoot=public)
- [x] 5-2. docker/apache.conf + docker/php.ini (업로드 25M·timezone)
- [x] 5-3. docker-compose.yml (web + db, 헬스체크, named 볼륨, 포트 8082/3307)
- [x] 5-4. .env.example + .env + .dockerignore + .gitignore — 인스턴스 가변요소 분리
- [x] 5-5. config.php를 환경변수 우선으로 (`getenv('DB_HOST')` 등) — Laragon 폴백 유지. 검증 통과.
- [x] 5-6. schema.sql 소문자 통일(`sunvolt-webpage`) + MySQL `lower_case_table_names=1` 적용
- [x] 5-7. `docker compose up --build` → **HTTP 200**·sitemap 43URL·어드민 페이지 OK
- [x] 5-8. 2번째 인스턴스(`koreatech`) 띄움 — 포트 8083/3308·DB `koreatech-web`·테마 `bold`·이름 `코리아텍 배터리`. **두 사이트 동시 가동 확인**.
- [x] 5-8a. 멀티사이트 초기화 이슈 발견 → `docker/db-init.sh`로 schema.sql의 `CREATE DATABASE/USE` 자동 strip → MYSQL_DATABASE env 기준 주입 (Laragon용 원본 schema.sql 보존)
- [ ] 5-9. (보류) 내부 회사 서버 배포 절차 문서화 — 서버 정보 받은 뒤 작업
- **테스트**: ✅ 단일/멀티 인스턴스 모두 정상. magazine·bold 테마 동시 렌더 확인. Laragon 의존성 제거 완료.
- ✅ **완료 시 → git push**

---

## 최종 보고
- [ ] 과제 1~5 진행 결과 보고서 작성 — 완료/미완/막힌 지점 정리 → `docs/REPORT.md`

---

## 진행 로그
> 작업할 때마다 한 줄씩 추가 (날짜 · 무엇을 · 결과).

- 2026-04-29 — ACTION_PLAN 수립, 과제 1 착수
- 2026-04-29 — 과제 1 (SEO/AEO) 구현 완료: robots·sitemap·OG·JSON-LD(Organization/WebSite/Product/BlogPosting/Article/Breadcrumb/FAQPage)·FAQ페이지. 코드 검증 통과, 사용자 브라우저 확인 대기.
- 2026-05-22 — 과제 2 (반응형+모바일 모드) 구현 완료: device.php 디바이스 감지, header/footer 셸 분기, 모바일 헤더+드로어+하단탭바, Vary 헤더. 전 페이지 렌더 검증 통과. → feature/seo-mobile 브랜치 push 완료.
- 2026-05-22 — 과제 4 (테마 시스템) 구현 완료: 테마 로더 + 5개 테마(classic/modern/magazine/bold/compact) + 어드민 테마 전환 UI. 20개 테마파일 검증, 5종 렌더 통과. → feature/theme-system 브랜치 push 완료.
- 2026-05-22 — 과제 4 후속 수정 4건: ①compact 히어로 깨짐(Swiper×grid 폭주) → 정적배너 교체 ②공유 푸터에 관리자 링크(전 테마) ③미리보기를 ?preview_theme= 방식으로 ④어드민 색상 편집(color_overrides.json, primary/accent 직접 지정). 검증 통과 → feature/theme-system push.
- 2026-05-28 — 과제 5 (Docker 컨테이너화 + 멀티사이트) 구현: Dockerfile/compose, env 분리, config 환경변수 지원, db-init.sh로 멀티사이트 안전 초기화. **2개 인스턴스(sunvolt:8082, koreatech:8083) 동시 가동 검증** — 각자 다른 DB/테마/사이트명.

## Git 브랜치 현황
- `feature/seo-mobile` — 과제 1(SEO/AEO) + 과제 2(반응형/모바일). push 완료.
- `feature/theme-system` — 과제 4(테마 시스템) + 후속 수정. push 완료. (seo-mobile에서 분기)
- `feature/docker-multisite` — 과제 5(Docker + 멀티사이트). push 예정. (theme-system에서 분기)
- master 병합은 사용자가 PR 검토 후 진행 예정.

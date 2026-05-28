<?php
/**
 * DB 접속 정보 + 사이트 전역 설정
 *
 * 환경변수 우선 (Docker 컨테이너) → 없으면 코드 기본값 (Laragon 로컬)
 *
 * Laragon 로컬:
 *   - host: 127.0.0.1 / user: root / pass: (빈) / db: sunvolt-webpage
 *
 * Docker:
 *   - host: db (compose 서비스명) / 나머지는 .env 의 DB_* 값
 */

// 어드민 테마 설정이 자동 갱신하는 줄 ↓ (정규식으로 X 부분만 교체됨)
$theme_default = 'magazine';
// ↑ env SITE_THEME 가 있으면 그게 우선됨 (멀티사이트 .env 별 설정용).

$env = function (string $key, $default) {
    $v = getenv($key);
    return ($v !== false && $v !== '') ? $v : $default;
};

return [
    'db' => [
        'host'    => $env('DB_HOST',  '127.0.0.1'),
        'port'    => (int)$env('DB_PORT', '3306'),
        'name'    => $env('DB_NAME',  'sunvolt-webpage'),
        'user'    => $env('DB_USER',  'root'),
        'pass'    => $env('DB_PASS',  ''),
        'charset' => 'utf8mb4',
    ],
    'site' => [
        'name'        => $env('SITE_NAME',   '썬볼트 배터리몰'),
        'tagline'     => $env('SITE_TAGLINE','전 차종·산업용·전동모빌리티 배터리 전문'),
        'phone'       => $env('SITE_PHONE',  '02-2661-0135'),
        'business_no' => $env('SITE_BIZNO',  '829-85-00989'),
        'address'     => $env('SITE_ADDR',   '10005 경기 김포시 하성면 하성로795번길 70 에이동 (마조리)'),
        'company'     => $env('SITE_COMPANY','Sunvolt'),
    ],
    'paths' => [
        'base_url'     => '',
        'image_base'   => '/assets/images/products/',
        'placeholder'  => 'https://placehold.co/800x800/0A2540/FFC107?text=No+Image',
    ],
    'paging' => [
        'per_page' => 12,
    ],

    // 레이아웃 테마 — env(SITE_THEME) > 위의 $theme_default
    'theme' => $env('SITE_THEME', $theme_default),
];

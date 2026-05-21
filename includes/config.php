<?php
/**
 * DB 접속 정보 + 사이트 전역 설정
 *
 * Laragon 기본값:
 *   - host: 127.0.0.1
 *   - user: root
 *   - pass: (빈 문자열)
 *   - db:   sunvolt-webpage
 */

return [
    'db' => [
        'host'    => '127.0.0.1',
        'port'    => 3306,
        'name'    => 'sunvolt-webpage',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],
    'site' => [
        'name'        => '썬볼트 배터리몰',
        'tagline'     => '전 차종·산업용·전동모빌리티 배터리 전문',
        'phone'       => '02-2661-0135',
        'business_no' => '829-85-00989',
        'address'     => '10005 경기 김포시 하성면 하성로795번길 70 에이동 (마조리)',
        'company'     => 'Sunvolt',
    ],
    'paths' => [
        'base_url'     => '',
        'image_base'   => '/assets/images/products/',
        'placeholder'  => 'https://placehold.co/800x800/0A2540/FFC107?text=No+Image',
    ],
    'paging' => [
        'per_page' => 12,
    ],
];

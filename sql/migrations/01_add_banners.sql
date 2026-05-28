-- ====================================================================
-- 마이그레이션 #01: 메인 히어로 배너 테이블 추가
--
-- 사용법: HeidiSQL에서 이 파일 열고 F9
--   기존 데이터는 유지됨. banners 테이블만 새로 생성.
-- ====================================================================

USE `sunvolt-webpage`;

DROP TABLE IF EXISTS `banners`;

CREATE TABLE `banners` (
  `id`           INT          NOT NULL AUTO_INCREMENT,
  `title`        VARCHAR(255) NOT NULL,
  `subtitle`     VARCHAR(500) DEFAULT NULL,
  `image_url`    VARCHAR(500) NOT NULL,
  `cta_text`     VARCHAR(100) DEFAULT NULL,
  `cta_url`      VARCHAR(500) DEFAULT NULL,
  `accent_label` VARCHAR(80)  DEFAULT NULL,
  `text_align`   VARCHAR(10)  DEFAULT 'left',
  `sort_order`   INT          DEFAULT 0,
  `published`    TINYINT      NOT NULL DEFAULT 1,
  `created_at`   DATETIME     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_published_order` (`published`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 시드 배너 3개
INSERT INTO `banners` (`title`, `subtitle`, `image_url`, `cta_text`, `cta_url`, `accent_label`, `text_align`, `sort_order`, `published`) VALUES
('모든 차량과 장비의 파워를 책임집니다',
 '승용차부터 산업용 ESS, 전동 모빌리티까지 — 정품 인증 배터리만',
 'https://placehold.co/1920x720/0A2540/FFC107?text=SUNVOLT+BATTERY',
 '자동차 배터리 보기',
 '/shop/list.php?ca_id=10',
 'BATTERY · CHARGER · ESS', 'left', 1, 1),

('산업용 리튬인산철 ESS 신제품 출시',
 'BMS 내장 · 4000회 사이클 · 안전성 보장 — 데이터센터·캠핑카·요트',
 'https://placehold.co/1920x720/004D40/FFC107?text=LiFePO4+ESS+New',
 'ESS 라인업 보기',
 '/shop/list.php?ca_id=22',
 'NEW · 신제품', 'left', 2, 1),

('B2B 대량 주문 단체할인',
 '택시 사업자, 산업체, 솔라 시공업체 — 100개 이상 견적 문의',
 'https://placehold.co/1920x720/FFC107/0A2540?text=B2B+BULK+ORDER',
 '견적 문의하기',
 '#',
 'B2B 전용', 'left', 3, 1);

SELECT '✓ banners 테이블 생성 + 시드 3개 입력 완료' AS message,
       (SELECT COUNT(*) FROM banners) AS banners;

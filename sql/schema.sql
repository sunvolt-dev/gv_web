-- ====================================================================
-- Webpage 프로젝트: 썬볼트 스타일 배터리 쇼핑몰 데모
-- DB 스키마 + 시드 데이터
--
-- 사용법:
--   1) HeidiSQL(Laragon 내장) 또는 MySQL CLI에서 이 파일 import
--   2) DB 'webpage' 자동 생성됨
--   3) 어드민 계정: admin / admin1234
-- ====================================================================

CREATE DATABASE IF NOT EXISTS `webpage`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;
USE `webpage`;

-- 기존 테이블 정리 (재실행 안전)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `product_options`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `admin_users`;
SET FOREIGN_KEY_CHECKS = 1;

-- ====================================================================
-- 카테고리 (자기참조 트리)
-- ====================================================================
CREATE TABLE `categories` (
  `ca_id`     INT          NOT NULL AUTO_INCREMENT,
  `ca_name`   VARCHAR(100) NOT NULL,
  `ca_parent` INT          DEFAULT NULL,
  `ca_order`  INT          DEFAULT 0,
  PRIMARY KEY (`ca_id`),
  KEY `idx_ca_parent` (`ca_parent`),
  CONSTRAINT `fk_ca_parent` FOREIGN KEY (`ca_parent`)
    REFERENCES `categories`(`ca_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- 상품
-- ====================================================================
CREATE TABLE `products` (
  `it_id`         INT           NOT NULL AUTO_INCREMENT,
  `it_name`       VARCHAR(255)  NOT NULL,
  `ca_id`         INT           NOT NULL,
  `it_price`      INT           NOT NULL DEFAULT 0,
  `it_sell_price` INT           NOT NULL DEFAULT 0,
  `it_stock`      INT           NOT NULL DEFAULT 0,
  `it_summary`    VARCHAR(500)  DEFAULT NULL,
  `it_desc`       LONGTEXT,
  `it_img1`       VARCHAR(255)  DEFAULT NULL,
  `it_img2`       VARCHAR(255)  DEFAULT NULL,
  `it_img3`       VARCHAR(255)  DEFAULT NULL,
  `it_img4`       VARCHAR(255)  DEFAULT NULL,
  `it_img5`       VARCHAR(255)  DEFAULT NULL,
  `it_use`        TINYINT       DEFAULT 1,
  `it_new`        TINYINT       DEFAULT 0,
  `it_best`       TINYINT       DEFAULT 0,
  `created_at`    DATETIME      DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`it_id`),
  KEY `idx_pr_ca` (`ca_id`),
  KEY `idx_pr_use` (`it_use`),
  CONSTRAINT `fk_pr_ca` FOREIGN KEY (`ca_id`)
    REFERENCES `categories`(`ca_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- 상품 옵션 (용량/타입 등)
-- ====================================================================
CREATE TABLE `product_options` (
  `io_id`        INT          NOT NULL AUTO_INCREMENT,
  `it_id`        INT          NOT NULL,
  `io_type`      VARCHAR(50)  NOT NULL,
  `io_value`     VARCHAR(100) NOT NULL,
  `io_price_add` INT          DEFAULT 0,
  PRIMARY KEY (`io_id`),
  KEY `idx_op_it` (`it_id`),
  CONSTRAINT `fk_op_it` FOREIGN KEY (`it_id`)
    REFERENCES `products`(`it_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- 어드민 사용자
-- ====================================================================
CREATE TABLE `admin_users` (
  `id`            INT          NOT NULL AUTO_INCREMENT,
  `username`      VARCHAR(50)  NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at`    DATETIME     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ====================================================================
-- 시드 데이터
-- ====================================================================

-- 어드민 계정 (admin / admin1234)
INSERT INTO `admin_users` (`username`, `password_hash`) VALUES
('admin', '$2y$10$7Q8u1dmi5Bg//meRFUAVVuMZdOhttwQOlApl1fXn.4KzgUcqgtGCO');

-- 대분류 카테고리
INSERT INTO `categories` (`ca_id`, `ca_name`, `ca_parent`, `ca_order`) VALUES
(10, '자동차배터리',         NULL, 1),
(20, '산업용배터리',         NULL, 2),
(30, '전동모빌리티배터리',   NULL, 3),
(40, '충전기·액세서리',      NULL, 4);

-- 중분류 카테고리
INSERT INTO `categories` (`ca_id`, `ca_name`, `ca_parent`, `ca_order`) VALUES
(11, '승용차용',     10, 1),
(12, 'SUV/상용',     10, 2),
(21, 'UPS용',        20, 1),
(22, 'ESS/리튬',     20, 2),
(31, '전동킥보드',   30, 1),
(32, '전기자전거',   30, 2),
(41, '충전기',       40, 1),
(42, '액세서리',     40, 2);

-- 상품
INSERT INTO `products`
(`it_name`, `ca_id`, `it_price`, `it_sell_price`, `it_stock`, `it_summary`, `it_desc`,
 `it_img1`, `it_img2`, `it_img3`, `it_use`, `it_new`, `it_best`) VALUES

-- ───── 자동차 배터리 / 승용차용 (ca_id=11) ─────
('솔라이트 SOL60 60Ah MF 자동차 배터리', 11, 110000, 89000, 80,
 '국산 1위 솔라이트 무보수형 60Ah · 승용차 전용',
 '<h3>국산 1위 솔라이트 자동차 배터리</h3><p>완전 무보수형(MF, Maintenance Free) 60Ah 자동차 배터리. 한국의 대표 자동차 배터리 브랜드 솔라이트의 베스트셀러 모델.</p><h4>주요 특징</h4><ul><li>용량: 60Ah / CCA 580A</li><li>크기: 230 x 175 x 220mm</li><li>무게: 14.8kg</li><li>적용 차종: 아반떼, 소나타, K3, K5 등 국산 준중형/중형</li></ul><p>품질보증 1년, 무료배송.</p>',
 'https://placehold.co/800x800/0A2540/FFC107?text=SOL60',
 'https://placehold.co/800x800/0A2540/FFFFFF?text=SOL60+Detail',
 'https://placehold.co/800x800/FFC107/0A2540?text=SOL60+Side',
 1, 0, 1),

('솔라이트 SOL80 80Ah MF 자동차 배터리', 11, 145000, 119000, 60,
 '대용량 80Ah · 중형/대형 세단용 무보수 배터리',
 '<h3>솔라이트 SOL80 대용량 자동차 배터리</h3><p>중대형 세단용 80Ah 무보수형 배터리. 그랜저, K7, K9 등 대형 세단에 적합.</p><h4>주요 특징</h4><ul><li>용량: 80Ah / CCA 720A</li><li>크기: 260 x 175 x 220mm</li><li>무게: 19.5kg</li></ul>',
 'https://placehold.co/800x800/0A2540/FFC107?text=SOL80',
 'https://placehold.co/800x800/0A2540/FFFFFF?text=SOL80+Detail',
 NULL, 1, 0, 1),

('아트라스BX 60L 60Ah 자동차 배터리', 11, 95000, 79000, 100,
 '아트라스BX 60Ah · 가성비 No.1 보급형',
 '<h3>아트라스BX 60Ah 자동차 배터리</h3><p>가성비 좋은 아트라스BX의 60Ah 모델. 무보수형으로 관리가 편리합니다.</p><ul><li>용량: 60Ah / CCA 540A</li><li>크기: 232 x 173 x 225mm</li></ul>',
 'https://placehold.co/800x800/D32F2F/FFFFFF?text=ATLAS+60',
 'https://placehold.co/800x800/D32F2F/FFC107?text=ATLAS+Side',
 NULL, 1, 0, 0),

('델코 GB60 60Ah AGM 스타트스톱 배터리', 11, 195000, 165000, 35,
 'AGM · 스타트스톱(ISG) 차량 전용',
 '<h3>델코 GB60 AGM 배터리</h3><p>스타트스톱(ISG) 시스템 차량을 위한 AGM 타입 고성능 배터리.</p><ul><li>타입: AGM</li><li>용량: 60Ah / CCA 680A</li><li>적용: ISG 장착 차량</li></ul>',
 'https://placehold.co/800x800/1B5E20/FFC107?text=DELCO+GB60',
 'https://placehold.co/800x800/1B5E20/FFFFFF?text=AGM+Type',
 NULL, 1, 1, 0),

-- ───── 자동차 배터리 / SUV·상용 (ca_id=12) ─────
('솔라이트 SOL100 100Ah 대형차 배터리', 12, 175000, 145000, 40,
 '100Ah 대용량 · SUV·상용차 전용',
 '<h3>솔라이트 SOL100 100Ah 배터리</h3><p>대형 SUV, 1톤 트럭, 캠핑카 등에 적합한 100Ah 대용량 무보수 배터리.</p><ul><li>용량: 100Ah / CCA 850A</li><li>크기: 305 x 175 x 220mm</li><li>적용: 카니발, 모하비, 포터, 봉고</li></ul>',
 'https://placehold.co/800x800/0A2540/FFC107?text=SOL100',
 'https://placehold.co/800x800/0A2540/FFFFFF?text=SOL100+Detail',
 NULL, 1, 0, 1),

('아트라스BX 90L 90Ah 상용차 배터리', 12, 135000, 115000, 50,
 'SUV·상용차용 90Ah · 가성비 좋은 대용량',
 '<h3>아트라스BX 90Ah</h3><p>SUV 및 상용차에 적합한 90Ah 배터리.</p><ul><li>용량: 90Ah / CCA 780A</li></ul>',
 'https://placehold.co/800x800/D32F2F/FFFFFF?text=ATLAS+90',
 NULL, NULL, 1, 0, 0),

-- ───── 산업용 / UPS (ca_id=21) ─────
('GS배터리 EB12100 12V 100Ah UPS 배터리', 21, 380000, 340000, 25,
 '데이터센터·서버실 UPS 전용 100Ah',
 '<h3>GS EB12100 UPS 배터리</h3><p>데이터센터, 서버실, 통신장비용 UPS 백업 배터리. 5년 장수명 설계.</p><ul><li>전압: 12V / 용량: 100Ah</li><li>설계수명: 5년</li><li>크기: 330 x 173 x 215mm</li></ul>',
 'https://placehold.co/800x800/263238/FFC107?text=GS+UPS+100Ah',
 'https://placehold.co/800x800/263238/FFFFFF?text=UPS+Detail',
 NULL, 1, 0, 1),

('로켓배터리 ES200-12 12V 200Ah 산업용', 21, 650000, 580000, 15,
 '대용량 산업용 200Ah · 무정전 전원',
 '<h3>로켓 ES200 산업용 배터리</h3><p>대용량 200Ah 산업용 딥사이클 배터리.</p><ul><li>전압: 12V / 용량: 200Ah</li><li>딥사이클 설계 · 1500회 충방전</li></ul>',
 'https://placehold.co/800x800/263238/FFC107?text=ROCKET+200Ah',
 NULL, NULL, 1, 0, 0),

-- ───── 산업용 / ESS·리튬 (ca_id=22) ─────
('리튬인산철 LiFePO4 12.8V 100Ah ESS 배터리', 22, 920000, 850000, 20,
 '안전한 리튬인산철 · 10년 수명 · ESS·캠핑카',
 '<h3>리튬인산철(LiFePO4) 12.8V 100Ah</h3><p>안전성과 수명이 뛰어난 리튬인산철 화학 기반의 차세대 배터리. ESS, 캠핑카, 요트, 솔라 시스템 등 폭넓게 사용.</p><ul><li>화학: LiFePO4 (리튬인산철)</li><li>용량: 12.8V 100Ah / 1280Wh</li><li>사이클: 4000회 이상 (80% DoD)</li><li>BMS 내장 · 과충전·과방전·단락 보호</li></ul>',
 'https://placehold.co/800x800/004D40/FFC107?text=LiFePO4+100Ah',
 'https://placehold.co/800x800/004D40/FFFFFF?text=BMS+Built-in',
 'https://placehold.co/800x800/FFC107/004D40?text=ESS+Ready',
 1, 1, 1),

('LiFePO4 24V 100Ah ESS 모듈', 22, 1850000, 1690000, 8,
 '24V 100Ah · 솔라·전기차 컨버전 ESS',
 '<h3>리튬인산철 24V 100Ah ESS 모듈</h3><p>대형 ESS, 전기차 컨버전, 산업용 솔라 시스템용 24V 100Ah.</p><ul><li>전압: 25.6V (정격 24V)</li><li>용량: 100Ah / 2560Wh</li><li>BMS 내장</li></ul>',
 'https://placehold.co/800x800/004D40/FFC107?text=24V+ESS',
 NULL, NULL, 1, 1, 0),

-- ───── 전동모빌리티 / 킥보드 (ca_id=31) ─────
('전동킥보드 36V 10Ah 리튬 배터리팩', 31, 180000, 159000, 45,
 '36V 10Ah · 샤오미 호환 · 30km 주행',
 '<h3>전동킥보드 36V 10Ah 배터리팩</h3><p>대표적 전동킥보드 모델 호환. 약 30km 주행 가능.</p><ul><li>전압: 36V / 용량: 10Ah / 360Wh</li><li>BMS 내장 · 방수 IP54</li><li>호환: 샤오미 M365, 일반 36V 킥보드</li></ul>',
 'https://placehold.co/800x800/1A237E/FFC107?text=36V+10Ah',
 'https://placehold.co/800x800/1A237E/FFFFFF?text=Kickboard+Pack',
 NULL, 1, 0, 1),

('전동킥보드 48V 13Ah 리튬 배터리팩', 31, 250000, 219000, 30,
 '48V 13Ah · 고속 킥보드 · 50km 주행',
 '<h3>전동킥보드 48V 13Ah</h3><p>고속 전동킥보드용 대용량 배터리팩. 50km 이상 주행.</p><ul><li>전압: 48V / 용량: 13Ah / 624Wh</li></ul>',
 'https://placehold.co/800x800/1A237E/FFC107?text=48V+13Ah',
 NULL, NULL, 1, 0, 0),

-- ───── 전동모빌리티 / 자전거 (ca_id=32) ─────
('전기자전거 36V 14Ah 다운튜브 배터리', 32, 220000, 189000, 25,
 '36V 14Ah · 다운튜브형 · 60km 주행',
 '<h3>전기자전거 36V 14Ah 다운튜브 배터리</h3><p>다운튜브 마운트 방식의 전기자전거 배터리. Samsung INR18650-29E 셀 사용.</p><ul><li>전압: 36V / 용량: 14Ah / 504Wh</li><li>셀: 삼성 SDI 18650</li><li>주행거리: 약 60km (PAS 모드)</li></ul>',
 'https://placehold.co/800x800/0D47A1/FFC107?text=Ebike+36V',
 'https://placehold.co/800x800/0D47A1/FFFFFF?text=DownTube',
 NULL, 1, 1, 0),

('전기자전거 48V 17.5Ah 고출력 배터리', 32, 320000, 279000, 18,
 '48V 17.5Ah · 산악·고출력 전기자전거용',
 '<h3>전기자전거 48V 17.5Ah</h3><p>산악 자전거, 고출력 모터(750W~1000W)용 대용량 배터리.</p><ul><li>전압: 48V / 용량: 17.5Ah / 840Wh</li></ul>',
 'https://placehold.co/800x800/0D47A1/FFC107?text=Ebike+48V',
 NULL, NULL, 1, 0, 1),

-- ───── 충전기·액세서리 / 충전기 (ca_id=41) ─────
('12V 자동차 배터리 자동충전기 6A', 41, 45000, 39000, 100,
 '12V 자동충전 · 펄스 디설페이션',
 '<h3>12V 자동충전기 6A</h3><p>자동차 배터리 전용 12V 6A 자동충전기. 과충전 방지 + 펄스 디설페이션 기능.</p><ul><li>입력: AC 220V / 출력: DC 12V 6A</li><li>자동 충전 단계 · 유지 충전</li></ul>',
 'https://placehold.co/800x800/FF6F00/FFFFFF?text=Charger+12V',
 NULL, NULL, 1, 0, 0),

('24V 산업용 배터리 충전기 10A', 41, 95000, 85000, 40,
 '24V 10A · 지게차·산업용 배터리 전용',
 '<h3>24V 산업용 배터리 충전기</h3><p>지게차, 청소차 등 24V 산업용 배터리 충전기.</p><ul><li>입력: AC 220V / 출력: DC 24V 10A</li></ul>',
 'https://placehold.co/800x800/FF6F00/FFFFFF?text=Charger+24V',
 NULL, NULL, 1, 0, 0),

('48V 리튬 전용 자동충전기 5A', 41, 75000, 65000, 50,
 '48V 리튬배터리 전용 · CC/CV 충전',
 '<h3>48V 리튬 전용 충전기</h3><p>전동킥보드·자전거의 48V 리튬배터리(13S) 전용 충전기. CC/CV 자동 전환.</p><ul><li>출력: 54.6V 5A (만충 전압)</li><li>리튬 전용 (납축전지 사용 불가)</li></ul>',
 'https://placehold.co/800x800/FF6F00/FFFFFF?text=Lithium+48V',
 NULL, NULL, 1, 1, 0),

-- ───── 충전기·액세서리 / 액세서리 (ca_id=42) ─────
('자동차 배터리 단자 클램프 (양극+음극 세트)', 42, 8000, 6500, 200,
 '구리 도금 단자 · 부식 방지',
 '<h3>배터리 단자 클램프 세트</h3><p>자동차 배터리용 단자 클램프 양극+음극 세트.</p>',
 'https://placehold.co/800x800/616161/FFC107?text=Clamp+Set',
 NULL, NULL, 1, 0, 0),

('점프 케이블 1500A 4M 부스터 케이블', 42, 35000, 29000, 80,
 '1500A · 4M 길이 · 방전 시 응급용',
 '<h3>점프 케이블 1500A</h3><p>방전된 자동차 배터리 응급 점프 시 사용하는 부스터 케이블.</p><ul><li>전류: 1500A</li><li>길이: 4M</li><li>난연 PVC 절연</li></ul>',
 'https://placehold.co/800x800/B71C1C/FFC107?text=Jump+Cable',
 NULL, NULL, 1, 0, 0),

('디지털 배터리 테스터 12V 6V 차량 진단', 42, 65000, 55000, 60,
 'CCA 측정 · LCD 디스플레이 · 6V/12V 호환',
 '<h3>디지털 배터리 테스터</h3><p>자동차 배터리 상태(CCA, 전압, 내부저항)를 측정하는 디지털 테스터.</p>',
 'https://placehold.co/800x800/424242/FFC107?text=Battery+Tester',
 NULL, NULL, 1, 0, 0);

-- 상품 옵션 (대표 상품 위주로)
INSERT INTO `product_options` (`it_id`, `io_type`, `io_value`, `io_price_add`) VALUES
-- 솔라이트 SOL60 (it_id=1) — 단자 방향
(1, '단자방향', '정극(+) 좌측', 0),
(1, '단자방향', '정극(+) 우측', 0),
-- 솔라이트 SOL80 (it_id=2)
(2, '단자방향', '정극(+) 좌측', 0),
(2, '단자방향', '정극(+) 우측', 0),
-- 리튬인산철 12.8V 100Ah (it_id=9) — 용량 옵션
(9, '용량',     '50Ah  (₩-150,000)',  -150000),
(9, '용량',     '100Ah (기본)',         0),
(9, '용량',     '200Ah (₩+550,000)',   550000),
-- 전동킥보드 36V 10Ah (it_id=11)
(11, '커넥터타입', 'XT60',           0),
(11, '커넥터타입', 'GX16-3핀',       0),
-- 12V 자동차 충전기 (it_id=15)
(15, '플러그타입', '국내용 (220V 2핀)',  0),
(15, '플러그타입', '시거잭 변환 포함',   3000);

-- 끝
SELECT '✓ Webpage DB 시드 완료' AS message,
       (SELECT COUNT(*) FROM categories) AS categories,
       (SELECT COUNT(*) FROM products)   AS products,
       (SELECT COUNT(*) FROM product_options) AS options;

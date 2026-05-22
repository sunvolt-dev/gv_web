<?php
require_once __DIR__ . '/../includes/functions.php';

/* FAQ 항목 — 질문/답변. AEO(답변엔진)에 FAQPage 구조화 데이터로 노출됨. */
$faqs = [
    [
        'q' => '배터리 주문하면 배송은 얼마나 걸리나요?',
        'a' => '평일 오후 3시 이전 결제 완료 건은 당일 출고되며, 보통 1~2일 내 수령 가능합니다. 주말·공휴일 주문은 다음 영업일에 출고됩니다. 제주·도서산간 지역은 1~2일 추가 소요될 수 있습니다.',
    ],
    [
        'q' => '내 차에 맞는 자동차 배터리는 어떻게 고르나요?',
        'a' => '차량 제조사·모델·연식에 따라 적합한 배터리 규격(용량 Ah, 단자 방향, 크기)이 다릅니다. 기존 배터리에 표기된 규격(예: 60Ah)을 확인하시거나, 차종을 고객센터로 알려주시면 적합 모델을 안내해 드립니다. 상품 상세페이지의 적용 차종 정보도 참고하세요.',
    ],
    [
        'q' => '배터리 교체 시기는 어떻게 알 수 있나요?',
        'a' => '자동차 배터리 평균 수명은 3~5년입니다. 시동이 약해지거나 늦게 걸림, 헤드라이트가 어두워짐, 계기판 배터리 경고등 점등, 단자 부식, 케이스 부풀음 등의 신호가 보이면 점검·교체를 권장합니다.',
    ],
    [
        'q' => '무료배송 조건이 어떻게 되나요?',
        'a' => '5만원 이상 주문 시 전국 무료배송입니다. 5만원 미만은 배송비 3,000원이 부과되며, 제주·도서산간 지역은 추가 배송비가 발생할 수 있습니다.',
    ],
    [
        'q' => '구매한 배터리 교환·반품이 가능한가요?',
        'a' => '수령 후 7일 이내 미사용·미개봉 제품에 한해 교환·반품이 가능합니다. 단순 변심의 경우 왕복 배송비는 고객 부담입니다. 제품 불량인 경우 100% 무상 교환해 드립니다.',
    ],
    [
        'q' => '리튬인산철(LiFePO4) 배터리와 납축전지의 차이는 무엇인가요?',
        'a' => '리튬인산철 배터리는 납축전지 대비 수명이 약 4배(4,000회 이상 충방전), 무게는 약 50% 가볍고, 완전 방전 후에도 회복이 가능합니다. 초기 비용은 높지만 장기적으로 경제적이며 ESS·캠핑카·전동 모빌리티에 적합합니다.',
    ],
    [
        'q' => '폐배터리 수거도 해주나요?',
        'a' => '네, 신규 배터리 구매 고객에 한해 기존 폐배터리 수거 서비스를 제공합니다. 배송 기사가 신품 배송 시 폐배터리를 회수하며, 자세한 사항은 주문 시 요청사항에 기재하거나 고객센터로 문의해 주세요.',
    ],
    [
        'q' => '결제 수단은 어떤 것이 있나요?',
        'a' => '신용카드, 실시간 계좌이체, 무통장입금, 간편결제를 지원합니다. 법인 구매의 경우 세금계산서 발행이 가능합니다.',
    ],
    [
        'q' => '세금계산서 발행이 되나요?',
        'a' => '네, 사업자 고객은 세금계산서 발행이 가능합니다. 주문 시 사업자 정보를 입력하시거나 고객센터로 사업자등록증을 전달해 주시면 발행해 드립니다.',
    ],
    [
        'q' => 'B2B 대량 구매 시 할인이 있나요?',
        'a' => '택시 사업자, 산업체, 솔라 시공업체 등 100개 이상 대량 주문 시 별도 견적과 추가 할인, 직배송을 지원합니다. 메인 페이지의 B2B 견적 문의 또는 고객센터로 연락해 주세요.',
    ],
];

$page_title = '자주 묻는 질문 (FAQ)';
$page_desc  = '썬볼트 배터리몰 자주 묻는 질문 — 배송·교환·반품, 배터리 선택, 결제, B2B 구매 안내';

/* FAQPage 구조화 데이터 (AEO 핵심) + BreadcrumbList */
$json_ld = [
    [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => array_map(fn($f) => [
            '@type'          => 'Question',
            'name'           => $f['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
        ], $faqs),
    ],
    breadcrumb_ld(['HOME' => '/', '자주 묻는 질문' => '/faq.php']),
];

require __DIR__ . '/../includes/header.php';
?>

<section class="bg-gray-50 border-b">
  <div class="max-w-3xl mx-auto px-4 py-10 md:py-14">
    <nav class="text-xs text-gray-500 mb-3">
      <a href="/" class="hover:text-primary">HOME</a> › <span class="text-primary font-semibold">FAQ</span>
    </nav>
    <h1 class="text-3xl md:text-4xl font-extrabold text-primary">자주 묻는 질문</h1>
    <p class="text-sm md:text-base text-gray-500 mt-2">배송·교환·배터리 선택 등 고객님이 가장 많이 묻는 질문을 모았습니다.</p>
  </div>
</section>

<section class="max-w-3xl mx-auto px-4 py-8 md:py-12">
  <div class="space-y-3" x-data="{ open: 0 }">
    <?php foreach ($faqs as $i => $f): ?>
    <div class="border rounded-xl overflow-hidden bg-white">
      <button type="button" @click="open = (open === <?= $i + 1 ?> ? 0 : <?= $i + 1 ?>)"
              class="w-full flex items-center justify-between gap-4 px-5 py-4 text-left">
        <span class="flex items-start gap-3">
          <span class="shrink-0 w-6 h-6 rounded-full bg-primary text-accent text-xs font-bold flex items-center justify-center">Q</span>
          <span class="font-bold text-gray-900"><?= h($f['q']) ?></span>
        </span>
        <span class="shrink-0 text-gray-400 text-xl transition-transform"
              :class="open === <?= $i + 1 ?> ? 'rotate-45' : ''">+</span>
      </button>
      <div x-show="open === <?= $i + 1 ?>" x-cloak
           x-transition.opacity.duration.200ms class="px-5 pb-5 border-t bg-gray-50/50">
        <div class="flex items-start gap-3 pt-4">
          <span class="shrink-0 w-6 h-6 rounded-full bg-accent text-primary text-xs font-bold flex items-center justify-center">A</span>
          <p class="text-sm text-gray-700 leading-relaxed"><?= h($f['a']) ?></p>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="mt-10 p-6 bg-primary rounded-2xl text-center text-white">
    <h2 class="font-extrabold text-lg mb-1">원하는 답변을 찾지 못하셨나요?</h2>
    <p class="text-sm text-white/70 mb-4">고객센터로 문의해 주시면 친절히 안내해 드립니다.</p>
    <a href="tel:<?= h($site['phone']) ?>"
       class="inline-block px-6 py-3 rounded-lg bg-accent text-primary font-bold">
      고객센터 전화하기 <?= h($site['phone']) ?>
    </a>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>

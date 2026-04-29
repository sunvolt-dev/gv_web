<?php
require_once __DIR__ . '/../../includes/functions.php';

$it_id = (int)($_GET['it_id'] ?? 0);
$p = $it_id ? get_product($it_id) : null;

if (!$p) {
    http_response_code(404);
    $page_title = '상품을 찾을 수 없습니다';
    require __DIR__ . '/../../includes/header.php';
    echo '<div class="max-w-3xl mx-auto px-4 py-20 text-center">';
    echo '<div class="text-6xl mb-4">🔋</div>';
    echo '<h1 class="text-2xl font-bold text-primary mb-2">상품을 찾을 수 없습니다</h1>';
    echo '<p class="text-gray-500 mb-6">요청하신 상품이 삭제되었거나 판매가 중단되었습니다.</p>';
    echo '<a href="/shop/list.php" class="inline-block px-6 py-3 rounded-lg bg-primary text-white font-bold">전체 상품 보기</a>';
    echo '</div>';
    require __DIR__ . '/../../includes/footer.php';
    exit;
}

$cat        = get_category((int)$p['ca_id']);
$breadcrumb = category_breadcrumb((int)$p['ca_id']);
$images     = product_images($p);
$has_sale   = $p['it_sell_price'] > 0 && $p['it_sell_price'] < $p['it_price'];
$base_price = $has_sale ? (int)$p['it_sell_price'] : (int)$p['it_price'];
$rate       = $has_sale ? discount_rate((int)$p['it_price'], (int)$p['it_sell_price']) : 0;
$opt_groups = get_options_grouped((int)$p['it_id']);

$page_title = $p['it_name'];
$page_desc  = (string)$p['it_summary'];

require __DIR__ . '/../../includes/header.php';
?>

<!-- 빵부스러기 -->
<nav class="bg-gray-50 border-b">
  <div class="max-w-7xl mx-auto px-4 py-3 text-xs text-gray-600 flex items-center gap-1.5 flex-wrap">
    <a href="/" class="hover:text-primary">HOME</a>
    <?php foreach ($breadcrumb as $b): ?>
    <span>›</span>
    <a href="<?= h(url('/shop/list.php', ['ca_id' => $b['ca_id']])) ?>" class="hover:text-primary">
      <?= h($b['ca_name']) ?>
    </a>
    <?php endforeach; ?>
    <span>›</span>
    <span class="text-primary font-bold truncate"><?= h($p['it_name']) ?></span>
  </div>
</nav>

<!-- 상품 메인 -->
<section class="max-w-7xl mx-auto px-4 py-8 md:py-12">
  <div class="grid md:grid-cols-2 gap-8 lg:gap-14">

    <!-- 이미지 갤러리 (Swiper) -->
    <div>
      <div class="swiper product-swiper bg-gray-50 rounded-2xl overflow-hidden border">
        <div class="swiper-wrapper">
          <?php foreach ($images as $img): ?>
          <div class="swiper-slide aspect-square">
            <img src="<?= h($img) ?>" alt="<?= h($p['it_name']) ?>"
                 class="w-full h-full object-cover">
          </div>
          <?php endforeach; ?>
        </div>
        <?php if (count($images) > 1): ?>
        <div class="swiper-button-prev !text-primary"></div>
        <div class="swiper-button-next !text-primary"></div>
        <div class="swiper-pagination"></div>
        <?php endif; ?>
      </div>

      <?php if (count($images) > 1): ?>
      <div class="swiper product-thumbs mt-3">
        <div class="swiper-wrapper">
          <?php foreach ($images as $img): ?>
          <div class="swiper-slide aspect-square cursor-pointer rounded-lg overflow-hidden border-2 border-transparent">
            <img src="<?= h($img) ?>" alt="" class="w-full h-full object-cover">
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- 상품 정보 + 옵션 -->
    <div x-data="productOptions(<?= htmlspecialchars(json_encode([
        'base_price' => $base_price,
        'options'    => $opt_groups,
    ], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>)"
         class="space-y-5">

      <!-- 라벨 -->
      <div class="flex gap-1.5 flex-wrap">
        <?php if ($p['it_best']): ?>
        <span class="px-2 py-1 rounded text-[11px] font-bold badge-best">BEST</span>
        <?php endif; ?>
        <?php if ($p['it_new']): ?>
        <span class="px-2 py-1 rounded text-[11px] font-bold badge-new">NEW</span>
        <?php endif; ?>
        <?php if ($cat): ?>
        <span class="px-2 py-1 rounded text-[11px] bg-gray-100 text-gray-600">
          <?= h($cat['ca_name']) ?>
        </span>
        <?php endif; ?>
      </div>

      <h1 class="text-2xl md:text-3xl font-extrabold text-primary leading-tight">
        <?= h($p['it_name']) ?>
      </h1>

      <?php if ($p['it_summary']): ?>
      <p class="text-gray-600"><?= h($p['it_summary']) ?></p>
      <?php endif; ?>

      <!-- 가격 -->
      <div class="bg-gray-50 rounded-xl p-5 space-y-1.5">
        <?php if ($has_sale): ?>
        <div class="flex items-center gap-2">
          <span class="text-sm text-gray-400 line-through"><?= price((int)$p['it_price']) ?></span>
          <span class="px-2 py-0.5 rounded text-[11px] font-bold badge-sale">-<?= $rate ?>%</span>
        </div>
        <?php endif; ?>
        <div class="text-3xl md:text-4xl font-extrabold text-primary"><?= price($base_price) ?></div>
        <div class="text-xs text-gray-500">VAT 포함 · 무료배송</div>
      </div>

      <!-- 옵션 select -->
      <?php if (!empty($opt_groups)): ?>
      <div class="space-y-3">
        <?php foreach ($opt_groups as $type => $opts): ?>
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1.5"><?= h($type) ?></label>
          <select x-model="selected['<?= h($type) ?>']" @change="updatePrice()"
                  class="w-full h-11 px-3 rounded-lg border border-gray-300
                         focus:outline-none focus:ring-2 focus:ring-accent text-sm bg-white">
            <option value="">선택해주세요</option>
            <?php foreach ($opts as $o): ?>
            <option value="<?= h($o['io_value']) ?>" data-add="<?= (int)$o['io_price_add'] ?>">
              <?= h($o['io_value']) ?>
              <?php if ($o['io_price_add'] != 0): ?>
                (<?= ($o['io_price_add']>0?'+':'') . number_format($o['io_price_add']) ?>원)
              <?php endif; ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- 수량 + 합계 -->
      <div class="flex items-center justify-between border-t border-b py-4">
        <span class="text-sm font-bold text-gray-700">수량</span>
        <div class="flex items-center border border-gray-300 rounded-lg">
          <button type="button" @click="qty = Math.max(1, qty - 1)"
                  class="w-9 h-9 flex items-center justify-center hover:bg-gray-50 text-lg">−</button>
          <input x-model.number="qty" type="number" min="1"
                 class="w-12 text-center border-0 focus:ring-0 text-sm">
          <button type="button" @click="qty++"
                  class="w-9 h-9 flex items-center justify-center hover:bg-gray-50 text-lg">+</button>
        </div>
      </div>

      <div class="flex items-baseline justify-between">
        <span class="text-sm text-gray-500">총 결제금액</span>
        <span class="text-2xl md:text-3xl font-extrabold text-primary">
          <span x-text="formatPrice(unitPrice * qty)"></span>원
        </span>
      </div>

      <!-- 액션 버튼 -->
      <form method="post" action="/cart_action.php" class="grid grid-cols-2 gap-3">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="it_id" value="<?= (int)$p['it_id'] ?>">
        <input type="hidden" name="qty" :value="qty">
        <input type="hidden" name="option_text" :value="optionText()">
        <input type="hidden" name="option_add" :value="optionAdd()">
        <button type="submit" name="goto" value="cart"
                class="h-12 rounded-lg border-2 border-primary text-primary font-bold
                       hover:bg-primary hover:text-white transition">
          🛒 장바구니
        </button>
        <button type="submit" name="goto" value="checkout"
                class="h-12 rounded-lg bg-primary text-white font-bold
                       hover:bg-primary-light transition">
          바로 구매하기
        </button>
      </form>

      <!-- 정보 박스 -->
      <div class="grid grid-cols-3 gap-2 text-xs text-center pt-2">
        <div class="p-3 bg-gray-50 rounded-lg">
          <div class="text-lg mb-1">🚚</div>
          <div class="text-gray-700">무료배송</div>
        </div>
        <div class="p-3 bg-gray-50 rounded-lg">
          <div class="text-lg mb-1">⏱️</div>
          <div class="text-gray-700">당일발송</div>
        </div>
        <div class="p-3 bg-gray-50 rounded-lg">
          <div class="text-lg mb-1">🛡️</div>
          <div class="text-gray-700">정품보장</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 상품 상세 설명 탭 -->
<section x-data="{ tab: 'desc' }"
         class="max-w-7xl mx-auto px-4 mt-12 md:mt-20">
  <div class="border-b flex">
    <button @click="tab = 'desc'"
            :class="tab === 'desc' ? 'text-primary border-primary' : 'text-gray-500 border-transparent'"
            class="px-6 py-3 font-bold border-b-2 transition">상품설명</button>
    <button @click="tab = 'shipping'"
            :class="tab === 'shipping' ? 'text-primary border-primary' : 'text-gray-500 border-transparent'"
            class="px-6 py-3 font-bold border-b-2 transition">배송/교환</button>
    <button @click="tab = 'qa'"
            :class="tab === 'qa' ? 'text-primary border-primary' : 'text-gray-500 border-transparent'"
            class="px-6 py-3 font-bold border-b-2 transition">Q&A</button>
  </div>

  <div x-show="tab === 'desc'" class="py-8 prose-product">
    <?= $p['it_desc'] ?: '<p class="text-gray-500">등록된 상세 설명이 없습니다.</p>' ?>
  </div>

  <div x-show="tab === 'shipping'" x-cloak class="py-8 text-sm text-gray-700 space-y-3">
    <h3 class="font-bold text-primary text-base">배송 안내</h3>
    <ul class="list-disc pl-5 space-y-1">
      <li>전국 무료배송 (제주·도서산간 추가 배송비 발생)</li>
      <li>평일 오후 3시 이전 결제 시 당일 출고</li>
      <li>배송업체: CJ대한통운</li>
    </ul>
    <h3 class="font-bold text-primary text-base mt-6">교환·반품</h3>
    <ul class="list-disc pl-5 space-y-1">
      <li>수령 후 7일 이내 미사용 제품에 한해 교환·반품 가능</li>
      <li>단순 변심 시 왕복 배송비 고객 부담</li>
      <li>제품 불량 시 100% 교환 보증</li>
    </ul>
  </div>

  <div x-show="tab === 'qa'" x-cloak class="py-8 text-sm text-gray-500">
    <p>현재 등록된 Q&A가 없습니다. (데모 페이지)</p>
  </div>
</section>

<script>
function productOptions(data) {
  return {
    qty: 1,
    selected: {},
    basePrice: data.base_price,
    options: data.options,
    get unitPrice() {
      return this.basePrice + this.optionAdd();
    },
    optionText() {
      return Object.entries(this.selected)
        .filter(([_, v]) => v)
        .map(([k, v]) => `${k}: ${v}`)
        .join(', ');
    },
    optionAdd() {
      let add = 0;
      for (const [type, value] of Object.entries(this.selected)) {
        if (!value || !this.options[type]) continue;
        const opt = this.options[type].find(o => o.io_value === value);
        if (opt) add += parseInt(opt.io_price_add || 0);
      }
      return add;
    },
    updatePrice() { /* triggered via x-model */ },
    formatPrice(n) {
      return n.toLocaleString();
    },
  };
}
</script>

<?php require __DIR__ . '/../../includes/footer.php'; ?>

<?php
require_once __DIR__ . '/../../includes/functions.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$per  = 9;

$total = (int)db_one('SELECT COUNT(*) c FROM case_studies WHERE published = 1')['c'];
$offset = ($page - 1) * $per;

// 첫 번째 이미지를 함께 가져옴
$cases = db_all(
    "SELECT cs.*,
            (SELECT image_url FROM case_images
              WHERE case_id = cs.id ORDER BY sort_order, id LIMIT 1) AS first_image
       FROM case_studies cs
      WHERE cs.published = 1
   ORDER BY cs.delivered_at DESC, cs.id DESC
      LIMIT $per OFFSET $offset"
);
$pg = pagination_links($page, $total, $per);

$page_title = '납품사례';
$page_desc  = '썬볼트가 함께한 다양한 분야의 배터리 납품 실적을 확인하세요';
require __DIR__ . '/../../includes/header.php';
?>

<section class="bg-primary text-white">
  <div class="max-w-7xl mx-auto px-4 py-12 md:py-16">
    <h1 class="text-3xl md:text-4xl font-extrabold">납품사례</h1>
    <p class="text-sm md:text-base text-white/70 mt-2"><?= h($page_desc) ?></p>
    <div class="mt-4 flex gap-6 text-sm">
      <span><span class="text-accent font-extrabold text-2xl"><?= number_format($total) ?></span> 건의 납품 실적</span>
    </div>
  </div>
</section>

<section class="max-w-7xl mx-auto px-4 py-8 md:py-12">

  <?php if (empty($cases)): ?>
  <div class="py-20 text-center text-gray-500">
    <div class="text-6xl mb-4">📦</div>
    아직 등록된 사례가 없습니다.
  </div>
  <?php else: ?>

  <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($cases as $c):
      $img = $c['first_image'] ?: 'https://placehold.co/800x600/0A2540/FFC107?text=Case';
    ?>
    <a href="<?= h(url('/cases/view.php', ['id' => $c['id']])) ?>"
       class="product-card group block bg-white rounded-xl overflow-hidden border">
      <div class="aspect-[4/3] bg-gray-100 overflow-hidden">
        <img src="<?= h($img) ?>" alt="<?= h($c['title']) ?>" loading="lazy"
             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
      </div>
      <div class="p-5">
        <div class="text-[11px] text-accent-dark font-bold mb-2">
          <?= h($c['delivered_at'] ? date('Y.m', strtotime($c['delivered_at'])) : '-') ?>
        </div>
        <h3 class="font-extrabold text-base text-primary line-clamp-2 leading-snug min-h-[2.8em] group-hover:text-accent-dark transition">
          <?= h($c['title']) ?>
        </h3>
        <div class="mt-3 space-y-1 text-xs text-gray-600">
          <div>🏢 <span class="font-semibold text-gray-800"><?= h($c['client_name']) ?></span></div>
          <?php if ($c['scale']): ?>
          <div class="line-clamp-1">📊 <?= h($c['scale']) ?></div>
          <?php endif; ?>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>

  <?php if ($pg['pages'] > 1): ?>
  <nav class="mt-10 flex items-center justify-center gap-1 text-sm">
    <?php for ($i = $pg['start']; $i <= $pg['end']; $i++): ?>
    <a href="<?= h(current_url_with(['page' => $i])) ?>"
       class="w-9 h-9 flex items-center justify-center rounded-lg border <?= $i == $page ? 'bg-primary text-white border-primary font-bold' : 'hover:bg-gray-50' ?>">
      <?= $i ?>
    </a>
    <?php endfor; ?>
  </nav>
  <?php endif; ?>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/../../includes/footer.php'; ?>

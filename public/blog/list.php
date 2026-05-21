<?php
require_once __DIR__ . '/../../includes/functions.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$per  = 9;

$total = (int)db_one('SELECT COUNT(*) c FROM posts WHERE published = 1')['c'];
$offset = ($page - 1) * $per;
$posts = db_all(
    "SELECT id, title, summary, thumbnail, created_at
     FROM posts WHERE published = 1
     ORDER BY created_at DESC LIMIT $per OFFSET $offset"
);
$pg = pagination_links($page, $total, $per);

$page_title = '블로그';
$page_desc  = '배터리 관련 최신 정보와 안전 관리 팁';
require __DIR__ . '/../../includes/header.php';
?>

<section class="bg-gray-50 border-b">
  <div class="max-w-7xl mx-auto px-4 py-8 md:py-12">
    <h1 class="text-3xl md:text-4xl font-extrabold text-primary">블로그</h1>
    <p class="text-sm md:text-base text-gray-500 mt-2"><?= h($page_desc) ?></p>
  </div>
</section>

<section class="max-w-7xl mx-auto px-4 py-8 md:py-12">

  <?php if (empty($posts)): ?>
  <div class="py-20 text-center text-gray-500">
    <div class="text-6xl mb-4">📝</div>
    아직 등록된 글이 없습니다.
  </div>
  <?php else: ?>

  <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($posts as $p):
      $img = $p['thumbnail'] ?: 'https://placehold.co/800x500/0A2540/FFC107?text=Blog';
    ?>
    <a href="<?= h(url('/blog/view.php', ['id' => $p['id']])) ?>"
       class="product-card group block bg-white rounded-xl overflow-hidden border">
      <div class="aspect-[16/10] bg-gray-100 overflow-hidden">
        <img src="<?= h($img) ?>" alt="<?= h($p['title']) ?>" loading="lazy"
             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
      </div>
      <div class="p-5">
        <div class="text-[11px] text-gray-400 mb-2"><?= h(date('Y.m.d', strtotime($p['created_at']))) ?></div>
        <h3 class="font-extrabold text-base md:text-lg text-primary line-clamp-2 leading-snug min-h-[3em] group-hover:text-accent-dark transition">
          <?= h($p['title']) ?>
        </h3>
        <?php if ($p['summary']): ?>
        <p class="text-sm text-gray-600 mt-2 line-clamp-2"><?= h($p['summary']) ?></p>
        <?php endif; ?>
        <div class="mt-4 inline-flex items-center text-xs text-primary font-bold group-hover:gap-2 gap-1 transition-all">
          자세히 보기 <span>→</span>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>

  <!-- 페이지네이션 -->
  <?php if ($pg['pages'] > 1): ?>
  <nav class="mt-10 flex items-center justify-center gap-1 text-sm">
    <?php if ($page > 1): ?>
    <a href="<?= h(current_url_with(['page' => $page - 1])) ?>"
       class="w-9 h-9 flex items-center justify-center rounded-lg border hover:bg-gray-50">‹</a>
    <?php endif; ?>
    <?php for ($i = $pg['start']; $i <= $pg['end']; $i++): ?>
    <a href="<?= h(current_url_with(['page' => $i])) ?>"
       class="w-9 h-9 flex items-center justify-center rounded-lg border <?= $i == $page ? 'bg-primary text-white border-primary font-bold' : 'hover:bg-gray-50' ?>">
      <?= $i ?>
    </a>
    <?php endfor; ?>
    <?php if ($page < $pg['pages']): ?>
    <a href="<?= h(current_url_with(['page' => $page + 1])) ?>"
       class="w-9 h-9 flex items-center justify-center rounded-lg border hover:bg-gray-50">›</a>
    <?php endif; ?>
  </nav>
  <?php endif; ?>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/../../includes/footer.php'; ?>

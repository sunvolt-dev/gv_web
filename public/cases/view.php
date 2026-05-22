<?php
require_once __DIR__ . '/../../includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
$case = $id ? db_one('SELECT * FROM case_studies WHERE id = ? AND published = 1', [$id]) : null;

if (!$case) {
    http_response_code(404);
    $page_title = '사례를 찾을 수 없습니다';
    require __DIR__ . '/../../includes/header.php';
    echo '<div class="max-w-3xl mx-auto px-4 py-20 text-center"><div class="text-6xl mb-4">📦</div>';
    echo '<h1 class="text-2xl font-bold text-primary mb-4">사례를 찾을 수 없습니다</h1>';
    echo '<a href="/cases/list.php" class="inline-block px-6 py-3 rounded-lg bg-primary text-white font-bold">납품사례 목록</a></div>';
    require __DIR__ . '/../../includes/footer.php';
    exit;
}

$images = db_all('SELECT * FROM case_images WHERE case_id = ? ORDER BY sort_order, id', [$id]);

$page_title = $case['title'];
$page_desc  = (string)$case['summary'] ?: mb_substr(strip_tags((string)$case['content']), 0, 150);
$og_type    = 'article';
$og_image   = $images[0]['image_url'] ?? 'https://placehold.co/1200x630/0A2540/FFC107?text=Sunvolt+Case';

/* ── 구조화 데이터: Article + BreadcrumbList ── */
$json_ld = [
    [
        '@context'      => 'https://schema.org',
        '@type'         => 'Article',
        'headline'      => $case['title'],
        'description'   => $page_desc,
        'image'         => abs_url($og_image),
        'datePublished' => date('c', strtotime($case['created_at'])),
        'dateModified'  => date('c', strtotime($case['updated_at'] ?: $case['created_at'])),
        'author'        => organization_ld(),
        'publisher'     => organization_ld(),
        'mainEntityOfPage' => canonical_url(),
    ],
    breadcrumb_ld(['HOME' => '/', '납품사례' => '/cases/list.php', $case['title'] => null]),
];
require __DIR__ . '/../../includes/header.php';
?>

<article class="max-w-5xl mx-auto px-4 py-8 md:py-12">

  <nav class="text-xs text-gray-500 mb-6 flex items-center gap-1.5">
    <a href="/" class="hover:text-primary">HOME</a> ›
    <a href="/cases/list.php" class="hover:text-primary">납품사례</a> ›
    <span class="text-primary font-semibold line-clamp-1"><?= h($case['title']) ?></span>
  </nav>

  <header class="mb-8">
    <div class="text-xs text-accent-dark font-bold mb-2">
      📅 <?= h($case['delivered_at'] ? date('Y년 m월', strtotime($case['delivered_at'])) : '-') ?>
    </div>
    <h1 class="text-2xl md:text-4xl font-extrabold text-primary leading-tight mb-4">
      <?= h($case['title']) ?>
    </h1>
    <?php if ($case['summary']): ?>
    <p class="text-base md:text-lg text-gray-600 leading-relaxed"><?= h($case['summary']) ?></p>
    <?php endif; ?>
  </header>

  <!-- 핵심 정보 박스 -->
  <div class="grid md:grid-cols-3 gap-3 mb-10 bg-gray-50 p-5 rounded-2xl">
    <div>
      <div class="text-xs text-gray-500 mb-1">🏢 고객사</div>
      <div class="font-extrabold text-primary"><?= h($case['client_name']) ?></div>
    </div>
    <div>
      <div class="text-xs text-gray-500 mb-1">📊 납품 규모</div>
      <div class="font-extrabold text-primary text-sm"><?= h($case['scale'] ?: '-') ?></div>
    </div>
    <div>
      <div class="text-xs text-gray-500 mb-1">📅 납품 일자</div>
      <div class="font-extrabold text-primary"><?= h($case['delivered_at'] ? date('Y년 m월 d일', strtotime($case['delivered_at'])) : '-') ?></div>
    </div>
  </div>

  <!-- 사진 갤러리 (Swiper) -->
  <?php if (!empty($images)): ?>
  <div class="mb-10">
    <div class="swiper case-swiper bg-gray-50 rounded-2xl overflow-hidden border">
      <div class="swiper-wrapper">
        <?php foreach ($images as $img): ?>
        <div class="swiper-slide aspect-video relative">
          <img src="<?= h($img['image_url']) ?>" alt="<?= h($img['caption'] ?? '') ?>"
               class="w-full h-full object-cover">
          <?php if ($img['caption']): ?>
          <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/70 to-transparent text-white text-sm font-semibold">
            <?= h($img['caption']) ?>
          </div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php if (count($images) > 1): ?>
      <div class="swiper-button-prev !text-white"></div>
      <div class="swiper-button-next !text-white"></div>
      <div class="swiper-pagination"></div>
      <?php endif; ?>
    </div>

    <?php if (count($images) > 1): ?>
    <div class="grid grid-cols-4 md:grid-cols-6 gap-2 mt-3">
      <?php foreach ($images as $i => $img): ?>
      <div class="aspect-square rounded-md overflow-hidden bg-gray-100 cursor-pointer hover:opacity-70"
           onclick="caseSwiper.slideTo(<?= $i ?>)">
        <img src="<?= h($img['image_url']) ?>" class="w-full h-full object-cover">
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <!-- 본문 -->
  <div class="prose-blog">
    <?= $case['content'] ?>
  </div>

  <div class="text-center mt-12">
    <a href="/cases/list.php" class="inline-block px-6 py-3 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm font-semibold">
      ← 납품사례 목록으로
    </a>
  </div>
</article>

<style>
.prose-blog { line-height: 1.85; color: #1F2937; font-size: 16px; }
.prose-blog h2 { font-size: 1.5rem; font-weight: 800; color: #0A2540; margin: 2rem 0 1rem; }
.prose-blog h3 { font-size: 1.2rem; font-weight: 700; color: #0A2540; margin: 1.5rem 0 0.75rem; }
.prose-blog p { margin: 0.75rem 0; }
.prose-blog ul { list-style: disc; padding-left: 1.5rem; margin: 0.75rem 0; }
.prose-blog ol { list-style: decimal; padding-left: 1.5rem; margin: 0.75rem 0; }
.prose-blog li { margin: 0.3rem 0; }
.prose-blog blockquote { border-left: 4px solid #FFC107; padding: 0.5rem 1rem; margin: 1.25rem 0; color: #4B5563; background: #FAFAFA; font-style: italic; }
.prose-blog strong { color: #0A2540; font-weight: 700; }
</style>

<script>
let caseSwiper;
document.addEventListener('DOMContentLoaded', () => {
  if (document.querySelector('.case-swiper')) {
    caseSwiper = new Swiper('.case-swiper', {
      loop: true,
      navigation: { nextEl: '.case-swiper .swiper-button-next', prevEl: '.case-swiper .swiper-button-prev' },
      pagination: { el: '.case-swiper .swiper-pagination', clickable: true },
    });
  }
});
</script>

<?php require __DIR__ . '/../../includes/footer.php'; ?>

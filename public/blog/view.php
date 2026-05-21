<?php
require_once __DIR__ . '/../../includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
$post = $id ? db_one('SELECT * FROM posts WHERE id = ? AND published = 1', [$id]) : null;

if (!$post) {
    http_response_code(404);
    $page_title = '글을 찾을 수 없습니다';
    require __DIR__ . '/../../includes/header.php';
    echo '<div class="max-w-3xl mx-auto px-4 py-20 text-center"><div class="text-6xl mb-4">📝</div>';
    echo '<h1 class="text-2xl font-bold text-primary mb-4">글을 찾을 수 없습니다</h1>';
    echo '<a href="/blog/list.php" class="inline-block px-6 py-3 rounded-lg bg-primary text-white font-bold">블로그 목록</a></div>';
    require __DIR__ . '/../../includes/footer.php';
    exit;
}

// 조회수 증가
db_exec('UPDATE posts SET views = views + 1 WHERE id = ?', [$id]);

// 이전/다음 글
$prev = db_one('SELECT id, title FROM posts WHERE published = 1 AND id < ? ORDER BY id DESC LIMIT 1', [$id]);
$next = db_one('SELECT id, title FROM posts WHERE published = 1 AND id > ? ORDER BY id ASC LIMIT 1', [$id]);

$page_title = $post['title'];
$page_desc  = (string)$post['summary'];
require __DIR__ . '/../../includes/header.php';
?>

<article class="max-w-3xl mx-auto px-4 py-8 md:py-14">

  <nav class="text-xs text-gray-500 mb-6 flex items-center gap-1.5">
    <a href="/" class="hover:text-primary">HOME</a> ›
    <a href="/blog/list.php" class="hover:text-primary">블로그</a> ›
    <span class="text-primary font-semibold line-clamp-1"><?= h($post['title']) ?></span>
  </nav>

  <header class="mb-8 pb-6 border-b">
    <h1 class="text-3xl md:text-4xl font-extrabold text-primary leading-tight mb-4">
      <?= h($post['title']) ?>
    </h1>
    <?php if ($post['summary']): ?>
    <p class="text-base md:text-lg text-gray-600 leading-relaxed">
      <?= h($post['summary']) ?>
    </p>
    <?php endif; ?>
    <div class="text-sm text-gray-500 mt-4 flex items-center gap-3">
      <span>📅 <?= h(date('Y년 m월 d일', strtotime($post['created_at']))) ?></span>
      <span>·</span>
      <span>썬볼트 배터리몰</span>
    </div>
  </header>

  <?php if ($post['thumbnail']): ?>
  <figure class="mb-10 -mx-4 md:mx-0">
    <img src="<?= h($post['thumbnail']) ?>" alt=""
         class="w-full md:rounded-2xl aspect-[16/9] object-cover">
  </figure>
  <?php endif; ?>

  <!-- 본문 (Quill HTML) -->
  <div class="ql-editor-output prose-blog">
    <?= $post['content'] ?>
  </div>

  <!-- 이전/다음 -->
  <nav class="mt-16 pt-8 border-t grid grid-cols-2 gap-3">
    <?php if ($prev): ?>
    <a href="<?= h(url('/blog/view.php', ['id' => $prev['id']])) ?>"
       class="block p-4 border rounded-xl hover:border-primary group">
      <div class="text-xs text-gray-400 mb-1">← 이전 글</div>
      <div class="text-sm font-bold text-gray-800 line-clamp-1 group-hover:text-primary"><?= h($prev['title']) ?></div>
    </a>
    <?php else: ?><div></div><?php endif; ?>
    <?php if ($next): ?>
    <a href="<?= h(url('/blog/view.php', ['id' => $next['id']])) ?>"
       class="block p-4 border rounded-xl hover:border-primary group text-right">
      <div class="text-xs text-gray-400 mb-1">다음 글 →</div>
      <div class="text-sm font-bold text-gray-800 line-clamp-1 group-hover:text-primary"><?= h($next['title']) ?></div>
    </a>
    <?php else: ?><div></div><?php endif; ?>
  </nav>

  <div class="text-center mt-10">
    <a href="/blog/list.php" class="inline-block px-6 py-3 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm font-semibold">
      ← 블로그 목록으로
    </a>
  </div>
</article>

<style>
.prose-blog { line-height: 1.85; color: #1F2937; font-size: 16px; }
.prose-blog h2 { font-size: 1.6rem; font-weight: 800; color: #0A2540; margin: 2.5rem 0 1rem; line-height: 1.3; }
.prose-blog h3 { font-size: 1.25rem; font-weight: 700; color: #0A2540; margin: 2rem 0 0.75rem; }
.prose-blog h4 { font-size: 1.1rem; font-weight: 700; color: #0A2540; margin: 1.5rem 0 0.5rem; }
.prose-blog p { margin: 1rem 0; }
.prose-blog ul { list-style: disc; padding-left: 1.5rem; margin: 1rem 0; }
.prose-blog ol { list-style: decimal; padding-left: 1.5rem; margin: 1rem 0; }
.prose-blog li { margin: 0.4rem 0; }
.prose-blog blockquote { border-left: 4px solid #FFC107; padding: 0.5rem 1rem; margin: 1.5rem 0; color: #4B5563; background: #FAFAFA; }
.prose-blog img { max-width: 100%; border-radius: 0.75rem; margin: 1.5rem 0; }
.prose-blog a { color: #0A2540; text-decoration: underline; }
.prose-blog strong { color: #0A2540; font-weight: 700; }
</style>

<?php require __DIR__ . '/../../includes/footer.php'; ?>

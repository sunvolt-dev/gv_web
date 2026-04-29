<?php
/**
 * 장바구니 동작 처리: 담기 / 수량 변경 / 삭제
 * POST → 적절한 페이지로 redirect
 */

require_once __DIR__ . '/../includes/functions.php';
cart_init();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

case 'add': {
    $it_id       = (int)($_POST['it_id'] ?? 0);
    $qty         = max(1, (int)($_POST['qty'] ?? 1));
    $option_text = trim((string)($_POST['option_text'] ?? ''));
    $option_add  = (int)($_POST['option_add'] ?? 0);
    $goto        = $_POST['goto'] ?? 'cart';

    $p = $it_id ? get_product($it_id) : null;
    if (!$p) { header('Location: /'); exit; }

    // 같은 상품·같은 옵션이면 합치기
    $key = "{$it_id}_" . md5($option_text);
    if (isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key]['qty'] += $qty;
    } else {
        $_SESSION['cart'][$key] = [
            'it_id'       => $it_id,
            'qty'         => $qty,
            'option_text' => $option_text,
            'option_add'  => $option_add,
        ];
    }

    if ($goto === 'checkout') {
        header('Location: /checkout.php');
    } else {
        header('Location: /cart.php?added=1');
    }
    exit;
}

case 'update': {
    $key = (string)($_POST['key'] ?? '');
    $qty = max(1, (int)($_POST['qty'] ?? 1));
    if (isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key]['qty'] = $qty;
    }
    header('Location: /cart.php');
    exit;
}

case 'delete': {
    $key = (string)($_POST['key'] ?? $_GET['key'] ?? '');
    if (isset($_SESSION['cart'][$key])) {
        unset($_SESSION['cart'][$key]);
    }
    header('Location: /cart.php');
    exit;
}

case 'clear': {
    $_SESSION['cart'] = [];
    header('Location: /cart.php');
    exit;
}

default:
    header('Location: /');
    exit;
}

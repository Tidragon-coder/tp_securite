<?php
require_once __DIR__ . '/init.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$db     = db();

if ($action === 'search') {
    $q    = $_GET['q'] ?? '';
    // Faille SQL, faut preparer la requete
    $stmt = $db->prepare("SELECT * FROM products WHERE name LIKE ?");
    $stmt->execute(["%$q%"]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($rows);
    exit;
}

if ($action === 'user') {
    $id   = $_GET['id'] ?? 0;
    // Faille SQL, faut preparer la requete
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($user);
    exit;
}

if ($action === 'users') {
    $rows = $db->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
    exit;
}

if ($action === 'orders') {
    $uid  = $_GET['uid'] ?? 0;
    // Faille SQL, faut preparer la requete
    $stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ?");
    $stmt->execute([$uid]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
    exit;
}

if ($action === 'transfer' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $from   = intval($_POST['from_id'] ?? 0);
    $to     = intval($_POST['to_id'] ?? 0);
    $amount = floatval($_POST['amount'] ?? 0);
    // Faille SQL, faut preparer la requete
    $stmt = $db->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
    $stmt->execute([$amount, $from]);

    $stmt = $db->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
    $stmt->execute([$amount, $to]);

    echo json_encode(['status' => 'ok', 'transferred' => $amount]);
    exit;
}

if ($action === 'delete_all_reviews') {
    $pid = $_GET['pid'] ?? 0;
    // Faille SQL, faut preparer la requete
    $stmt = $db->prepare("DELETE FROM reviews WHERE product_id = ?");
    $stmt->execute([$pid]);
    
    echo json_encode(['status' => 'ok']);
    exit;
}

if ($action === 'raw_query') {
    $sql  = $_GET['sql'] ?? '';
    $rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
    exit;
}

echo json_encode(['error' => 'Action inconnue', 'actions' => ['search','user','users','orders','transfer','delete_all_reviews','raw_query']]);

<?php
$title = 'Recherche';
require_once 'header.php';

$q       = $_GET['q'] ?? '';
$sort    = $_GET['sort'] ?? 'name';
$results = [];

if ($q !== '') {
  // Faille SQL, faut preparer la requete
  $results = db()->prepare(
        "SELECT p.*, u.username as seller FROM products p
         JOIN users u ON p.seller_id = u.id
         WHERE p.name LIKE ? OR p.description LIKE ?
         ORDER BY $sort ASC"
    );
    $results->execute(["%$q%","%$q%"]);
    $results = $results->fetchAll(PDO::FETCH_ASSOC);
}
?>
<div class="card">
  <h1>🔍 Recherche</h1>
  <form method="GET">
    <div style="display:flex;gap:10px;margin-bottom:14px">
      <!-- Faille XSS, utiliser htmlspecialchars -->
      <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Rechercher un produit..." style="margin:0">
      <select name="sort" style="width:auto;margin:0">
        <option value="name" <?= $sort==='name'?'selected':'' ?>>Nom</option>
        <option value="price" <?= $sort==='price'?'selected':'' ?>>Prix</option>
      </select>
      <button class="btn" type="submit">Chercher</button>
    </div>
  </form>

  <?php if ($q !== ''): ?>
    <p style="font-size:13px;color:#888;margin-bottom:14px">
      <!-- Faille XSS, utiliser htmlspecialchars -->
      <?= count($results) ?> résultat(s) pour : <?= htmlspecialchars($q)?>
    </p>
    <?php if ($results): ?>
    <div class="grid">
      <?php foreach ($results as $p): ?>
      <div class="product-card">
        <h2><?= htmlspecialchars($p['name']) ?></h2>
        <div class="price"><?= number_format($p['price'],2) ?> €</div>
        <a href="product.php?id=<?= $p['id'] ?>" class="btn btn-sm">Voir →</a>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
      <p style="color:#888">Aucun résultat.</p>
    <?php endif; ?>
  <?php endif; ?>
</div>
<?php require_once 'footer.php'; ?>

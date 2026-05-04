<?php
require_once "config.php";
if ($pdo === null) {
    header("Location: static.html");
    exit;
}

$languages = $pdo->query("SELECT * FROM languages ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$selectedLang = $_GET['lang'] ?? 'all';
$selectedCat = $_GET['cat'] ?? 'all';
$search = $_GET['search'] ?? '';

$sql = "SELECT s.*, l.name AS language_name, l.voice_code, c.name AS category_name, c.telugu_name, c.icon
        FROM stories s
        JOIN languages l ON s.language_code = l.code
        JOIN categories c ON s.category_slug = c.slug
        WHERE 1=1";
$params = [];

if ($selectedLang !== 'all') {
    $sql .= " AND s.language_code = ?";
    $params[] = $selectedLang;
}
if ($selectedCat !== 'all') {
    $sql .= " AND s.category_slug = ?";
    $params[] = $selectedCat;
}
if ($search !== '') {
    $sql .= " AND s.title LIKE ?";
    $params[] = "%$search%";
}

$sql .= " ORDER BY s.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Kids Story World</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="hero">
  <nav>
    <h1>📚 Kids Story World</h1>
    <div>
      <a href="admin.php" class="admin-link">Admin</a>
      <button onclick="toggleTheme()">🌙</button>
    </div>
  </nav>
  <div class="hero-content">
    <div class="floating">🦄</div>
    <h2>Magical Stories for Kids</h2>
    <p>Choose language and category. Telugu selected means Telugu stories and Telugu voice reading where browser supports Telugu voice.</p>
    <a href="#stories" class="start-btn">Start Reading</a>
  </div>
</header>

<section class="filters">
  <form method="GET">
    <div>
      <label>🌍 Choose Language</label>
      <select name="lang">
        <option value="all">All Languages</option>
        <?php foreach($languages as $lang): ?>
          <option value="<?= htmlspecialchars($lang['code']) ?>" <?= $selectedLang === $lang['code'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($lang['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label>🎭 Choose Category</label>
      <select name="cat">
        <option value="all">All Categories</option>
        <?php foreach($categories as $cat): ?>
          <option value="<?= htmlspecialchars($cat['slug']) ?>" <?= $selectedCat === $cat['slug'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['icon'] . " " . $cat['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label>🔎 Search</label>
      <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search story title">
    </div>

    <button class="filter-btn" type="submit">Show Stories</button>
  </form>
</section>

<main id="stories">
  <h2 class="section-title">✨ Story Collection</h2>
  <p class="count"><?= count($stories) ?> stories found</p>

  <div class="story-grid">
    <?php foreach($stories as $story): ?>
      <div class="card">
        <img src="<?= htmlspecialchars($story['image_url']) ?>" alt="Story">
        <div class="card-body">
          <h3><?= htmlspecialchars($story['title']) ?></h3>
          <span class="badge"><?= htmlspecialchars($story['language_name']) ?></span>
          <span class="badge"><?= htmlspecialchars($story['icon'] . " " . $story['category_name']) ?></span>
          <span class="badge"><?= htmlspecialchars($story['reading_time']) ?></span>
          <button class="read-btn"
            onclick='openStory(<?= json_encode($story, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
            📖 Read / ▶ Play
          </button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<div class="modal" id="storyModal">
  <div class="modal-box">
    <button class="close-btn" onclick="closeStory()">✖</button>
    <img id="modalImage" src="" alt="">
    <h2 id="modalTitle"></h2>
    <p class="meta" id="modalMeta"></p>
    <p id="modalText"></p>
    <div class="player">
      <button onclick="speakStory()">▶ Play</button>
      <button onclick="pauseStory()">⏸ Pause</button>
      <button onclick="resumeStory()">⏯ Resume</button>
      <button onclick="stopStory()">⏹ Stop</button>
    </div>
    <p class="voice-note" id="voiceNote"></p>
  </div>
</div>

<footer>
  <p>🌈 Kids Story World | PHP + MySQL Website</p>
</footer>

<script src="script.js"></script>
</body>
</html>

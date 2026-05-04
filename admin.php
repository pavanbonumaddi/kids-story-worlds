<?php
require_once "config.php";

$message = "";
$languages = $pdo->query("SELECT * FROM languages ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"] ?? "";
    $language_code = $_POST["language_code"] ?? "";
    $category_slug = $_POST["category_slug"] ?? "";
    $story_text = $_POST["story_text"] ?? "";
    $age_group = $_POST["age_group"] ?? "4-10";
    $reading_time = $_POST["reading_time"] ?? "3 min";
    $image_url = $_POST["image_url"] ?? "https://placehold.co/700x430?text=Kids+Story";

    if ($title && $language_code && $category_slug && $story_text) {
        $stmt = $pdo->prepare("INSERT INTO stories (title, language_code, category_slug, story_text, age_group, reading_time, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $language_code, $category_slug, $story_text, $age_group, $reading_time, $image_url]);
        $message = "Story added successfully!";
    } else {
        $message = "Please fill all required fields.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Admin - Add Story</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="admin-page">
  <div class="admin-box">
    <h1>➕ Add New Story</h1>
    <p><a href="index.php">← Back to Website</a></p>
    <?php if($message): ?><div class="message"><?= htmlspecialchars($message) ?></div><?php endif; ?>

    <form method="POST" class="admin-form">
      <label>Story Title *</label>
      <input type="text" name="title" required>

      <label>Language *</label>
      <select name="language_code" required>
        <?php foreach($languages as $lang): ?>
          <option value="<?= htmlspecialchars($lang['code']) ?>"><?= htmlspecialchars($lang['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Category *</label>
      <select name="category_slug" required>
        <?php foreach($categories as $cat): ?>
          <option value="<?= htmlspecialchars($cat['slug']) ?>"><?= htmlspecialchars($cat['icon'] . " " . $cat['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Story Text *</label>
      <textarea name="story_text" rows="9" required></textarea>

      <label>Age Group</label>
      <input type="text" name="age_group" value="4-10">

      <label>Reading Time</label>
      <input type="text" name="reading_time" value="3 min">

      <label>Image URL</label>
      <input type="text" name="image_url" value="https://placehold.co/700x430?text=Kids+Story">

      <button type="submit" class="filter-btn">Save Story</button>
    </form>
  </div>
</div>
</body>
</html>

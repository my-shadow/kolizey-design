<?php
date_default_timezone_set('Europe/Kyiv');
session_start();

$data_file = 'data.json';
$data = json_decode(file_get_contents($data_file), true) ?? [];
$s = &$data['settings'];

function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
function sv($k, $d = '') { global $s; return htmlspecialchars($s[$k] ?? $d, ENT_QUOTES, 'UTF-8'); }
function save_data($data, $file) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// --- Auth ---
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    if (($_POST['password'] ?? '') === ($s['admin_password'] ?? 'kolisey')) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin'); exit;
    }
    $login_error = 'Невірний пароль';
}

if (isset($_POST['action']) && $_POST['action'] === 'logout') {
    session_destroy();
    header('Location: admin'); exit;
}

// --- Save Content ---
if (isset($_POST['action']) && $_POST['action'] === 'save_content' && isset($_SESSION['admin_logged_in'])) {
    $fields = [
        'meta_title','meta_desc','og_image',
        'hero_title','hero_desc','promo_text',
        'internet_title','internet_desc','price_internet_hour',
        'price_copy_bw','price_copy_color',
        'price_lam_a4','price_lam_a3',
        'price_scan','price_id_photo',
        'price_photo_a6','price_photo_a4','price_photo_a3',
        'price_mug','price_tshirt','price_frame','price_binding',
    ];
    foreach ($fields as $f) {
        $s[$f] = trim($_POST[$f] ?? '');
    }
    save_data($data, $data_file);
    header('Location: admin?tab=content&saved=1'); exit;
}

// --- Save Settings ---
if (isset($_POST['action']) && $_POST['action'] === 'save_settings' && isset($_SESSION['admin_logged_in'])) {
    $fields = ['business_name','address','phone','phone_manager','analytics_id'];
    foreach ($fields as $f) {
        $s[$f] = trim($_POST[$f] ?? '');
    }
    $new_pass = trim($_POST['new_password'] ?? '');
    if ($new_pass !== '') {
        $s['admin_password'] = $new_pass;
    }
    save_data($data, $data_file);
    header('Location: admin?tab=settings&saved=1'); exit;
}

$logged_in  = isset($_SESSION['admin_logged_in']);
$active_tab = $_GET['tab'] ?? 'content';
$saved      = isset($_GET['saved']);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Адмінка — <?= e($s['business_name'] ?? 'Колізей') ?></title>
<meta name="robots" content="noindex,nofollow">
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={theme:{extend:{colors:{brand:{DEFAULT:'#2f0578',dark:'#1e0350'}}}}}</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
  body { font-family: system-ui, sans-serif; }
  .tab-btn { transition: color .15s, border-color .15s; }
  .tab-btn.active { color:#2f0578; border-bottom-color:#2f0578; }
  .tab-pane { display:none; }
  .tab-pane.active { display:block; }
  .char-ok   { color:#16a34a; }
  .char-warn { color:#d97706; }
  .char-bad  { color:#dc2626; }
  .field-label { display:block; font-size:.75rem; font-weight:600; color:#6b7280; margin-bottom:.4rem; text-transform:uppercase; letter-spacing:.05em; }
</style>
</head>
<body class="bg-gray-50 min-h-screen">

<?php if (!$logged_in): ?>
<!-- LOGIN -->
<div class="min-h-screen flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-sm">
    <div class="flex items-center gap-2 justify-center mb-6">
      <i class="fa-solid fa-print text-brand text-2xl"></i>
      <span class="text-xl font-black text-gray-900">Адмінка</span>
    </div>
    <?php if (isset($login_error)): ?>
      <div class="bg-red-50 text-red-600 text-sm px-4 py-3 rounded-xl mb-4 flex items-center gap-2">
        <i class="fa-solid fa-circle-exclamation"></i> <?= e($login_error) ?>
      </div>
    <?php endif; ?>
    <form method="POST">
      <input type="hidden" name="action" value="login">
      <label class="field-label">Пароль</label>
      <input type="password" name="password" autofocus required
             class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand mb-4">
      <button type="submit"
              class="w-full bg-brand text-white font-semibold py-3 rounded-xl hover:bg-brand-dark transition-colors">
        Увійти
      </button>
    </form>
  </div>
</div>

<?php else: ?>
<!-- ADMIN PANEL -->
<header class="bg-white border-b border-gray-200 sticky top-0 z-40">
  <div class="max-w-5xl mx-auto px-4 h-14 flex items-center justify-between">
    <div class="flex items-center gap-2 font-black text-gray-900">
      <i class="fa-solid fa-print text-brand"></i>
      <span><?= e($s['business_name'] ?? 'Колізей') ?></span>
      <span class="text-gray-300 font-normal ml-1">· Адмінка</span>
    </div>
    <div class="flex items-center gap-3">
      <a href="/" target="_blank" class="text-sm text-gray-500 hover:text-gray-800 flex items-center gap-1.5 transition-colors">
        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i> Сайт
      </a>
      <form method="POST" class="inline">
        <input type="hidden" name="action" value="logout">
        <button class="text-sm text-gray-500 hover:text-red-600 flex items-center gap-1.5 transition-colors">
          <i class="fa-solid fa-right-from-bracket text-xs"></i> Вийти
        </button>
      </form>
    </div>
  </div>
</header>

<?php if ($saved): ?>
  <div class="max-w-5xl mx-auto px-4 pt-4">
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 rounded-xl flex items-center gap-2">
      <i class="fa-solid fa-circle-check"></i> Зміни збережено
    </div>
  </div>
<?php endif; ?>

<main class="max-w-5xl mx-auto px-4 py-6">
  <!-- Tabs -->
  <div class="border-b border-gray-200 mb-8 flex gap-6">
    <button class="tab-btn pb-3 text-sm font-semibold text-gray-500 border-b-2 border-transparent <?= $active_tab === 'content'  ? 'active' : '' ?>" data-tab="content">
      <i class="fa-solid fa-pen-to-square mr-1.5"></i>Контент
    </button>
    <button class="tab-btn pb-3 text-sm font-semibold text-gray-500 border-b-2 border-transparent <?= $active_tab === 'settings' ? 'active' : '' ?>" data-tab="settings">
      <i class="fa-solid fa-gear mr-1.5"></i>Налаштування
    </button>
  </div>

  <!-- ==================== TAB: CONTENT ==================== -->
  <div id="tab-content" class="tab-pane <?= $active_tab === 'content' ? 'active' : '' ?>">
    <form method="POST">
      <input type="hidden" name="action" value="save_content">

      <!-- SEO / Meta -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-base font-bold text-gray-900 mb-5 flex items-center gap-2">
          <i class="fa-solid fa-magnifying-glass text-brand text-sm"></i> SEO / Meta
        </h2>
        <div class="space-y-5">
          <div>
            <label class="field-label">Meta Title <span id="title-count" class="normal-case font-normal"></span></label>
            <input type="text" name="meta_title" id="meta-title" maxlength="120"
                   value="<?= sv('meta_title') ?>"
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand"
                   placeholder="Назва сторінки (до 70 символів)">
          </div>
          <div>
            <label class="field-label">Meta Description <span id="desc-count" class="normal-case font-normal"></span></label>
            <textarea name="meta_desc" id="meta-desc" rows="2" maxlength="250"
                      class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand resize-none"
                      placeholder="Опис сторінки (до 160 символів)"><?= sv('meta_desc') ?></textarea>
          </div>
          <div>
            <label class="field-label">OG Image URL</label>
            <input type="url" name="og_image" value="<?= sv('og_image') ?>"
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand"
                   placeholder="https://example.com/og.jpg (1200×630)">
          </div>

          <!-- SERP Preview -->
          <div>
            <p class="field-label">Попередній перегляд (Google)</p>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
              <p class="text-xs text-gray-400 mb-1">design.loc</p>
              <p id="serp-title" class="text-base text-blue-600 hover:underline cursor-pointer truncate font-medium"></p>
              <p id="serp-desc"  class="text-sm text-gray-500 mt-0.5 line-clamp-2"></p>
            </div>
          </div>
        </div>
      </div>

      <!-- Hero -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-base font-bold text-gray-900 mb-5 flex items-center gap-2">
          <i class="fa-solid fa-house text-brand text-sm"></i> Головний екран
        </h2>
        <div class="space-y-4">
          <div>
            <label class="field-label">Заголовок</label>
            <input type="text" name="hero_title" value="<?= sv('hero_title') ?>"
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
          </div>
          <div>
            <label class="field-label">Опис</label>
            <textarea name="hero_desc" rows="3"
                      class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand resize-none"><?= sv('hero_desc') ?></textarea>
          </div>
          <div>
            <label class="field-label">Текст бігучого рядка</label>
            <input type="text" name="promo_text" value="<?= sv('promo_text') ?>"
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
          </div>
        </div>
      </div>

      <!-- Prices -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-base font-bold text-gray-900 mb-5 flex items-center gap-2">
          <i class="fa-solid fa-tag text-brand text-sm"></i> Ціни на послуги (грн)
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">

          <div>
            <p class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
              <i class="fa-solid fa-copy text-violet-400 text-xs"></i> Ксерокопія
            </p>
            <div class="space-y-2">
              <div class="flex items-center gap-3">
                <label class="text-sm text-gray-500 w-32 shrink-0">Чорно-біла</label>
                <div class="flex items-center gap-1.5 flex-1">
                  <input type="number" name="price_copy_bw" value="<?= sv('price_copy_bw') ?>" min="0"
                         class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                  <span class="text-sm text-gray-400 shrink-0">грн/лист</span>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <label class="text-sm text-gray-500 w-32 shrink-0">Кольорова</label>
                <div class="flex items-center gap-1.5 flex-1">
                  <input type="number" name="price_copy_color" value="<?= sv('price_copy_color') ?>" min="0"
                         class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                  <span class="text-sm text-gray-400 shrink-0">грн/лист</span>
                </div>
              </div>
            </div>
          </div>

          <div>
            <p class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
              <i class="fa-solid fa-layer-group text-fuchsia-400 text-xs"></i> Ламінування
            </p>
            <div class="space-y-2">
              <div class="flex items-center gap-3">
                <label class="text-sm text-gray-500 w-32 shrink-0">Формат А4</label>
                <div class="flex items-center gap-1.5 flex-1">
                  <input type="number" name="price_lam_a4" value="<?= sv('price_lam_a4') ?>" min="0"
                         class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                  <span class="text-sm text-gray-400 shrink-0">грн</span>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <label class="text-sm text-gray-500 w-32 shrink-0">Формат А3</label>
                <div class="flex items-center gap-1.5 flex-1">
                  <input type="number" name="price_lam_a3" value="<?= sv('price_lam_a3') ?>" min="0"
                         class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                  <span class="text-sm text-gray-400 shrink-0">грн</span>
                </div>
              </div>
            </div>
          </div>

          <div>
            <p class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
              <i class="fa-solid fa-file-import text-sky-400 text-xs"></i> Сканування
            </p>
            <div class="flex items-center gap-3">
              <label class="text-sm text-gray-500 w-32 shrink-0">За аркуш</label>
              <div class="flex items-center gap-1.5 flex-1">
                <input type="number" name="price_scan" value="<?= sv('price_scan') ?>" min="0"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                <span class="text-sm text-gray-400 shrink-0">грн</span>
              </div>
            </div>
          </div>

          <div>
            <p class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
              <i class="fa-solid fa-id-card text-amber-400 text-xs"></i> Фото на документи
            </p>
            <div class="flex items-center gap-3">
              <label class="text-sm text-gray-500 w-32 shrink-0">6 фото · 5 хв</label>
              <div class="flex items-center gap-1.5 flex-1">
                <input type="number" name="price_id_photo" value="<?= sv('price_id_photo') ?>" min="0"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                <span class="text-sm text-gray-400 shrink-0">грн</span>
              </div>
            </div>
          </div>

          <div>
            <p class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
              <i class="fa-solid fa-image text-emerald-400 text-xs"></i> Друк фото
            </p>
            <div class="space-y-2">
              <?php foreach (['photo_a6'=>'А6 (10×15)', 'photo_a4'=>'А4', 'photo_a3'=>'А3'] as $key=>$label): ?>
              <div class="flex items-center gap-3">
                <label class="text-sm text-gray-500 w-32 shrink-0"><?= $label ?></label>
                <div class="flex items-center gap-1.5 flex-1">
                  <input type="number" name="price_<?= $key ?>" value="<?= sv('price_'.$key) ?>" min="0"
                         class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                  <span class="text-sm text-gray-400 shrink-0">грн</span>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="space-y-4">
            <div>
              <p class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                <i class="fa-solid fa-mug-hot text-orange-400 text-xs"></i> Друк на чашках
              </p>
              <div class="flex items-center gap-1.5">
                <input type="number" name="price_mug" value="<?= sv('price_mug') ?>" min="0"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                <span class="text-sm text-gray-400 shrink-0">грн</span>
              </div>
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                <i class="fa-solid fa-shirt text-rose-400 text-xs"></i> Друк на футболках
              </p>
              <div class="flex items-center gap-1.5">
                <input type="number" name="price_tshirt" value="<?= sv('price_tshirt') ?>" min="0"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                <span class="text-sm text-gray-400 shrink-0">грн</span>
              </div>
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                <i class="fa-regular fa-image text-teal-400 text-xs"></i> Фото в рамці
              </p>
              <div class="flex items-center gap-1.5">
                <input type="number" name="price_frame" value="<?= sv('price_frame') ?>" min="0"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                <span class="text-sm text-gray-400 shrink-0">грн</span>
              </div>
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                <i class="fa-solid fa-book-open text-xs" style="color:rgb(243,79,118)"></i> Зшивання на пружині
              </p>
              <div class="flex items-center gap-1.5">
                <input type="number" name="price_binding" value="<?= sv('price_binding') ?>" min="0"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                <span class="text-sm text-gray-400 shrink-0">грн</span>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Internet Café -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-base font-bold text-gray-900 mb-5 flex items-center gap-2">
          <i class="fa-solid fa-display text-brand text-sm"></i> Інтернет-кафе
        </h2>
        <div class="space-y-4">
          <div>
            <label class="field-label">Заголовок секції</label>
            <input type="text" name="internet_title" value="<?= sv('internet_title') ?>"
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
          </div>
          <div>
            <label class="field-label">Опис</label>
            <textarea name="internet_desc" rows="3"
                      class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand resize-none"><?= sv('internet_desc') ?></textarea>
          </div>
          <div>
            <label class="field-label">Ціна за годину (залиште порожнім, якщо не відображати)</label>
            <div class="flex items-center gap-1.5 max-w-xs">
              <input type="number" name="price_internet_hour" value="<?= sv('price_internet_hour') ?>" min="0"
                     class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand"
                     placeholder="напр. 30">
              <span class="text-sm text-gray-400 shrink-0">грн/год</span>
            </div>
          </div>
        </div>
      </div>

      <button type="submit"
              class="bg-brand text-white font-semibold px-8 py-3 rounded-xl hover:bg-brand-dark transition-colors flex items-center gap-2">
        <i class="fa-solid fa-floppy-disk"></i> Зберегти контент
      </button>
    </form>
  </div>
  <!-- END TAB: CONTENT -->

  <!-- ==================== TAB: SETTINGS ==================== -->
  <div id="tab-settings" class="tab-pane <?= $active_tab === 'settings' ? 'active' : '' ?>">
    <form method="POST">
      <input type="hidden" name="action" value="save_settings">

      <!-- Contact Info -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-base font-bold text-gray-900 mb-5 flex items-center gap-2">
          <i class="fa-solid fa-address-card text-brand text-sm"></i> Контактні дані
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="field-label">Назва бізнесу</label>
            <input type="text" name="business_name" value="<?= sv('business_name') ?>"
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
          </div>
          <div>
            <label class="field-label">Адреса</label>
            <input type="text" name="address" value="<?= sv('address') ?>"
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
          </div>
          <div>
            <label class="field-label">Телефон (для дзвінків)</label>
            <input type="text" name="phone" value="<?= sv('phone') ?>"
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand"
                   placeholder="096 890 40 55">
          </div>
          <div>
            <label class="field-label">Телефон менеджера (Telegram / Viber / WhatsApp)</label>
            <input type="text" name="phone_manager" value="<?= sv('phone_manager') ?>"
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand"
                   placeholder="067 201 12 01">
          </div>
        </div>
      </div>

      <!-- Analytics -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
          <i class="fa-brands fa-google text-brand text-sm"></i> Аналітика
        </h2>
        <div>
          <label class="field-label">Google Analytics ID</label>
          <input type="text" name="analytics_id" value="<?= sv('analytics_id') ?>"
                 class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand max-w-xs"
                 placeholder="G-XXXXXXXXXX">
        </div>
      </div>

      <!-- Security -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
          <i class="fa-solid fa-lock text-brand text-sm"></i> Безпека
        </h2>
        <div>
          <label class="field-label">Новий пароль (залиште порожнім, щоб не змінювати)</label>
          <input type="password" name="new_password" autocomplete="new-password"
                 class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand max-w-xs"
                 placeholder="••••••••">
        </div>
      </div>

      <button type="submit"
              class="bg-brand text-white font-semibold px-8 py-3 rounded-xl hover:bg-brand-dark transition-colors flex items-center gap-2">
        <i class="fa-solid fa-floppy-disk"></i> Зберегти налаштування
      </button>
    </form>
  </div>
  <!-- END TAB: SETTINGS -->
</main>

<?php endif; ?>

<script>
(function () {
  // Tab switching
  const tabBtns  = document.querySelectorAll('.tab-btn');
  const tabPanes = document.querySelectorAll('.tab-pane');
  tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const target = btn.dataset.tab;
      tabBtns.forEach(b  => b.classList.remove('active'));
      tabPanes.forEach(p => p.classList.remove('active'));
      btn.classList.add('active');
      document.getElementById('tab-' + target)?.classList.add('active');
      history.replaceState(null, '', '?tab=' + target);
    });
  });

  // SEO character counters + SERP preview
  const titleEl = document.getElementById('meta-title');
  const descEl  = document.getElementById('meta-desc');
  const titleCount = document.getElementById('title-count');
  const descCount  = document.getElementById('desc-count');
  const serpTitle  = document.getElementById('serp-title');
  const serpDesc   = document.getElementById('serp-desc');

  function colorClass(len, ok, warn) {
    if (len <= ok)   return 'char-ok';
    if (len <= warn) return 'char-warn';
    return 'char-bad';
  }
  function updateMeta() {
    if (!titleEl) return;
    const tLen = titleEl.value.length;
    const dLen = descEl.value.length;
    titleCount.textContent = tLen + ' / 70';
    titleCount.className = colorClass(tLen, 70, 85);
    descCount.textContent = dLen + ' / 160';
    descCount.className = colorClass(dLen, 160, 180);
    if (serpTitle) { serpTitle.textContent = titleEl.value || '—'; }
    if (serpDesc)  { serpDesc.textContent  = descEl.value  || '—'; }
  }
  if (titleEl) {
    titleEl.addEventListener('input', updateMeta);
    descEl.addEventListener('input', updateMeta);
    updateMeta();
  }
})();
</script>
</body>
</html>

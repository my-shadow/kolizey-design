<?php
date_default_timezone_set('Europe/Kyiv');
$data = json_decode(file_get_contents('data.json'), true) ?? [];
$s = $data['settings'] ?? [];

function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
function sv($k, $d = '') { global $s; return htmlspecialchars($s[$k] ?? $d, ENT_QUOTES, 'UTF-8'); }

$business     = $s['business_name']    ?? 'Дизайн-студія Колізей';
$address      = $s['address']          ?? 'вул. Шевченка 89';
$phone        = $s['phone']            ?? '096 890 40 55';
$phone_mgr    = $s['phone_manager']    ?? '067 201 12 01';
$hero_title   = $s['hero_title']       ?? 'Ваш центр друку та дизайну';
$hero_desc    = $s['hero_desc']        ?? '';
$promo_text   = $s['promo_text']       ?? '';
$int_title    = $s['internet_title']   ?? 'Інтернет-кафе';
$int_desc     = $s['internet_desc']    ?? '';
$footer_text  = $s['footer_text']      ?? '';
$analytics_id = $s['analytics_id']     ?? '';

$p = [
    'copy_bw'    => $s['price_copy_bw']    ?? '5',
    'copy_color' => $s['price_copy_color'] ?? '15',
    'lam_a4'     => $s['price_lam_a4']     ?? '30',
    'lam_a3'     => $s['price_lam_a3']     ?? '50',
    'scan'       => $s['price_scan']        ?? '5',
    'id_photo'   => $s['price_id_photo']   ?? '150',
    'photo_a6'   => $s['price_photo_a6']   ?? '10',
    'photo_a4'   => $s['price_photo_a4']   ?? '25',
    'photo_a3'   => $s['price_photo_a3']   ?? '40',
    'mug'        => $s['price_mug']        ?? '400',
    'tshirt'     => $s['price_tshirt']     ?? '500',
    'frame'      => $s['price_frame']      ?? '150',
    'internet'   => $s['price_internet_hour'] ?? '',
];

$photos = glob('photos/*.jpg');
usort($photos, function($a, $b) {
    preg_match('/photo_(\d+)/', $a, $ma);
    preg_match('/photo_(\d+)/', $b, $mb);
    return ($ma[1] ?? 0) - ($mb[1] ?? 0);
});
$gallery_photos = array_values(array_filter($photos, fn($ph) => strpos($ph, 'photo_10') === false));
$cafe_photo = 'photos/photo_10.jpg';

$phone_int = '+38' . preg_replace('/\D/', '', $phone);
$mgr_int   = '+38' . preg_replace('/\D/', '', $phone_mgr);
$wa_num    = ltrim($mgr_int, '+');

$tg_link  = 'https://t.me/' . $mgr_int;
$vb_link  = 'viber://chat?number=' . urlencode($mgr_int);
$wa_link  = 'https://wa.me/' . $wa_num;
$tel_link = 'tel:' . $phone_int;
?>
<!DOCTYPE html>
<html lang="uk">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= sv('meta_title') ?: e($business) ?></title>
<meta name="description" content="<?= sv('meta_desc') ?>">
<meta property="og:title" content="<?= sv('meta_title') ?: e($business) ?>">
<meta property="og:description" content="<?= sv('meta_desc') ?>">
<meta property="og:type" content="website">
<?php if (!empty($s['og_image'])): ?>
<meta property="og:image" content="<?= sv('og_image') ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<?php endif; ?>
<meta property="og:locale" content="uk_UA">
<link rel="icon" href="favicon.ico" type="image/x-icon">
<?php if ($analytics_id): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($analytics_id) ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= e($analytics_id) ?>');</script>
<?php endif; ?>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        brand: { DEFAULT: '#2f0578', light: '#4a0daa', dark: '#1e0350' }
      },
      fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] }
    }
  }
}
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
  * { scroll-behavior: smooth; }
  body { font-family: 'Inter', system-ui, sans-serif; }

  /* Animations */
  .anim { opacity: 0; transition: opacity .6s ease, transform .6s ease; }
  .anim-up    { transform: translateY(32px); }
  .anim-left  { transform: translateX(-32px); }
  .anim-right { transform: translateX(32px); }
  .anim-scale { transform: scale(.92); }
  .anim.visible { opacity: 1; transform: none; }
  .d1{transition-delay:.1s}.d2{transition-delay:.2s}.d3{transition-delay:.3s}
  .d4{transition-delay:.4s}.d5{transition-delay:.5s}.d6{transition-delay:.6s}

  /* Ticker */
  .ticker-wrap { overflow: hidden; white-space: nowrap; }
  .ticker-inner { display: inline-flex; animation: ticker 30s linear infinite; }
  .ticker-inner:hover { animation-play-state: paused; }
  @keyframes ticker { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }

  /* Gallery scroll */
  .gallery-track { display: flex; gap: 12px; width: max-content; }
  .gallery-row-1 .gallery-track { animation: gl-left 40s linear infinite; }
  .gallery-row-2 .gallery-track { animation: gl-right 40s linear infinite; }
  .gallery-row-1:hover .gallery-track,
  .gallery-row-2:hover .gallery-track { animation-play-state: paused; }
  @keyframes gl-left  { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }
  @keyframes gl-right { 0%{transform:translateX(-50%)} 100%{transform:translateX(0)} }

  /* Lightbox */
  #lightbox { display:none; position:fixed; inset:0; background:rgba(0,0,0,.92);
    z-index:9999; align-items:center; justify-content:center; }
  #lightbox.open { display:flex; }
  #lightbox img { max-height:90vh; max-width:90vw; object-fit:contain; border-radius:8px; }
  .lb-btn { position:absolute; top:50%; transform:translateY(-50%);
    background:rgba(255,255,255,.15); color:#fff; border:none; cursor:pointer;
    width:48px; height:48px; border-radius:50%; font-size:20px;
    display:flex; align-items:center; justify-content:center;
    transition:background .2s; }
  .lb-btn:hover { background:rgba(255,255,255,.3); }
  #lb-prev { left:16px; }
  #lb-next { right:16px; }
  #lb-close { position:absolute; top:16px; right:16px;
    background:rgba(255,255,255,.15); color:#fff; border:none; cursor:pointer;
    width:40px; height:40px; border-radius:50%; font-size:18px;
    display:flex; align-items:center; justify-content:center; transition:background .2s; }
  #lb-close:hover { background:rgba(255,255,255,.3); }
  #lb-counter { position:absolute; bottom:20px; left:50%; transform:translateX(-50%);
    color:rgba(255,255,255,.6); font-size:14px; }

  /* Navbar scroll shadow */
  .nav-scrolled { box-shadow: 0 2px 20px rgba(0,0,0,.08); }

  /* Service card hover */
  .service-card { transition: transform .25s ease, box-shadow .25s ease; }
  .service-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(124,58,237,.15); }
</style>
</head>
<body class="bg-white text-gray-800 antialiased">

<!-- NAVBAR -->
<nav id="navbar" class="fixed top-0 inset-x-0 z-50 bg-white/95 backdrop-blur-sm transition-shadow duration-300">
  <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
    <a href="#" class="flex items-center gap-2 font-black text-xl text-brand select-none">
      <i class="fa-solid fa-print text-brand"></i>
      <span>Колізей</span>
    </a>
    <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
      <a href="#services" class="hover:text-brand transition-colors">Послуги</a>
      <a href="#gallery"  class="hover:text-brand transition-colors">Галерея</a>
      <a href="#internet" class="hover:text-brand transition-colors">Інтернет</a>
      <a href="#contacts" class="hover:text-brand transition-colors">Контакти</a>
    </div>
    <a href="<?= e($tel_link) ?>" class="flex items-center gap-2 bg-brand text-white text-sm font-semibold px-4 py-2 rounded-full hover:bg-brand-dark transition-colors">
      <i class="fa-solid fa-phone text-xs"></i>
      <span><?= e($phone) ?></span>
    </a>
  </div>
</nav>

<!-- HERO -->
<section class="pt-16 min-h-screen flex items-center bg-gradient-to-br from-violet-50 via-white to-purple-50">
  <div class="max-w-6xl mx-auto px-4 py-20 grid md:grid-cols-2 gap-12 items-center">
    <div>
      <div class="anim anim-up inline-flex items-center gap-2 bg-violet-100 text-violet-700 text-sm font-semibold px-4 py-1.5 rounded-full mb-6">
        <i class="fa-solid fa-location-dot text-xs"></i>
        <span><?= e($address) ?></span>
      </div>
      <h1 class="anim anim-up d1 text-4xl md:text-5xl font-black text-gray-900 leading-tight mb-5">
        <?= e($hero_title) ?>
      </h1>
      <p class="anim anim-up d2 text-lg text-gray-500 mb-8 leading-relaxed">
        <?= e($hero_desc) ?>
      </p>
      <div class="anim anim-up d3 flex flex-wrap gap-3">
        <a href="<?= e($tg_link) ?>" target="_blank"
           class="flex items-center gap-2 bg-[#2AABEE] text-white font-semibold px-5 py-3 rounded-xl hover:opacity-90 transition-opacity shadow-sm">
          <i class="fa-brands fa-telegram text-lg"></i>
          <span>Telegram</span>
        </a>
        <a href="<?= e($vb_link) ?>"
           class="flex items-center gap-2 bg-[#7360F2] text-white font-semibold px-5 py-3 rounded-xl hover:opacity-90 transition-opacity shadow-sm">
          <i class="fa-brands fa-viber text-lg"></i>
          <span>Viber</span>
        </a>
        <a href="<?= e($wa_link) ?>" target="_blank"
           class="flex items-center gap-2 bg-[#25D366] text-white font-semibold px-5 py-3 rounded-xl hover:opacity-90 transition-opacity shadow-sm">
          <i class="fa-brands fa-whatsapp text-lg"></i>
          <span>WhatsApp</span>
        </a>
        <a href="<?= e($tel_link) ?>"
           class="flex items-center gap-2 bg-gray-900 text-white font-semibold px-5 py-3 rounded-xl hover:opacity-90 transition-opacity shadow-sm">
          <i class="fa-solid fa-phone text-sm"></i>
          <span>Зателефонувати</span>
        </a>
      </div>
    </div>
    <div class="anim anim-right d2 flex justify-center">
      <div class="relative">
        <div class="absolute inset-0 bg-gradient-to-br from-violet-400 to-purple-600 rounded-3xl blur-3xl opacity-20 scale-110"></div>
        <img src="photos/photo_2.jpg" alt="Дизайн-студія Колізей"
             class="relative w-72 md:w-96 h-auto rounded-3xl shadow-2xl object-cover">
      </div>
    </div>
  </div>
</section>

<!-- TICKER -->
<div class="bg-brand py-3 overflow-hidden">
  <div class="ticker-wrap">
    <div class="ticker-inner">
      <?php for ($i = 0; $i < 4; $i++): ?>
        <span class="text-white text-sm font-medium px-8 opacity-90"><?= e($promo_text) ?></span>
        <span class="text-violet-300 px-2">·</span>
      <?php endfor; ?>
    </div>
  </div>
</div>

<!-- SERVICES -->
<section id="services" class="py-20 bg-white">
  <div class="max-w-6xl mx-auto px-4">
    <div class="text-center mb-14">
      <h2 class="anim anim-up text-3xl md:text-4xl font-black text-gray-900 mb-3">Наші послуги</h2>
      <p class="anim anim-up d1 text-gray-500 text-lg">Усе для друку та персоналізації — в одному місці</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

      <!-- Ксерокопія -->
      <div class="anim anim-up d1 service-card bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center mb-4">
          <i class="fa-solid fa-copy text-xl text-violet-600"></i>
        </div>
        <h3 class="font-bold text-gray-900 mb-3">Ксерокопія</h3>
        <div class="space-y-1 text-sm text-gray-500">
          <div class="flex justify-between">
            <span>Чорно-біла</span>
            <span class="font-semibold text-gray-800"><?= e($p['copy_bw']) ?> грн/лист</span>
          </div>
          <div class="flex justify-between">
            <span>Кольорова</span>
            <span class="font-semibold text-gray-800"><?= e($p['copy_color']) ?> грн/лист</span>
          </div>
        </div>
      </div>

      <!-- Ламінування -->
      <div class="anim anim-up d2 service-card bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="w-12 h-12 bg-fuchsia-100 rounded-xl flex items-center justify-center mb-4">
          <i class="fa-solid fa-layer-group text-xl text-fuchsia-600"></i>
        </div>
        <h3 class="font-bold text-gray-900 mb-3">Ламінування</h3>
        <div class="space-y-1 text-sm text-gray-500">
          <div class="flex justify-between">
            <span>Формат А4</span>
            <span class="font-semibold text-gray-800"><?= e($p['lam_a4']) ?> грн</span>
          </div>
          <div class="flex justify-between">
            <span>Формат А3</span>
            <span class="font-semibold text-gray-800"><?= e($p['lam_a3']) ?> грн</span>
          </div>
        </div>
      </div>

      <!-- Сканування -->
      <div class="anim anim-up d3 service-card bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="w-12 h-12 bg-sky-100 rounded-xl flex items-center justify-center mb-4">
          <i class="fa-solid fa-file-import text-xl text-sky-600"></i>
        </div>
        <h3 class="font-bold text-gray-900 mb-3">Сканування</h3>
        <div class="text-sm text-gray-500">
          <div class="flex justify-between">
            <span>За аркуш</span>
            <span class="font-semibold text-gray-800"><?= e($p['scan']) ?> грн</span>
          </div>
        </div>
      </div>

      <!-- Фото на документи -->
      <div class="anim anim-up d4 service-card bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4">
          <i class="fa-solid fa-id-card text-xl text-amber-600"></i>
        </div>
        <h3 class="font-bold text-gray-900 mb-3">Фото на документи</h3>
        <div class="text-sm text-gray-500">
          <div class="flex justify-between">
            <span>6 фото · 5 хвилин</span>
            <span class="font-semibold text-gray-800"><?= e($p['id_photo']) ?> грн</span>
          </div>
        </div>
      </div>

      <!-- Друк фото -->
      <div class="anim anim-up d1 service-card bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
          <i class="fa-solid fa-image text-xl text-emerald-600"></i>
        </div>
        <h3 class="font-bold text-gray-900 mb-3">Друк фото</h3>
        <div class="space-y-1 text-sm text-gray-500">
          <div class="flex justify-between">
            <span>Формат А6 (10×15)</span>
            <span class="font-semibold text-gray-800"><?= e($p['photo_a6']) ?> грн</span>
          </div>
          <div class="flex justify-between">
            <span>Формат А4</span>
            <span class="font-semibold text-gray-800"><?= e($p['photo_a4']) ?> грн</span>
          </div>
          <div class="flex justify-between">
            <span>Формат А3</span>
            <span class="font-semibold text-gray-800"><?= e($p['photo_a3']) ?> грн</span>
          </div>
        </div>
      </div>

      <!-- Друк на чашках -->
      <div class="anim anim-up d2 service-card bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mb-4">
          <i class="fa-solid fa-mug-hot text-xl text-orange-600"></i>
        </div>
        <h3 class="font-bold text-gray-900 mb-3">Друк на чашках</h3>
        <div class="text-sm text-gray-500">
          <div class="flex justify-between">
            <span>Ваш дизайн або фото</span>
            <span class="font-semibold text-gray-800"><?= e($p['mug']) ?> грн</span>
          </div>
        </div>
      </div>

      <!-- Друк на футболках -->
      <div class="anim anim-up d3 service-card bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="w-12 h-12 bg-rose-100 rounded-xl flex items-center justify-center mb-4">
          <i class="fa-solid fa-shirt text-xl text-rose-600"></i>
        </div>
        <h3 class="font-bold text-gray-900 mb-3">Друк на футболках</h3>
        <div class="text-sm text-gray-500">
          <div class="flex justify-between">
            <span>Ваш дизайн або фото</span>
            <span class="font-semibold text-gray-800"><?= e($p['tshirt']) ?> грн</span>
          </div>
        </div>
      </div>

      <!-- Фото в рамці -->
      <div class="anim anim-up d4 service-card bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center mb-4">
          <i class="fa-regular fa-image text-xl text-teal-600"></i>
        </div>
        <h3 class="font-bold text-gray-900 mb-3">Фото в рамці</h3>
        <div class="text-sm text-gray-500">
          <div class="flex justify-between">
            <span>Різні розміри рамок</span>
            <span class="font-semibold text-gray-800"><?= e($p['frame']) ?> грн</span>
          </div>
        </div>
      </div>

    </div>

    <!-- CTA under services -->
    <div class="anim anim-up mt-12 text-center">
      <p class="text-gray-500 mb-4">Маєте запитання щодо послуг або хочете замовити?</p>
      <div class="flex flex-wrap justify-center gap-3">
        <a href="<?= e($tg_link) ?>" target="_blank"
           class="inline-flex items-center gap-2 bg-[#2AABEE] text-white font-semibold px-5 py-2.5 rounded-xl hover:opacity-90 transition-opacity text-sm">
          <i class="fa-brands fa-telegram"></i> Telegram
        </a>
        <a href="<?= e($vb_link) ?>"
           class="inline-flex items-center gap-2 bg-[#7360F2] text-white font-semibold px-5 py-2.5 rounded-xl hover:opacity-90 transition-opacity text-sm">
          <i class="fa-brands fa-viber"></i> Viber
        </a>
        <a href="<?= e($wa_link) ?>" target="_blank"
           class="inline-flex items-center gap-2 bg-[#25D366] text-white font-semibold px-5 py-2.5 rounded-xl hover:opacity-90 transition-opacity text-sm">
          <i class="fa-brands fa-whatsapp"></i> WhatsApp
        </a>
        <a href="<?= e($tel_link) ?>"
           class="inline-flex items-center gap-2 bg-gray-900 text-white font-semibold px-5 py-2.5 rounded-xl hover:opacity-90 transition-opacity text-sm">
          <i class="fa-solid fa-phone text-xs"></i> <?= e($phone) ?>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- GALLERY -->
<section id="gallery" class="py-20 bg-gradient-to-b from-violet-50 to-white overflow-hidden">
  <div class="max-w-6xl mx-auto px-4 mb-10 text-center">
    <h2 class="anim anim-up text-3xl md:text-4xl font-black text-gray-900 mb-3">Наші роботи</h2>
    <p class="anim anim-up d1 text-gray-500 text-lg">Натисніть на фото, щоб переглянути</p>
  </div>

  <?php
  $doubled = array_merge($gallery_photos, $gallery_photos);
  $half = count($gallery_photos);
  $row1 = $doubled;
  $row2 = array_merge(array_slice($gallery_photos, (int)floor($half/2)), $gallery_photos, array_slice($gallery_photos, 0, (int)floor($half/2)));
  $row2 = array_merge($row2, $row2);
  ?>

  <div class="space-y-3">
    <!-- Row 1 -->
    <div class="gallery-row-1">
      <div class="gallery-track">
        <?php foreach ($row1 as $ph): ?>
          <img src="<?= e($ph) ?>" alt="Роботи студії"
               class="h-56 w-auto rounded-xl object-cover cursor-pointer hover:opacity-90 transition-opacity gallery-img"
               loading="lazy">
        <?php endforeach; ?>
      </div>
    </div>
    <!-- Row 2 -->
    <div class="gallery-row-2">
      <div class="gallery-track">
        <?php foreach ($row2 as $ph): ?>
          <img src="<?= e($ph) ?>" alt="Роботи студії"
               class="h-56 w-auto rounded-xl object-cover cursor-pointer hover:opacity-90 transition-opacity gallery-img"
               loading="lazy">
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- INTERNET CAFÉ -->
<section id="internet" class="py-20 bg-white">
  <div class="max-w-6xl mx-auto px-4 grid md:grid-cols-2 gap-12 items-center">
    <div class="anim anim-left">
      <img src="<?= e($cafe_photo) ?>" alt="Інтернет-кафе"
           class="w-full h-80 object-cover rounded-3xl shadow-xl">
    </div>
    <div>
      <div class="anim anim-up inline-flex items-center gap-2 bg-indigo-100 text-indigo-700 text-sm font-semibold px-4 py-1.5 rounded-full mb-5">
        <i class="fa-solid fa-display text-xs"></i>
        <span>Інтернет-кафе</span>
      </div>
      <h2 class="anim anim-up d1 text-3xl md:text-4xl font-black text-gray-900 mb-4"><?= e($int_title) ?></h2>
      <p class="anim anim-up d2 text-gray-500 text-lg leading-relaxed mb-6"><?= e($int_desc) ?></p>
      <?php if (!empty($p['internet'])): ?>
        <div class="anim anim-up d3 inline-flex items-center gap-3 bg-indigo-50 rounded-xl px-5 py-3 mb-6">
          <i class="fa-solid fa-clock text-indigo-600"></i>
          <span class="text-gray-700">Вартість: <strong class="text-gray-900"><?= e($p['internet']) ?> грн/год</strong></span>
        </div>
      <?php endif; ?>
      <div class="anim anim-up d4 flex flex-wrap gap-3">
        <a href="<?= e($tg_link) ?>" target="_blank"
           class="inline-flex items-center gap-2 bg-[#2AABEE] text-white font-semibold px-5 py-2.5 rounded-xl hover:opacity-90 transition-opacity text-sm">
          <i class="fa-brands fa-telegram"></i> Telegram
        </a>
        <a href="<?= e($tel_link) ?>"
           class="inline-flex items-center gap-2 bg-gray-900 text-white font-semibold px-5 py-2.5 rounded-xl hover:opacity-90 transition-opacity text-sm">
          <i class="fa-solid fa-phone text-xs"></i> Зателефонувати
        </a>
      </div>
    </div>
  </div>
</section>

<!-- CONTACTS -->
<section id="contacts" class="py-20 bg-gradient-to-b from-violet-50 to-white">
  <div class="max-w-6xl mx-auto px-4">
    <div class="text-center mb-14">
      <h2 class="anim anim-up text-3xl md:text-4xl font-black text-gray-900 mb-3">Контакти</h2>
      <p class="anim anim-up d1 text-gray-500 text-lg">Пишіть, телефонуйте або заходьте до нас</p>
    </div>
    <div class="grid md:grid-cols-3 gap-6 mb-10">
      <!-- Address -->
      <div class="anim anim-up d1 bg-white border border-gray-100 rounded-2xl p-6 shadow-sm text-center">
        <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center mx-auto mb-4">
          <i class="fa-solid fa-location-dot text-xl text-violet-600"></i>
        </div>
        <h3 class="font-bold text-gray-900 mb-2">Адреса</h3>
        <p class="text-gray-500 text-sm"><?= e($address) ?></p>
      </div>
      <!-- Phone -->
      <div class="anim anim-up d2 bg-white border border-gray-100 rounded-2xl p-6 shadow-sm text-center">
        <div class="w-12 h-12 bg-fuchsia-100 rounded-xl flex items-center justify-center mx-auto mb-4">
          <i class="fa-solid fa-phone text-xl text-fuchsia-600"></i>
        </div>
        <h3 class="font-bold text-gray-900 mb-2">Телефон</h3>
        <a href="<?= e($tel_link) ?>" class="text-brand font-semibold hover:underline"><?= e($phone) ?></a>
      </div>
      <!-- Messengers -->
      <div class="anim anim-up d3 bg-white border border-gray-100 rounded-2xl p-6 shadow-sm text-center">
        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mx-auto mb-4">
          <i class="fa-solid fa-comments text-xl text-emerald-600"></i>
        </div>
        <h3 class="font-bold text-gray-900 mb-3">Менеджер</h3>
        <div class="flex justify-center gap-3">
          <a href="<?= e($tg_link) ?>" target="_blank"
             class="w-10 h-10 bg-[#2AABEE] text-white rounded-full flex items-center justify-center hover:opacity-80 transition-opacity">
            <i class="fa-brands fa-telegram"></i>
          </a>
          <a href="<?= e($vb_link) ?>"
             class="w-10 h-10 bg-[#7360F2] text-white rounded-full flex items-center justify-center hover:opacity-80 transition-opacity">
            <i class="fa-brands fa-viber"></i>
          </a>
          <a href="<?= e($wa_link) ?>" target="_blank"
             class="w-10 h-10 bg-[#25D366] text-white rounded-full flex items-center justify-center hover:opacity-80 transition-opacity">
            <i class="fa-brands fa-whatsapp"></i>
          </a>
        </div>
        <p class="text-xs text-gray-400 mt-2"><?= e($phone_mgr) ?></p>
      </div>
    </div>

    <!-- Big CTA block -->
    <div class="anim anim-up bg-brand rounded-3xl p-8 md:p-12 text-center text-white shadow-xl">
      <h3 class="text-2xl md:text-3xl font-black mb-3">Готові допомогти прямо зараз</h3>
      <p class="text-violet-200 mb-8 text-lg">Зв'яжіться з нами будь-яким зручним способом</p>
      <div class="flex flex-wrap justify-center gap-4">
        <a href="<?= e($tg_link) ?>" target="_blank"
           class="flex items-center gap-2 bg-white/20 backdrop-blur text-white font-semibold px-6 py-3 rounded-xl hover:bg-white/30 transition-colors border border-white/20">
          <i class="fa-brands fa-telegram text-lg"></i> Telegram
        </a>
        <a href="<?= e($vb_link) ?>"
           class="flex items-center gap-2 bg-white/20 backdrop-blur text-white font-semibold px-6 py-3 rounded-xl hover:bg-white/30 transition-colors border border-white/20">
          <i class="fa-brands fa-viber text-lg"></i> Viber
        </a>
        <a href="<?= e($wa_link) ?>" target="_blank"
           class="flex items-center gap-2 bg-white/20 backdrop-blur text-white font-semibold px-6 py-3 rounded-xl hover:bg-white/30 transition-colors border border-white/20">
          <i class="fa-brands fa-whatsapp text-lg"></i> WhatsApp
        </a>
        <a href="<?= e($tel_link) ?>"
           class="flex items-center gap-2 bg-white text-brand font-semibold px-6 py-3 rounded-xl hover:bg-violet-50 transition-colors shadow-sm">
          <i class="fa-solid fa-phone text-sm"></i> <?= e($phone) ?>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="bg-gray-900 text-gray-400 py-8">
  <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row items-center justify-between gap-4">
    <div class="flex items-center gap-2 text-white font-bold">
      <i class="fa-solid fa-print text-brand"></i>
      <span><?= e($business) ?></span>
    </div>
    <nav class="flex flex-wrap justify-center gap-6 text-sm">
      <a href="#services"  class="hover:text-white transition-colors">Послуги</a>
      <a href="#gallery"   class="hover:text-white transition-colors">Галерея</a>
      <a href="#internet"  class="hover:text-white transition-colors">Інтернет</a>
      <a href="#contacts"  class="hover:text-white transition-colors">Контакти</a>
    </nav>
    <p class="text-sm"><?= e($footer_text) ?></p>
  </div>
</footer>

<!-- LIGHTBOX -->
<div id="lightbox" role="dialog" aria-modal="true">
  <button id="lb-close" aria-label="Закрити"><i class="fa-solid fa-xmark"></i></button>
  <button id="lb-prev"  class="lb-btn" aria-label="Попереднє"><i class="fa-solid fa-chevron-left"></i></button>
  <img id="lb-img" src="" alt="Фото">
  <button id="lb-next"  class="lb-btn" aria-label="Наступне"><i class="fa-solid fa-chevron-right"></i></button>
  <div id="lb-counter"></div>
</div>

<script>
(function () {
  // --- Navbar shadow on scroll ---
  const nav = document.getElementById('navbar');
  window.addEventListener('scroll', () => {
    nav.classList.toggle('nav-scrolled', window.scrollY > 10);
  }, { passive: true });

  // --- Intersection observer for animations ---
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); } });
  }, { threshold: 0.12 });
  document.querySelectorAll('.anim').forEach(el => observer.observe(el));

  // --- Lightbox ---
  const lb       = document.getElementById('lightbox');
  const lbImg    = document.getElementById('lb-img');
  const lbCount  = document.getElementById('lb-counter');
  const allImgs  = <?= json_encode(array_values($gallery_photos)) ?>;
  let current = 0;

  function openLb(idx) {
    current = ((idx % allImgs.length) + allImgs.length) % allImgs.length;
    lbImg.src = allImgs[current];
    lbCount.textContent = (current + 1) + ' / ' + allImgs.length;
    lb.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeLb() {
    lb.classList.remove('open');
    document.body.style.overflow = '';
  }

  document.querySelectorAll('.gallery-img').forEach(img => {
    img.addEventListener('click', () => {
      const src = img.getAttribute('src');
      const idx = allImgs.indexOf(src);
      openLb(idx >= 0 ? idx : 0);
    });
  });

  document.getElementById('lb-close').addEventListener('click', closeLb);
  document.getElementById('lb-prev').addEventListener('click', () => openLb(current - 1));
  document.getElementById('lb-next').addEventListener('click', () => openLb(current + 1));
  lb.addEventListener('click', e => { if (e.target === lb) closeLb(); });

  document.addEventListener('keydown', e => {
    if (!lb.classList.contains('open')) return;
    if (e.key === 'Escape')     closeLb();
    if (e.key === 'ArrowLeft')  openLb(current - 1);
    if (e.key === 'ArrowRight') openLb(current + 1);
  });

  // Touch swipe in lightbox
  let touchX = null;
  lb.addEventListener('touchstart', e => { touchX = e.touches[0].clientX; }, { passive: true });
  lb.addEventListener('touchend', e => {
    if (touchX === null) return;
    const dx = e.changedTouches[0].clientX - touchX;
    if (Math.abs(dx) > 50) openLb(dx < 0 ? current + 1 : current - 1);
    touchX = null;
  });
})();
</script>
</body>
</html>

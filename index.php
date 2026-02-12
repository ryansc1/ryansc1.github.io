<?php
/**
 * index.php - Maintenance Page
 * Cara pakai:
 * 1) Upload file ini jadi index.php (atau route ke file ini).
 * 2) Atur $maintenance = true untuk aktif.
 * 3) (Opsional) set $allowed_ips biar kamu tetap bisa akses website normal.
 */

$maintenance = true;

// IP yang boleh bypass maintenance (mis. IP kantor/rumah kamu). Kosongkan kalau tidak perlu.
$allowed_ips = [
  // "123.123.123.123",
];

// URL tujuan kalau maintenance dimatikan (mis. home utama)
$redirect_when_off = "/";

// (Opsional) ETA selesai maintenance (untuk countdown)
$eta_finish = "2026-02-12 23:59:00"; // format: Y-m-d H:i:s (WIB/Server time)
$show_countdown = true;

// ====================== LOGIC ======================
$client_ip = $_SERVER['REMOTE_ADDR'] ?? '';
$is_allowed = in_array($client_ip, $allowed_ips, true);

// Jika maintenance OFF atau IP diizinkan, redirect ke website normal
if (!$maintenance || $is_allowed) {
  // Kalau kamu menaruh file ini terpisah (bukan root), sesuaikan redirect-nya
  if (!headers_sent()) {
    header("Location: " . $redirect_when_off, true, 302);
  }
  exit;
}

// Set status code 503 dan header Retry-After agar SEO tahu ini sementara
http_response_code(503);
header("Content-Type: text/html; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Retry-After: 3600"); // 1 jam

// Hitung countdown (detik)
$eta_ts = strtotime($eta_finish);
$now_ts = time();
$remaining = max(0, ($eta_ts ?: 0) - $now_ts);

$title = "Website Sedang Maintenance";
$brand = "Nama Website Kamu";
$subtitle = "Kami sedang melakukan peningkatan sistem agar layanan lebih cepat dan stabil.";
$contact = "Kontak: WhatsApp 08xx-xxxx-xxxx | Email: support@domain.com";
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex,nofollow" />
  <title><?= htmlspecialchars($title) ?> - <?= htmlspecialchars($brand) ?></title>
  <style>
    :root{
      --bg:#0b1220;
      --card:#0f1b33;
      --text:#e7eefc;
      --muted:#a8b3cf;
      --accent:#5eead4;
      --accent2:#60a5fa;
      --danger:#fb7185;
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";
      background: radial-gradient(1200px 700px at 20% 10%, rgba(96,165,250,.25), transparent 55%),
                  radial-gradient(1000px 600px at 80% 30%, rgba(94,234,212,.18), transparent 60%),
                  var(--bg);
      color:var(--text);
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:24px;
    }
    .wrap{max-width:860px; width:100%;}
    .card{
      background: rgba(15,27,51,.82);
      border: 1px solid rgba(255,255,255,.08);
      box-shadow: 0 20px 80px rgba(0,0,0,.45);
      border-radius: 20px;
      overflow:hidden;
    }
    .top{
      padding:28px 28px 0 28px;
      display:flex;
      gap:16px;
      align-items:flex-start;
      justify-content:space-between;
      flex-wrap:wrap;
    }
    .badge{
      display:inline-flex;
      align-items:center;
      gap:10px;
      padding:10px 14px;
      border-radius:999px;
      background: rgba(94,234,212,.12);
      border: 1px solid rgba(94,234,212,.25);
      color: var(--accent);
      font-weight:600;
      letter-spacing:.2px;
    }
    .dot{
      width:10px;height:10px;border-radius:50%;
      background: var(--accent);
      box-shadow: 0 0 0 6px rgba(94,234,212,.12);
    }
    h1{
      margin:14px 0 10px 0;
      font-size: clamp(26px, 3.2vw, 42px);
      line-height:1.1;
      letter-spacing:-.4px;
    }
    p{margin:0; color:var(--muted); font-size: 16px; line-height:1.6;}
    .content{padding: 0 28px 28px 28px;}
    .grid{
      margin-top:18px;
      display:grid;
      grid-template-columns: 1fr;
      gap:14px;
    }
    @media (min-width: 720px){
      .grid{grid-template-columns: 1.2fr .8fr;}
    }
    .panel{
      padding:16px;
      border-radius:16px;
      background: rgba(255,255,255,.04);
      border: 1px solid rgba(255,255,255,.06);
    }
    .kpi{
      display:flex;
      gap:10px;
      flex-wrap:wrap;
      margin-top:10px;
    }
    .kpi .pill{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:10px 12px;
      border-radius:999px;
      background: rgba(96,165,250,.10);
      border: 1px solid rgba(96,165,250,.22);
      color: #cfe6ff;
      font-weight:600;
      font-size:14px;
      white-space:nowrap;
    }
    .countdown{
      font-variant-numeric: tabular-nums;
      display:flex;
      gap:10px;
      margin-top:10px;
      flex-wrap:wrap;
    }
    .timebox{
      min-width:90px;
      padding:12px 12px;
      border-radius:14px;
      text-align:center;
      background: rgba(0,0,0,.18);
      border: 1px solid rgba(255,255,255,.06);
    }
    .timebox .num{
      font-size:22px;
      font-weight:800;
      color: var(--text);
    }
    .timebox .lbl{
      font-size:12px;
      color: var(--muted);
      margin-top:2px;
    }
    .footer{
      display:flex;
      gap:12px;
      justify-content:space-between;
      flex-wrap:wrap;
      margin-top:14px;
      padding-top:14px;
      border-top: 1px dashed rgba(255,255,255,.10);
      color: var(--muted);
      font-size: 14px;
    }
    a{color: var(--accent2); text-decoration:none}
    a:hover{text-decoration:underline}
    .small{font-size:13px; color: var(--muted)}
    .warn{color: var(--danger); font-weight:600}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <div class="top">
        <div>
          <div class="badge"><span class="dot"></span> Maintenance Mode</div>
          <h1><?= htmlspecialchars($title) ?></h1>
          <p><?= htmlspecialchars($subtitle) ?></p>
          <div class="kpi">
            <div class="pill">⚙️ Upgrade Sistem</div>
            <div class="pill">🛡️ Peningkatan Keamanan</div>
            <div class="pill">🚀 Optimasi Performa</div>
          </div>
        </div>
        <div class="panel" style="min-width:260px">
          <div style="font-weight:700; margin-bottom:6px;">Info</div>
          <div class="small">Status: <span class="warn">Sementara tidak tersedia</span></div>
          <div class="small">Kode: 503 (Service Unavailable)</div>
          <?php if ($show_countdown && $eta_ts): ?>
            <div class="small" style="margin-top:10px;">Perkiraan selesai:</div>
            <div style="font-weight:700; margin-top:4px;">
              <?= htmlspecialchars(date("d M Y H:i", $eta_ts)) ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="content">
        <div class="grid">
          <div class="panel">
            <div style="font-weight:700; margin-bottom:6px;">Apa yang bisa kamu lakukan?</div>
            <p>
              Silakan refresh beberapa saat lagi. Jika kamu butuh bantuan segera, hubungi admin melalui kontak di bawah.
            </p>
            <div class="footer">
              <div><?= htmlspecialchars($contact) ?></div>
              <div>© <?= date("Y") ?> <?= htmlspecialchars($brand) ?></div>
            </div>
          </div>

          <div class="panel">
            <div style="font-weight:700; margin-bottom:6px;">Estimasi Waktu</div>

            <?php if ($show_countdown && $eta_ts): ?>
              <div class="countdown" id="countdown">
                <div class="timebox"><div class="num" id="d">0</div><div class="lbl">Hari</div></div>
                <div class="timebox"><div class="num" id="h">0</div><div class="lbl">Jam</div></div>
                <div class="timebox"><div class="num" id="m">0</div><div class="lbl">Menit</div></div>
                <div class="timebox"><div class="num" id="s">0</div><div class="lbl">Detik</div></div>
              </div>
              <p class="small" style="margin-top:10px;">
                Jika countdown mencapai 0, coba reload halaman.
              </p>
            <?php else: ?>
              <p class="small">Maintenance sedang berlangsung. Kami akan kembali secepatnya.</p>
            <?php endif; ?>

            <p class="small" style="margin-top:10px;">
              Tips: simpan bookmark halaman ini, atau cek update di sosial media (jika ada).
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php if ($show_countdown && $eta_ts): ?>
<script>
  (function(){
    let remaining = <?= (int)$remaining ?>; // detik
    const elD = document.getElementById('d');
    const elH = document.getElementById('h');
    const elM = document.getElementById('m');
    const elS = document.getElementById('s');

    function tick(){
      if (remaining < 0) remaining = 0;

      const d = Math.floor(remaining / 86400);
      const h = Math.floor((remaining % 86400) / 3600);
      const m = Math.floor((remaining % 3600) / 60);
      const s = Math.floor(remaining % 60);

      elD.textContent = d;
      elH.textContent = h;
      elM.textContent = m;
      elS.textContent = s;

      if (remaining === 0) return;
      remaining -= 1;
      setTimeout(tick, 1000);
    }
    tick();
  })();
</script>
<?php endif; ?>
</body>
</html>

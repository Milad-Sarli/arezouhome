<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>درگاه پرداخت تست</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css">
<style>
*{box-sizing:border-box}
body{font-family:'Vazirmatn',Tahoma,sans-serif;background:#f1f5f9;color:#0f172a;margin:0;display:flex;min-height:100vh;align-items:center;justify-content:center}
.card{background:#fff;border-radius:1rem;box-shadow:0 20px 60px rgba(15,23,42,.15);max-width:420px;width:92%;padding:2rem;text-align:center}
.badge{display:inline-block;background:#fef3c7;color:#92400e;font-size:.75rem;font-weight:700;border-radius:9999px;padding:.2rem .9rem;margin-bottom:1rem}
h1{font-size:1.2rem;margin:0 0 .4rem}
.auth{font-size:.8rem;color:#64748b;margin-bottom:1.4rem;direction:ltr}
.row{display:flex;gap:.7rem}
button{flex:1;font-family:inherit;font-weight:800;border:0;border-radius:.7rem;padding:.8rem;cursor:pointer;font-size:.95rem}
.ok{background:#16a34a;color:#fff}
.cancel{background:#e2e8f0;color:#334155}
</style>
</head>
<body>
<div class="card">
<div class="badge">درگاه تست — پول واقعی جابه‌جا نمی‌شود</div>
<h1>پرداخت سفارش</h1>
<div class="auth">{{ $authority }}</div>
<div class="row">
<form method="get" action="{{ $callback }}" style="flex:1;display:flex">
<input type="hidden" name="Authority" value="{{ $authority }}"/>
<input type="hidden" name="Status" value="NOK"/>
<button type="submit" class="cancel">پرداخت ناموفق</button>
</form>
<form method="get" action="{{ $callback }}" style="flex:1;display:flex">
<input type="hidden" name="Authority" value="{{ $authority }}"/>
<input type="hidden" name="Status" value="OK"/>
<button type="submit" class="ok">پرداخت موفق</button>
</form>
</div>
</div>
</body>
</html>

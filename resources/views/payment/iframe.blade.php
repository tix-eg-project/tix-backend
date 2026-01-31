<!-- <!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Complete Payment</title>
  <style>
    body { font-family: Arial, sans-serif; margin:0 }
    .wrap { max-width: 900px; margin: 20px auto; padding: 10px }
    iframe { width: 100%; height: 75vh; border: 0 }
    .bar { display:flex; gap:10px; align-items:center; margin-top:12px }
    .btn { padding:10px 14px; border-radius:8px; border:1px solid #ddd; cursor:pointer }
  </style>
</head>
<body>
<div class="wrap">
  <iframe src="{{ $iframe_url }}" allowpaymentrequest></iframe>

  <div class="bar">
    <button class="btn" id="continueBtn">متابعة</button>
    <span>بعد إتمام الدفع اضغط “متابعة” أو انتظر تحويل تلقائي…</span>
  </div>
</div>

<script>
  const uuid = @json($transaction_id);
  const callbackUrl = "{{ route('xpay.callback') }}";
  const btn = document.getElementById('continueBtn');

  function goToResult() {
    if (!uuid) return;
    window.location = callbackUrl + '?transaction_uuid=' + encodeURIComponent(uuid);
  }

  btn.addEventListener('click', goToResult);

  // Poll كل 5 ثواني: أول ما السيرفر يثبّت أو حتى لو لسه، هنروح صفحة النتيجة (الـ callback نفسه بيرجع Blade)
  let tries = 0;
  const poll = setInterval(() => {
    tries++;
    fetch(callbackUrl + '?transaction_uuid=' + encodeURIComponent(uuid), { method: 'GET', credentials: 'same-origin' })
      .then(() => { goToResult(); clearInterval(poll); })
      .catch(() => { if (tries > 12) clearInterval(poll); });
  }, 5000);
</script>
</body>
</html> -->

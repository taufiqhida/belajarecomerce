<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon - Cold Storage</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            color: #fff;
            padding: 1.5rem;
        }

        .card {
            text-align: center;
            max-width: 560px;
            width: 100%;
        }

        .icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            display: block;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #10b981, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p {
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.7;
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        .countdown {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .cd-unit {
            text-align: center;
        }

        .cd-num {
            font-size: 3rem;
            font-weight: 900;
            line-height: 1;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 14px;
            padding: 1rem 1.25rem;
            min-width: 80px;
        }

        .cd-label {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-top: 0.5rem;
        }

        .cd-sep {
            font-size: 2.5rem;
            font-weight: 900;
            opacity: 0.3;
            margin-top: 0.75rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            padding: 0.5rem 1.25rem;
            border-radius: 999px;
            font-size: 0.85rem;
            color: #10b981;
        }
    </style>
</head>

<body>
    <div class="card">
        <span class="icon">⏳</span>
        <h1>Coming Soon</h1>
        <p>
            Cold Storage sedang mempersiapkan pengalaman belanja terbaik untuk Anda.
            Pantau terus untuk penawaran-penawaran menarik!
        </p>
        <div class="countdown" id="countdown">
            <div class="cd-unit">
                <div class="cd-num" id="cd-d">00</div>
                <div class="cd-label">Hari</div>
            </div>
            <div class="cd-sep">:</div>
            <div class="cd-unit">
                <div class="cd-num" id="cd-h">00</div>
                <div class="cd-label">Jam</div>
            </div>
            <div class="cd-sep">:</div>
            <div class="cd-unit">
                <div class="cd-num" id="cd-m">00</div>
                <div class="cd-label">Menit</div>
            </div>
            <div class="cd-sep">:</div>
            <div class="cd-unit">
                <div class="cd-num" id="cd-s">00</div>
                <div class="cd-label">Detik</div>
            </div>
        </div>
        <div class="badge">🛍️ Segera Hadir</div>
    </div>
    <script>
        // Tanggal launching dari admin panel
        @if(isset($setting) && $setting->launch_date)
            const launch = new Date('{{ $setting->launch_date->toISOString() }}');
        @else
            const launch = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000);
        @endif
        function tick() {
            const diff = Math.max(0, launch - Date.now());
            if (diff <= 0) {
                document.getElementById('countdown').innerHTML = '<div style="font-size:1.2rem;color:rgba(255,255,255,.6);">🎉 Segera dibuka!</div>';
                return;
            }
            const d = Math.floor(diff / 86400000);
            const h = Math.floor((diff % 86400000) / 3600000);
            const m = Math.floor((diff % 3600000) / 60000);
            const s = Math.floor((diff % 60000) / 1000);
            const pad = n => String(n).padStart(2, '0');
            document.getElementById('cd-d').textContent = pad(d);
            document.getElementById('cd-h').textContent = pad(h);
            document.getElementById('cd-m').textContent = pad(m);
            document.getElementById('cd-s').textContent = pad(s);
        }
        tick(); setInterval(tick, 1000);
    </script>
</body>

</html>

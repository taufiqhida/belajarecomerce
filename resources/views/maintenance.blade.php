<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance - Cold Storage</title>
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
            max-width: 520px;
            width: 100%;
        }

        .icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            display: block;
            animation: spin 4s linear infinite;
        }

        @keyframes spin {

            0%,
            100% {
                transform: rotate(0deg);
            }

            50% {
                transform: rotate(20deg);
            }
        }

        h1 {
            font-size: 2.25rem;
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

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 0.5rem 1.25rem;
            border-radius: 999px;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #f59e0b;
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(0.85);
            }
        }
    </style>
</head>

<body>
    <div class="card">
        <span class="icon">🔧</span>
        <h1>Sedang Maintenance</h1>
        <p>
            Kami sedang melakukan pemeliharaan sistem untuk memberikan
            pengalaman belanja yang lebih baik. Mohon kunjungi kembali dalam beberapa saat.
        </p>
        <div class="badge">
            <div class="dot"></div>
            Kami segera kembali
        </div>
    </div>
</body>

</html>

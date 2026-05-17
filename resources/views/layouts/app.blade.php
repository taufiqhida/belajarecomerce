<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $setting->store_name ?? 'Taufiq Store')</title>
    <meta name="description" content="@yield('description', $setting->store_description ?? '')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ════════════════════════════════════
           ROOT & RESET
        ════════════════════════════════════ */
        :root {
            /* === Cold Storage KUD Mino Saroyo — Ocean Blue-to-Green Theme === */
            --primary: #0e9f6e;         /* teal-green */
            --primary-d: #057a55;       /* deep green */
            --primary-l: #d1fae5;       /* mint light */
            --primary-xl: #ecfdf5;      /* mint ultra-light */
            --accent: #0284c7;          /* ocean blue */
            --accent-l: #e0f2fe;        /* sky blue light */
            --dark: #0c1a2e;            /* deep navy */
            --dark-2: #0f2744;          /* navy */
            --mid: #334e68;             /* slate blue */
            --muted: #6b8fae;           /* muted blue-gray */
            --border: #cfe2f3;          /* blue-tinted border */
            --border-2: #e8f4fd;        /* soft blue border */
            --white: #ffffff;
            --light: #f0f9ff;           /* ocean-air background */
            --red: #ef4444;
            --amber: #f59e0b;
            --wa: #25d366;
            --radius-sm: 8px;
            --radius: 14px;
            --radius-lg: 20px;
            --radius-xl: 28px;
            --shadow-xs: 0 1px 3px rgba(2, 132, 199, .08);
            --shadow-sm: 0 2px 10px rgba(2, 132, 199, .10);
            --shadow: 0 4px 20px rgba(2, 132, 199, .12);
            --shadow-lg: 0 8px 40px rgba(2, 132, 199, .15);
            --shadow-xl: 0 20px 60px rgba(0, 0, 0, .18);
            --t: .22s cubic-bezier(.4, 0, .2, 1);
        }

        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--light);
            color: var(--dark);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        img {
            display: block;
            max-width: 100%;
        }

        button {
            font-family: inherit;
        }


        /* ════════════════════════════════════
           NAVBAR
        ════════════════════════════════════ */
        .navbar {
            background: rgba(255, 255, 255, .96);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 200;
            box-shadow: 0 2px 20px rgba(0, 0, 0, .05);
        }

        .nav-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            height: 68px;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: .55rem;
            flex-shrink: 0;
        }

        .nav-brand-logo {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--accent), var(--primary));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            color: #fff;
            box-shadow: 0 4px 12px rgba(2, 132, 199, .40);
        }

        .nav-brand-text {
            font-size: 1.05rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--accent), var(--primary-d));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }

        .nav-search {
            flex: 1;
            max-width: 360px;
            position: relative;
        }

        .nav-search-input {
            width: 100%;
            height: 42px;
            padding: 0 1.1rem 0 2.8rem;
            border: 1.5px solid var(--border);
            border-radius: 999px;
            font-size: .875rem;
            font-family: inherit;
            background: var(--light);
            color: var(--dark);
            transition: var(--t);
            outline: none;
        }

        .nav-search-input:focus {
            border-color: var(--accent);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(2, 132, 199, .12);
        }

        .nav-search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: .8rem;
            pointer-events: none;
        }

        .search-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            z-index: 300;
            display: none;
            overflow: hidden;
        }

        .sd-item {
            display: flex;
            align-items: center;
            gap: .85rem;
            padding: .7rem 1rem;
            cursor: pointer;
            transition: background var(--t);
        }

        .sd-item:hover {
            background: var(--light);
        }

        .sd-item-img {
            width: 44px;
            height: 44px;
            object-fit: cover;
            border-radius: 9px;
            background: var(--light);
            flex-shrink: 0;
        }

        .sd-item-name {
            font-size: .84rem;
            font-weight: 600;
        }

        .sd-item-price {
            font-size: .76rem;
            color: var(--primary-d);
            font-weight: 500;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-left: auto;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: .4rem;
            padding: .5rem .9rem;
            border-radius: var(--radius-sm);
            font-size: .845rem;
            font-weight: 600;
            color: var(--mid);
            transition: var(--t);
            white-space: nowrap;
        }

        .nav-link:hover,
        .nav-link.active {
            background: var(--primary-l);
            color: var(--primary-d);
        }

        .nav-link i {
            font-size: .85rem;
        }

        .nav-cart-btn {
            position: relative;
            display: flex;
            align-items: center;
            gap: .4rem;
            padding: .5rem .9rem;
            border-radius: var(--radius-sm);
            font-size: .845rem;
            font-weight: 600;
            color: var(--mid);
            transition: var(--t);
            cursor: pointer;
        }

        .nav-cart-btn:hover {
            background: var(--primary-l);
            color: var(--primary-d);
        }

        .cart-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background: var(--red);
            color: #fff;
            font-size: .6rem;
            font-weight: 800;
            border-radius: 999px;
            min-width: 17px;
            height: 17px;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 0 3px;
            border: 2px solid #fff;
        }

        /* ════════════════════════════════════
           HERO BANNER
        ════════════════════════════════════ */
        .hero {
            background: linear-gradient(145deg, #0c1a2e 0%, #0f2744 40%, #084c3f 80%, #065f46 100%);
            padding: 3.5rem 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 55% 70% at 85% 40%, rgba(2, 132, 199, .22) 0%, transparent 65%),
                radial-gradient(ellipse 40% 55% at 15% 85%, rgba(14, 159, 110, .18) 0%, transparent 65%),
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Ccircle cx='30' cy='30' r='1.5' fill='rgba(255,255,255,0.04)'/%3E%3C/svg%3E");
            pointer-events: none;
        }

        /* Animated wave effect at bottom of hero */
        .hero::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 60px;
            background: var(--light);
            clip-path: ellipse(55% 100% at 50% 100%);
        }

        .hero-inner {
            max-width: 1280px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: rgba(2, 132, 199, .2);
            border: 1px solid rgba(56, 189, 248, .4);
            border-radius: 999px;
            padding: .3rem .85rem;
            font-size: .75rem;
            font-weight: 700;
            color: #38bdf8;
            letter-spacing: .05em;
            margin-bottom: 1.25rem;
        }

        .hero-title {
            font-size: clamp(1.75rem, 4vw, 2.75rem);
            font-weight: 900;
            color: #fff;
            line-height: 1.2;
            margin-bottom: .85rem;
        }

        .hero-title .highlight {
            background: linear-gradient(135deg, #34d399, #38bdf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-sub {
            font-size: .95rem;
            color: rgba(255, 255, 255, .65);
            max-width: 480px;
            margin-bottom: 2rem;
        }

        .hero-cta {
            display: flex;
            gap: .85rem;
            flex-wrap: wrap;
        }

        .hero-stats {
            display: flex;
            gap: 2.5rem;
            margin-top: 2.5rem;
            flex-wrap: wrap;
        }

        .hero-stat-num {
            font-size: 1.4rem;
            font-weight: 900;
            color: #fff;
        }

        .hero-stat-label {
            font-size: .75rem;
            color: rgba(255, 255, 255, .5);
            margin-top: .1rem;
        }

        /* ════════════════════════════════════
           SECTION WRAPPER
        ════════════════════════════════════ */
        .page-body {
            max-width: 1280px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem 4rem;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            gap: 1rem;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .section-title .pill {
            display: inline-flex;
            align-items: center;
            padding: .15rem .6rem;
            border-radius: 999px;
            font-size: .68rem;
            font-weight: 700;
            background: var(--primary-l);
            color: var(--primary-d);
        }

        .see-all {
            font-size: .82rem;
            font-weight: 600;
            color: var(--primary-d);
            display: flex;
            align-items: center;
            gap: .3rem;
            white-space: nowrap;
            transition: var(--t);
        }

        .see-all:hover {
            color: var(--accent);
        }

        /* ════════════════════════════════════
           FLASH SALE SECTION
        ════════════════════════════════════ */
        .flash-section {
            background: linear-gradient(135deg, #1f1f1f 0%, #2d1f00 50%, #3d0f0f 100%);
            border-radius: var(--radius-xl);
            padding: 1.75rem;
            margin-bottom: 2.5rem;
            position: relative;
            overflow: hidden;
        }

        .flash-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 50% 80% at 90% 50%, rgba(245, 158, 11, .15) 0%, transparent 70%);
            pointer-events: none;
        }

        .flash-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .flash-label {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-size: 1.15rem;
            font-weight: 900;
            color: #fff;
        }

        .flash-icon {
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.4rem;
        }

        .countdown-wrap {
            display: flex;
            align-items: center;
            gap: .45rem;
        }

        .cd-box {
            background: rgba(255, 255, 255, .1);
            border: 1px solid rgba(255, 255, 255, .15);
            border-radius: 8px;
            padding: .3rem .6rem;
            text-align: center;
            min-width: 44px;
        }

        .cd-num {
            font-size: 1.1rem;
            font-weight: 800;
            color: #fff;
            line-height: 1;
        }

        .cd-label {
            font-size: .58rem;
            color: rgba(255, 255, 255, .5);
            text-transform: uppercase;
            letter-spacing: .07em;
            margin-top: 2px;
        }

        .cd-sep {
            color: rgba(255, 255, 255, .5);
            font-weight: 800;
            margin-bottom: 6px;
        }

        .flash-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
        }

        .flash-card {
            background: rgba(255, 255, 255, .07);
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: var(--radius);
            overflow: hidden;
            transition: var(--t);
            cursor: pointer;
        }

        .flash-card:hover {
            background: rgba(255, 255, 255, .12);
            border-color: rgba(245, 158, 11, .4);
            transform: translateY(-3px);
        }

        .flash-card-img {
            aspect-ratio: 1;
            overflow: hidden;
            background: rgba(255, 255, 255, .05);
            position: relative;
        }

        .flash-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .4s;
        }

        .flash-card:hover .flash-card-img img {
            transform: scale(1.07);
        }

        .flash-discount-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            color: #fff;
            font-size: .62rem;
            font-weight: 800;
            padding: .2rem .5rem;
            border-radius: 999px;
        }

        .flash-card-body {
            padding: .75rem .85rem .9rem;
        }

        .flash-card-name {
            font-size: .8rem;
            font-weight: 600;
            color: rgba(255, 255, 255, .9);
            margin-bottom: .3rem;
            line-height: 1.35;
        }

        .flash-price {
            font-size: .95rem;
            font-weight: 800;
            color: #fcd34d;
        }

        .flash-original {
            font-size: .72rem;
            color: rgba(255, 255, 255, .35);
            text-decoration: line-through;
            margin-top: 1px;
        }

        /* ════════════════════════════════════
           FILTER BAR (CATEGORIES)
        ════════════════════════════════════ */
        .filter-bar {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
            margin-bottom: 1.75rem;
        }

        .filter-chip {
            padding: .42rem 1.1rem;
            border-radius: 999px;
            font-size: .82rem;
            font-weight: 600;
            cursor: pointer;
            border: 1.5px solid var(--border);
            background: var(--white);
            color: var(--mid);
            transition: var(--t);
            white-space: nowrap;
        }

        .filter-chip:hover {
            border-color: var(--primary);
            color: var(--primary-d);
            background: var(--primary-xl);
        }

        .filter-chip.active {
            border-color: var(--primary);
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 14px rgba(22, 163, 74, .3);
        }

        /* ════════════════════════════════════
           PRODUCT GRID
        ════════════════════════════════════ */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.25rem;
        }

        .product-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            overflow: hidden;
            border: 1px solid var(--border-2);
            box-shadow: var(--shadow-xs);
            transition: var(--t);
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            border-color: rgba(22, 163, 74, .25);
            box-shadow: var(--shadow-lg);
            transform: translateY(-5px);
        }

        .card-img {
            aspect-ratio: 1;
            overflow: hidden;
            background: var(--light);
            position: relative;
        }

        .card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .45s ease;
        }

        .product-card:hover .card-img img {
            transform: scale(1.07);
        }

        .card-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: .22rem .65rem;
            border-radius: 999px;
            font-size: .65rem;
            font-weight: 800;
            letter-spacing: .03em;
        }

        .card-badge.best_seller {
            background: var(--amber);
            color: #fff;
        }

        .card-badge.new {
            background: var(--accent);
            color: #fff;
        }

        .card-badge.promo {
            background: var(--red);
            color: #fff;
        }

        .card-badge.limited {
            background: var(--dark);
            color: #fff;
        }

        .card-badge.flash {
            background: linear-gradient(135deg, var(--amber), var(--red));
            color: #fff;
        }

        .card-body {
            padding: 1rem 1.1rem 1.15rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .card-category {
            font-size: .68rem;
            font-weight: 700;
            color: var(--primary-d);
            text-transform: uppercase;
            letter-spacing: .07em;
            margin-bottom: .35rem;
        }

        .card-name {
            font-size: .915rem;
            font-weight: 700;
            line-height: 1.4;
            color: var(--dark);
            margin-bottom: .5rem;
            flex: 1;
        }

        .card-price {
            font-size: 1rem;
            font-weight: 800;
            color: var(--primary-d);
        }

        .card-price .from-text {
            font-size: .7rem;
            font-weight: 400;
            color: var(--muted);
        }

        /* ════════════════════════════════════
           BUTTONS
        ════════════════════════════════════ */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            padding: .65rem 1.35rem;
            border-radius: var(--radius-sm);
            font-size: .875rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            border: none;
            transition: var(--t);
            line-height: 1;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--primary));
            color: #fff;
            box-shadow: 0 4px 14px rgba(2, 132, 199, .35);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(2, 132, 199, .50);
            filter: brightness(1.07);
        }

        .btn-outline {
            background: transparent;
            color: var(--white);
            border: 1.5px solid rgba(255, 255, 255, .3);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, .15);
            border-color: rgba(255, 255, 255, .6);
        }

        .btn-ghost {
            background: var(--white);
            color: var(--primary-d);
            border: 1.5px solid var(--primary-l);
        }

        .btn-ghost:hover {
            background: var(--primary-l);
            border-color: var(--primary);
        }

        .btn-wa {
            background: linear-gradient(135deg, #25d366, #1db954);
            color: #fff;
            box-shadow: 0 4px 14px rgba(37, 211, 102, .35);
        }

        .btn-wa:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(37, 211, 102, .5);
        }

        .btn-lg {
            padding: .85rem 1.75rem;
            font-size: .95rem;
            border-radius: 11px;
        }

        .btn-sm {
            padding: .42rem .9rem;
            font-size: .78rem;
            border-radius: 7px;
        }

        .btn-block {
            width: 100%;
        }

        .btn-danger {
            background: var(--red);
            color: #fff;
        }

        .btn-danger:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }

        .btn-gray {
            background: var(--light);
            color: var(--mid);
            border: 1.5px solid var(--border);
        }

        .btn-gray:hover {
            background: var(--border);
        }

        /* ════════════════════════════════════
           FORM CONTROLS
        ════════════════════════════════════ */
        .form-group {
            margin-bottom: 1.1rem;
        }

        .form-label {
            display: block;
            font-size: .8rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: .4rem;
        }

        .form-control {
            width: 100%;
            padding: .7rem 1rem;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: .875rem;
            font-family: inherit;
            color: var(--dark);
            background: var(--white);
            transition: var(--t);
            outline: none;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(22, 163, 74, .1);
        }

        textarea.form-control {
            min-height: 80px;
            resize: vertical;
        }

        /* ════════════════════════════════════
           TOAST
        ════════════════════════════════════ */
        .toast {
            position: fixed;
            bottom: 1.75rem;
            right: 1.75rem;
            background: var(--dark);
            color: #fff;
            padding: .8rem 1.35rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow-xl);
            z-index: 1000;
            font-size: .85rem;
            font-weight: 600;
            transform: translateY(140%);
            transition: transform .35s cubic-bezier(.34, 1.56, .64, 1);
            display: flex;
            align-items: center;
            gap: .55rem;
            max-width: 320px;
        }

        .toast.show {
            transform: translateY(0);
        }

        .toast.ok {
            border-left: 4px solid var(--primary);
        }

        .toast.err {
            border-left: 4px solid var(--red);
        }

        /* ════════════════════════════════════
           MODAL
        ════════════════════════════════════ */
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .6);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            z-index: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            opacity: 0;
            pointer-events: none;
            transition: opacity var(--t);
        }

        .overlay.open {
            opacity: 1;
            pointer-events: auto;
        }

        .modal {
            background: var(--white);
            border-radius: var(--radius-xl);
            padding: 2rem;
            max-width: 500px;
            width: 100%;
            max-height: 92vh;
            overflow-y: auto;
            transform: scale(.92) translateY(20px);
            transition: transform .3s cubic-bezier(.34, 1.56, .64, 1);
            box-shadow: var(--shadow-xl);
        }

        .overlay.open .modal {
            transform: scale(1) translateY(0);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-2);
        }

        .modal-title {
            font-size: 1.1rem;
            font-weight: 800;
        }

        .modal-close {
            background: var(--light);
            border: none;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1rem;
            color: var(--mid);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--t);
        }

        .modal-close:hover {
            background: var(--border);
            color: var(--dark);
        }

        /* ════════════════════════════════════
           CART STYLES
        ════════════════════════════════════ */
        .cart-page-wrap {
            max-width: 860px;
            margin: 0 auto;
        }

        .cart-empty {
            text-align: center;
            padding: 5rem 1rem;
            color: var(--muted);
        }

        .cart-empty-icon {
            font-size: 4.5rem;
            margin-bottom: 1.25rem;
            opacity: .25;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--white);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
        }

        .cart-table th {
            font-size: .72rem;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: .85rem 1.25rem;
            text-align: left;
            background: var(--light);
        }

        .cart-table td {
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--border-2);
            vertical-align: middle;
        }

        .cart-prod {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .cart-prod-img {
            width: 58px;
            height: 58px;
            object-fit: cover;
            border-radius: 10px;
            background: var(--light);
            flex-shrink: 0;
        }

        .cart-prod-name {
            font-size: .88rem;
            font-weight: 700;
        }

        .cart-prod-var {
            font-size: .76rem;
            color: var(--muted);
            margin-top: 2px;
        }

        .qty-ctrl {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .qty-btn {
            width: 30px;
            height: 30px;
            border: 1.5px solid var(--border);
            border-radius: 7px;
            background: var(--white);
            cursor: pointer;
            font-size: 1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--t);
            color: var(--mid);
        }

        .qty-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .qty-num {
            font-size: .88rem;
            font-weight: 800;
            min-width: 24px;
            text-align: center;
        }

        .price-cell {
            font-size: .92rem;
            font-weight: 800;
            color: var(--primary-d);
        }

        .cart-remove {
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 1rem;
            transition: var(--t);
            padding: .25rem;
            border-radius: 6px;
        }

        .cart-remove:hover {
            color: var(--red);
            background: #fee2e2;
        }

        .cart-summary-box {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 1.75rem;
            box-shadow: var(--shadow-sm);
        }

        .sum-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .55rem 0;
            font-size: .875rem;
        }

        .sum-row.total {
            font-size: 1.15rem;
            font-weight: 900;
            border-top: 2px solid var(--border-2);
            padding-top: 1rem;
            margin-top: .5rem;
        }

        .sum-row.total .sum-amt {
            color: var(--primary-d);
        }

        /* ════════════════════════════════════
           DISCOUNT INPUT
        ════════════════════════════════════ */
        .discount-row {
            display: flex;
            gap: .6rem;
            margin-bottom: 1rem;
        }

        .discount-row .form-control {
            border-radius: var(--radius-sm);
        }

        /* ════════════════════════════════════
           PAGINATION
        ════════════════════════════════════ */
        .pagination {
            display: flex;
            gap: .4rem;
            justify-content: center;
            margin-top: 2.5rem;
            flex-wrap: wrap;
        }

        .pagination a,
        .pagination span {
            padding: .45rem .85rem;
            border-radius: var(--radius-sm);
            font-size: .84rem;
            font-weight: 600;
            border: 1.5px solid var(--border);
            color: var(--mid);
            transition: var(--t);
        }

        .pagination .active span {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
            box-shadow: 0 3px 10px rgba(22, 163, 74, .3);
        }

        .pagination a:hover {
            border-color: var(--primary);
            color: var(--primary-d);
            background: var(--primary-xl);
        }

        /* ════════════════════════════════════
           EMPTY STATE
        ════════════════════════════════════ */
        .empty-state {
            text-align: center;
            padding: 5rem 1rem;
            color: var(--muted);
        }

        .empty-state-icon {
            font-size: 4rem;
            opacity: .2;
            display: block;
            margin-bottom: 1.25rem;
        }

        .empty-state h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: .5rem;
        }

        /* ════════════════════════════════════
           FOOTER
        ════════════════════════════════════ */
        .footer {
            background: var(--dark-2);
            color: rgba(255, 255, 255, .5);
            padding: 3rem 1.5rem 1.5rem;
            margin-top: 5rem;
        }

        .footer-inner {
            max-width: 1280px;
            margin: 0 auto;
        }

        .footer-top {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 2rem;
            margin-bottom: 2rem;
            align-items: start;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: .6rem;
            font-size: 1.15rem;
            font-weight: 900;
            color: #fff;
            margin-bottom: .6rem;
        }

        .footer-desc {
            font-size: .85rem;
            color: rgba(255, 255, 255, .45);
            max-width: 320px;
            line-height: 1.7;
        }

        .footer-divider {
            border: none;
            border-top: 1px solid rgba(255, 255, 255, .08);
            margin: 1.5rem 0;
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: .75rem;
            font-size: .8rem;
        }

        /* ════════════════════════════════════
           CHIP / TAG UTILITIES
        ════════════════════════════════════ */
        .tag {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .18rem .6rem;
            border-radius: 999px;
            font-size: .68rem;
            font-weight: 700;
        }

        .tag-green {
            background: var(--primary-l);
            color: var(--primary-d);
        }

        .tag-accent {
            background: var(--accent-l);
            color: var(--accent);
        }

        .tag-amber {
            background: #fef3c7;
            color: #92400e;
        }

        .tag-red {
            background: #fee2e2;
            color: var(--red);
        }

        /* ════════════════════════════════════
           RESPONSIVE
        ════════════════════════════════════ */
        @media (max-width: 768px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: .9rem;
            }

            .flash-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .footer-top {
                grid-template-columns: 1fr;
            }

            .nav-brand-text {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .nav-link .nav-link-text {
                display: none;
            }

            .hero {
                padding: 2.5rem 1rem;
            }
        }

        /* ════════════════════════════════════
           HAMBURGER / MOBILE NAV
        ════════════════════════════════════ */
        .nav-burger {
            display: none;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
            width: 38px;
            height: 38px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            border-radius: 8px;
            transition: background var(--t);
        }

        .nav-burger:hover { background: var(--primary-l); }

        .nav-burger span {
            display: block;
            height: 2.5px;
            background: var(--dark);
            border-radius: 2px;
            transition: all .3s;
        }

        .mobile-nav {
            display: none;
            flex-direction: column;
            background: rgba(255,255,255,.98);
            backdrop-filter: blur(20px);
            border-top: 1px solid var(--border);
            padding: 0.75rem 1rem 1rem;
            gap: 0.25rem;
        }

        .mobile-nav .nav-link {
            padding: .65rem .9rem;
            border-radius: var(--radius-sm);
            font-size: .9rem;
        }

        .mobile-nav .nav-cart-btn {
            padding: .65rem .9rem;
            border-radius: var(--radius-sm);
            font-size: .9rem;
            justify-content: flex-start;
        }

        @media (max-width: 768px) {
            .nav-burger { display: flex; }
            .nav-desktop { display: none !important; }
            .nav-search { max-width: 200px; }
            .mobile-nav.open { display: flex; }
        }

        @media (max-width: 480px) {
            .nav-search { max-width: 150px; }
        }

        /* Fix toast utk mobile */
        @media (max-width: 480px) {
            .toast {
                left: 1rem;
                right: 1rem;
                bottom: 1rem;
                max-width: 100%;
            }
        }
    </style>
    @yield('head')
</head>

<body>
    @php
        $setting = \App\Models\StoreSetting::current();
    @endphp

    @if($setting->is_announcement_active && $setting->announcement_text)
        <div id="promo-bar">
            <div id="promo-bar-inner">
                {{-- Badge kiri --}}
                <div id="promo-badge">
                    <span>TODAY'S</span>
                    <span>SALE</span>
                </div>

                {{-- Teks tengah --}}
                <div id="promo-text">
                    {{ $setting->announcement_text }}
                </div>

                {{-- Kanan: trust text + tombol --}}
                <div id="promo-actions">
                    @if($setting->announcement_deadline)
                        @php
                            $bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                            $dl = \Carbon\Carbon::parse($setting->announcement_deadline);
                            $tglDeadline = $dl->day . ' ' . $bulan[$dl->month] . ' ' . $dl->year;
                        @endphp
                        <span id="promo-trust">
                            <i class="fas fa-calendar-alt"></i>
                            Berlaku hingga {{ $tglDeadline }}
                        </span>
                    @endif
                    @if($setting->announcement_link)
                        <a href="{{ $setting->announcement_link }}" id="promo-btn">Ambil Promo</a>
                    @endif
                </div>
            </div>
        </div>
        <style>
            #promo-bar {
                background: linear-gradient(90deg, #0d1117 0%, #141c2b 50%, #0d1117 100%);
                border-bottom: 1px solid rgba(255, 255, 255, .07);
                padding: 0 1.5rem;
                position: relative;
                z-index: 201;
            }

            #promo-bar-inner {
                max-width: 1280px;
                margin: 0 auto;
                display: flex;
                align-items: center;
                gap: 1.25rem;
                height: 52px;
            }

            #promo-badge {
                background: linear-gradient(135deg, #e91e8c, #c2185b);
                color: #fff;
                font-size: .6rem;
                font-weight: 900;
                padding: .3rem .5rem;
                border-radius: 6px;
                text-align: center;
                text-transform: uppercase;
                letter-spacing: .04em;
                line-height: 1.25;
                flex-shrink: 0;
                box-shadow: 0 2px 10px rgba(233, 30, 140, .4);
            }

            #promo-text {
                flex: 1;
                font-size: .82rem;
                font-weight: 500;
                color: rgba(255, 255, 255, .9);
                line-height: 1.45;
                text-align: center;
            }

            #promo-actions {
                display: flex;
                align-items: center;
                gap: 1rem;
                flex-shrink: 0;
            }

            #promo-trust {
                font-size: .78rem;
                font-weight: 600;
                color: rgba(255, 255, 255, .7);
                display: flex;
                align-items: center;
                gap: .35rem;
                white-space: nowrap;
            }

            #promo-trust i {
                color: #4ade80;
                font-size: .8rem;
            }

            #promo-btn {
                background: linear-gradient(135deg, #2563eb, #1d4ed8);
                color: #fff;
                font-size: .8rem;
                font-weight: 700;
                padding: .45rem 1.15rem;
                border-radius: 8px;
                white-space: nowrap;
                transition: all .2s;
                text-decoration: none;
                box-shadow: 0 3px 12px rgba(37, 99, 235, .4);
            }

            #promo-btn:hover {
                background: linear-gradient(135deg, #1d4ed8, #1e40af);
                transform: translateY(-1px);
                box-shadow: 0 6px 18px rgba(37, 99, 235, .5);
            }

            @media (max-width: 640px) {
                #promo-trust {
                    display: none;
                }

                #promo-text {
                    font-size: .75rem;
                    text-align: left;
                }
            }
        </style>
    @endif

    @php $hasFlash = isset($flashSales) && $flashSales->count() > 0; @endphp

    {{-- FLASH BAR --}}

    {{-- NAVBAR --}}
    <nav class="navbar" id="mainNav">
        <div class="nav-inner">
            <a href="{{ route('home') }}" class="nav-brand">
                <div class="nav-brand-logo">🐟</div>
                <span class="nav-brand-text">{{ $setting->store_name ?? 'Cold Storage KUD Mino Saroyo' }}</span>
            </a>

            <div class="nav-search" id="searchWrap">
                <i class="fas fa-search nav-search-icon"></i>
                <input type="text" class="nav-search-input" id="navQ" placeholder="Cari produk..." autocomplete="off">
                <div class="search-dropdown" id="searchDrop"></div>
            </div>

            {{-- Desktop nav links --}}
            <div class="nav-actions nav-desktop">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    <i class="fas fa-store"></i>
                    <span class="nav-link-text">Produk</span>
                </a>
                <a href="{{ route('articles') }}"
                    class="nav-link {{ request()->routeIs('articles', 'article.detail') ? 'active' : '' }}">
                    <i class="fas fa-newspaper"></i>
                    <span class="nav-link-text">Artikel</span>
                </a>
                <a href="{{ route('riwayat-pesanan') }}"
                    class="nav-link {{ request()->routeIs('riwayat-pesanan') ? 'active' : '' }}">
                    <i class="fas fa-receipt"></i>
                    <span class="nav-link-text">Pesanan Saya</span>
                </a>
                <a href="{{ route('cart') }}" class="nav-cart-btn">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="nav-link-text">Keranjang</span>
                    <span class="cart-badge" id="cartBadge">0</span>
                </a>
            </div>

            {{-- Mobile: cart icon + hamburger --}}
            <div class="nav-actions" style="margin-left:auto;">
                <a href="{{ route('cart') }}" class="nav-cart-btn" style="display:none;" id="mobileCartBtn">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-badge" id="cartBadgeMobile">0</span>
                </a>
                <button class="nav-burger" id="burgerBtn" onclick="toggleMobileNav()" aria-label="Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>

        {{-- Mobile nav drawer --}}
        <div class="mobile-nav" id="mobileNav">
            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="fas fa-store"></i> Produk
            </a>
            <a href="{{ route('articles') }}"
                class="nav-link {{ request()->routeIs('articles', 'article.detail') ? 'active' : '' }}">
                <i class="fas fa-newspaper"></i> Artikel
            </a>
            <a href="{{ route('riwayat-pesanan') }}"
                class="nav-link {{ request()->routeIs('riwayat-pesanan') ? 'active' : '' }}">
                <i class="fas fa-receipt"></i> Pesanan Saya
            </a>
            <a href="{{ route('cart') }}" class="nav-cart-btn">
                <i class="fas fa-shopping-bag"></i> Keranjang
                <span class="cart-badge" id="cartBadgeMobile2" style="position:static;display:none!important;"></span>
            </a>
        </div>
    </nav>

    {{-- HERO (only on homepage) --}}
    @if(request()->routeIs('home'))
        <div class="hero">
            <div class="hero-inner">
                <div class="hero-badge">
                    <i class="fas fa-fish"></i>
                    SEGAR · HIGIENIS · TERPERCAYA
                </div>
                <h1 class="hero-title">
                    Cold Storage<br>
                    <span class="highlight">KUD Mino Saroyo</span>
                </h1>
                <p class="hero-sub">
                    Pusat penjualan ikan & hasil laut segar berkualitas tinggi. Langsung dari nelayan, disimpan dengan teknologi cold storage modern.
                </p>
                <div class="hero-cta">
                    <a href="#products" class="btn btn-primary btn-lg">
                        <i class="fas fa-fish"></i> Lihat Produk Ikan
                    </a>
                    @if(!empty($setting->whatsapp_number))
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $setting->whatsapp_number) }}" target="_blank"
                            class="btn btn-outline btn-lg">
                            <i class="fab fa-whatsapp"></i> Pesan via WhatsApp
                        </a>
                    @endif
                </div>
                @if(isset($productCount) || true)
                    <div class="hero-stats">
                        <div>
                            <div class="hero-stat-num">🐟</div>
                            <div class="hero-stat-label">Ikan Segar</div>
                        </div>
                        <div>
                            <div class="hero-stat-num">❄️</div>
                            <div class="hero-stat-label">Cold Storage</div>
                        </div>
                        <div>
                            <div class="hero-stat-num">🚢</div>
                            <div class="hero-stat-label">Langsung Nelayan</div>
                        </div>
                        <div>
                            <div class="hero-stat-num">100%</div>
                            <div class="hero-stat-label">Halal & Higienis</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- PAGE CONTENT --}}
    <div class="page-body" id="products">
        @yield('content')
    </div>

    {{-- FOOTER --}}
    <footer class="footer">
        <div class="footer-inner">
            <div class="footer-top">
                <div>
                    <div class="footer-brand">
                        <div class="nav-brand-logo" style="box-shadow:none;">🐟</div>
                        {{ $setting->store_name ?? 'Cold Storage KUD Mino Saroyo' }}
                    </div>
                    <p class="footer-desc">
                        {{ $setting->store_description ?? 'Belanja mudah, harga terbaik. Nikmati pengalaman berbelanja online yang menyenangkan.' }}
                    </p>
                </div>
                @if(!empty($setting->whatsapp_number))
                    <div>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $setting->whatsapp_number) }}"
                            class="btn btn-wa" target="_blank">
                            <i class="fab fa-whatsapp"></i> Hubungi Kami
                        </a>
                    </div>
                @endif
            </div>
            <hr class="footer-divider">
            <div class="footer-bottom">
                <span>© {{ date('Y') }} {{ $setting->store_name ?? 'Taufiq Store' }}. Hak cipta dilindungi.</span>
                <span>Dibuat dengan ❤️</span>
            </div>
        </div>
    </footer>

    {{-- TOAST --}}
    <div class="toast" id="toast"></div>

    <script>
        // ═══ CART ═══
        const CART_KEY = 'ts_cart_v2';
        function getCart() { try { return JSON.parse(localStorage.getItem(CART_KEY)) || []; } catch { return []; } }
        function saveCart(c) { localStorage.setItem(CART_KEY, JSON.stringify(c)); updateBadge(); }
        function updateBadge() {
            const n = getCart().reduce((s, i) => s + (i.qty || 1), 0);
            ['cartBadge', 'cartBadgeMobile'].forEach(id => {
                const b = document.getElementById(id);
                if (b) { b.textContent = n; b.style.display = n > 0 ? 'flex' : 'none'; }
            });
        }

        // ═══ MOBILE NAV ═══
        function toggleMobileNav() {
            const nav = document.getElementById('mobileNav');
            const btn = document.getElementById('burgerBtn');
            if (nav) nav.classList.toggle('open');
            if (btn) btn.classList.toggle('open');
        }
        // Show mobile cart btn on small screens
        function checkMobileCartBtn() {
            const btn = document.getElementById('mobileCartBtn');
            if (btn) btn.style.display = window.innerWidth <= 768 ? 'flex' : 'none';
        }
        window.addEventListener('resize', checkMobileCartBtn);
        document.addEventListener('DOMContentLoaded', checkMobileCartBtn);

        function addToCart(item) {
            const cart = getCart();
            const key = item.id + (item.variantId ? '_' + item.variantId : '');
            const ex = cart.find(c => (c.id + (c.variantId ? '_' + c.variantId : '')) === key);
            if (ex) { ex.qty = (ex.qty || 1) + 1; }
            else { cart.push({ ...item, qty: 1 }); }
            saveCart(cart);
        }
        function removeFromCart(key) { saveCart(getCart().filter(c => (c.id + (c.variantId ? '_' + c.variantId : '')) !== key)); }
        function clearCart() { localStorage.removeItem(CART_KEY); updateBadge(); }

        // ═══ TOAST ═══
        function toast(msg, type = 'ok') {
            const t = document.getElementById('toast');
            t.className = `toast ${type}`;
            t.innerHTML = (type === 'ok' ? '<i class="fas fa-check-circle" style="color:#4ade80"></i> ' : '<i class="fas fa-times-circle" style="color:#f87171"></i> ') + msg;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3500);
        }

        // ═══ SEARCH ═══
        const qInput = document.getElementById('navQ');
        const drop = document.getElementById('searchDrop');
        let searchTimer;
        if (qInput) {
            qInput.addEventListener('input', function () {
                clearTimeout(searchTimer);
                if (this.value.trim().length < 2) { drop.style.display = 'none'; return; }
                searchTimer = setTimeout(async () => {
                    try {
                        const r = await fetch('/api/products/search?q=' + encodeURIComponent(this.value));
                        const data = await r.json();
                        if (!data.length) { drop.style.display = 'none'; return; }
                        drop.innerHTML = data.map(p =>
                            `<a href="/produk/${p.slug}" class="sd-item">
                                ${p.image
                                ? `<img src="${p.image}" alt="" class="sd-item-img">`
                                : `<div class="sd-item-img" style="background:var(--light);display:flex;align-items:center;justify-content:center;font-size:1.4rem;">🛍️</div>`
                            }
                                <div>
                                    <div class="sd-item-name">${p.name}</div>
                                    <div class="sd-item-price">Rp ${Number(p.base_price).toLocaleString('id')}</div>
                                </div>
                            </a>`
                        ).join('');
                        drop.style.display = 'block';
                    } catch (e) { drop.style.display = 'none'; }
                }, 320);
            });
            document.addEventListener('click', e => {
                if (!document.getElementById('searchWrap').contains(e.target)) drop.style.display = 'none';
            });
        }

        document.addEventListener('DOMContentLoaded', updateBadge);
    </script>
    @yield('scripts')
</body>

</html>
@extends('layouts.app')

@section('title', 'Artikel - ' . ($setting->store_name ?? 'Cold Storage'))

@section('head')
    <style>
        .articles-header {
            margin-bottom: 2rem;
        }

        .articles-header h1 {
            font-size: 1.75rem;
            font-weight: 900;
            margin-bottom: 0.35rem;
        }

        .articles-header p {
            color: var(--muted);
            font-size: 0.9rem;
        }

        .articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .article-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            overflow: hidden;
            border: 1px solid var(--border-2);
            box-shadow: var(--shadow-xs);
            transition: all 0.25s;
            display: flex;
            flex-direction: column;
        }

        .article-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-5px);
            border-color: rgba(22, 163, 74, .2);
        }

        .article-card-img {
            aspect-ratio: 16/9;
            overflow: hidden;
            background: var(--light);
            position: relative;
        }

        .article-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .article-card:hover .article-card-img img {
            transform: scale(1.06);
        }

        .article-card-img .no-thumb {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            opacity: 0.15;
        }

        .article-card-body {
            padding: 1.25rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .article-card-date {
            font-size: 0.72rem;
            color: var(--muted);
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .article-card-title {
            font-size: 1.05rem;
            font-weight: 800;
            line-height: 1.4;
            margin-bottom: 0.5rem;
            color: var(--dark);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .article-card-excerpt {
            font-size: 0.84rem;
            color: var(--mid);
            line-height: 1.65;
            flex: 1;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .article-card-link {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--primary-d);
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            transition: all 0.2s;
        }

        .article-card-link:hover {
            gap: 0.5rem;
            color: var(--accent);
        }

        @media (max-width: 480px) {
            .articles-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <div class="articles-header">
        <h1>📰 Artikel & Blog</h1>
        <p>Tips, info, dan berita terbaru untuk kamu</p>
    </div>

    @if($articles->count() > 0)
        <div class="articles-grid">
            @foreach($articles as $article)
                <a href="{{ route('article.detail', $article->slug) }}" class="article-card">
                    <div class="article-card-img">
                        @if($article->thumbnail)
                            <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="{{ $article->title }}">
                        @else
                            <div class="no-thumb">📄</div>
                        @endif
                    </div>
                    <div class="article-card-body">
                        <div class="article-card-date">
                            <i class="fas fa-calendar-alt"></i>
                            {{ $article->published_at->format('d M Y') }}
                        </div>
                        <div class="article-card-title">{{ $article->title }}</div>
                        @if($article->excerpt)
                            <div class="article-card-excerpt">{{ $article->excerpt }}</div>
                        @endif
                        <div class="article-card-link">
                            Baca Selengkapnya <i class="fas fa-arrow-right" style="font-size:0.7rem;"></i>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{ $articles->links('pagination.default') }}
    @else
        <div class="empty-state">
            <span class="empty-state-icon">📝</span>
            <h3>Belum Ada Artikel</h3>
            <p>Artikel akan segera hadir. Nantikan ya!</p>
        </div>
    @endif
@endsection

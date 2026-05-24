@extends('layouts.app')

@section('title', $article->title . ' - ' . ($setting->store_name ?? 'Cold Storage'))
@section('description', $article->excerpt ?? '')

@section('head')
    <style>
        .article-detail-wrap {
            max-width: 780px;
            margin: 0 auto;
        }

        .article-back {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            color: var(--mid);
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            transition: all 0.2s;
        }

        .article-back:hover {
            color: var(--primary-d);
        }

        .article-hero-img {
            width: 100%;
            aspect-ratio: 16/9;
            border-radius: var(--radius-lg);
            overflow: hidden;
            margin-bottom: 2rem;
            background: var(--light);
        }

        .article-hero-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .article-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .article-meta-item {
            font-size: 0.78rem;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 0.3rem;
            font-weight: 600;
        }

        .article-title {
            font-size: clamp(1.5rem, 4vw, 2.25rem);
            font-weight: 900;
            line-height: 1.25;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .article-excerpt {
            font-size: 1.05rem;
            color: var(--mid);
            line-height: 1.7;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1.5px solid var(--border);
            font-weight: 500;
        }

        .article-content {
            font-size: 0.95rem;
            line-height: 1.85;
            color: var(--dark-2);
        }

        .article-content h2 {
            font-size: 1.4rem;
            font-weight: 800;
            margin: 2rem 0 0.75rem;
            color: var(--dark);
        }

        .article-content h3 {
            font-size: 1.15rem;
            font-weight: 700;
            margin: 1.5rem 0 0.5rem;
            color: var(--dark);
        }

        .article-content p {
            margin-bottom: 1.25rem;
        }

        .article-content img {
            max-width: 100%;
            border-radius: var(--radius);
            margin: 1.5rem 0;
        }

        .article-content ul,
        .article-content ol {
            padding-left: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .article-content li {
            margin-bottom: 0.35rem;
        }

        .article-content blockquote {
            border-left: 4px solid var(--primary);
            padding: 0.75rem 1.25rem;
            margin: 1.5rem 0;
            background: var(--primary-xl);
            border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
            color: var(--dark-2);
            font-style: italic;
        }

        .article-content a {
            color: var(--primary-d);
            text-decoration: underline;
            font-weight: 600;
        }

        .article-content a:hover {
            color: var(--accent);
        }

        .article-content pre {
            background: var(--dark);
            color: #e2e8f0;
            padding: 1.25rem;
            border-radius: var(--radius);
            overflow-x: auto;
            margin: 1.25rem 0;
            font-size: 0.85rem;
            line-height: 1.6;
        }

        .article-content code {
            background: var(--light);
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }

        .article-content pre code {
            background: none;
            padding: 0;
        }

        /* Share */
        .article-share {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 1.5px solid var(--border);
        }

        .article-share-label {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--mid);
        }

        .share-btn {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            color: white;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
        }

        .share-btn:hover {
            transform: scale(1.1);
        }

        .share-btn.wa { background: #25d366; }
        .share-btn.fb { background: #1877f2; }
        .share-btn.tw { background: #1da1f2; }
        .share-btn.cp { background: var(--mid); }

        /* Related */
        .related-section {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1.5px solid var(--border);
        }

        .related-title {
            font-size: 1.15rem;
            font-weight: 800;
            margin-bottom: 1.25rem;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1.25rem;
        }

        .related-card {
            display: flex;
            gap: 0.85rem;
            background: var(--white);
            border: 1px solid var(--border-2);
            border-radius: var(--radius);
            padding: 0.85rem;
            transition: all 0.2s;
        }

        .related-card:hover {
            box-shadow: var(--shadow-sm);
            border-color: rgba(22, 163, 74, .2);
        }

        .related-card-img {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            overflow: hidden;
            background: var(--light);
            flex-shrink: 0;
        }

        .related-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .related-card-info {
            flex: 1;
            min-width: 0;
        }

        .related-card-title {
            font-size: 0.85rem;
            font-weight: 700;
            line-height: 1.35;
            margin-bottom: 0.3rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .related-card-date {
            font-size: 0.7rem;
            color: var(--muted);
        }
    </style>
@endsection

@section('content')
    <div class="article-detail-wrap">
        <a href="{{ route('articles') }}" class="article-back">
            <i class="fas fa-arrow-left" style="font-size:0.75rem;"></i> Kembali ke Artikel
        </a>

        @if($article->thumbnail)
            <div class="article-hero-img">
                <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="{{ $article->title }}">
            </div>
        @endif

        <div class="article-meta">
            <div class="article-meta-item">
                <i class="fas fa-calendar-alt"></i>
                {{ $article->published_at->format('d M Y') }}
            </div>
            <div class="article-meta-item">
                <i class="fas fa-clock"></i>
                {{ ceil(str_word_count(strip_tags($article->content)) / 200) }} menit baca
            </div>
        </div>

        <h1 class="article-title">{{ $article->title }}</h1>

        @if($article->excerpt)
            <div class="article-excerpt">{{ $article->excerpt }}</div>
        @endif

        <div class="article-content">
            {!! $article->content !!}
        </div>

        {{-- Share Buttons --}}
        <div class="article-share">
            <span class="article-share-label">Bagikan:</span>
            <button class="share-btn wa" onclick="window.open('https://wa.me/?text={{ urlencode($article->title . ' - ' . route('article.detail', $article->slug)) }}', '_blank')" title="WhatsApp">
                <i class="fab fa-whatsapp"></i>
            </button>
            <button class="share-btn fb" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('article.detail', $article->slug)) }}', '_blank')" title="Facebook">
                <i class="fab fa-facebook-f"></i>
            </button>
            <button class="share-btn tw" onclick="window.open('https://twitter.com/intent/tweet?url={{ urlencode(route('article.detail', $article->slug)) }}&text={{ urlencode($article->title) }}', '_blank')" title="Twitter">
                <i class="fab fa-twitter"></i>
            </button>
            <button class="share-btn cp" onclick="navigator.clipboard.writeText('{{ route('article.detail', $article->slug) }}');this.innerHTML='<i class=\'fas fa-check\'></i>';setTimeout(()=>this.innerHTML='<i class=\'fas fa-link\'></i>',2000)" title="Copy Link">
                <i class="fas fa-link"></i>
            </button>
        </div>

        {{-- Related Articles --}}
        @if($relatedArticles->count() > 0)
            <div class="related-section">
                <div class="related-title">📚 Artikel Lainnya</div>
                <div class="related-grid">
                    @foreach($relatedArticles as $rel)
                        <a href="{{ route('article.detail', $rel->slug) }}" class="related-card">
                            <div class="related-card-img">
                                @if($rel->thumbnail)
                                    <img src="{{ asset('storage/' . $rel->thumbnail) }}" alt="{{ $rel->title }}">
                                @else
                                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;opacity:.15;background:var(--light);">📄</div>
                                @endif
                            </div>
                            <div class="related-card-info">
                                <div class="related-card-title">{{ $rel->title }}</div>
                                <div class="related-card-date">{{ $rel->published_at->format('d M Y') }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection


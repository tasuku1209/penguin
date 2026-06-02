<h1>{{ $post->title }}</h1>
<p>{{ $post->user->name }}</p>
<p>{{ $post->body }}</p>
<p>いいね数: {{ $post->likes_count }}</p>

<h3>タグ</h3>
@foreach ($post->tags as $tag)
    <span>{{ $tag->name }}</span>
@endforeach

<h3>コメント</h3>
@foreach ($post->comments as $comment)
    <p>{{ $comment->body }}</p>
@endforeach

    <a href="{{ route('posts.index') }}">
        投稿一覧へ
    </a>
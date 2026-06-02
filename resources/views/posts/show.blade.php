<h1>{{ $post->title }}</h1>
<p>{{ $post->user->name }}</p>
<p>{{ $post->body }}</p>
<p>いいね数: {{ $post->likes_count }}</p>

<h3>タグ</h3>
@foreach ($post->tags as $tag)
    <span>{{ $tag->name }}</span>
@endforeach

<form action="{{ route('comments.store', $post) }}" method="POST">
    @csrf
    <textarea name="body" placeholder="コメントを入力">{{ old('body') }}</textarea>
    @error('body')
        <p>{{ $message }}</p>
    @enderror
    <button type="submit">コメントする</button>
</form>

<h3>コメント</h3>
@forelse ($post->comments as $comment)
    <p>{{ $comment->body }}</p>
    <p>by {{ $comment->user->name }}</p>
@empty
    <p>コメントはまだありません。</p>
@endforelse

    <a href="{{ route('posts.index') }}">
        投稿一覧へ
    </a>
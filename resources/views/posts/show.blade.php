<h1>{{ $post->title }}</h1>
<p>{{ $post->user->name }}</p>
<p>{{ $post->body }}</p>
@can('update', $post)
    <a href="{{ route('posts.edit', $post) }}">編集</a>
@endcan
@can('delete', $post) 
    <form method="POST" action="{{ route('posts.destroy', $post) }}">
        @csrf
        @method('DELETE')
        <button type="submit">削除</button>
    </form>
@endcan
<p>いいね数: {{ $post->likes_count }}</p>

<h3>タグ</h3>
@foreach ($post->tags as $tag)
    <span>{{ $tag->name }}</span>
@endforeach

@auth
    <form action="{{ route('likes.store', $post) }}" method="POST">
        @csrf
        <button type="submit">いいね</button>
    </form>

    <form action="{{ route('comments.store', $post) }}" method="POST">
        @csrf
        <textarea name="body" placeholder="コメントを入力">{{ old('body') }}</textarea>
        @error('body')
            <p>{{ $message }}</p>
        @enderror
        <button type="submit">コメントする</button>
    </form>
@endauth

<h3>コメント</h3>
@forelse ($post->comments as $comment)
    <p>{{ $comment->body }}</p>
    <p>by {{ $comment->user->name }}</p>
    @can('delete', $comment) 
        <form method="POST" action="{{ route('comments.destroy', [$post, $comment]) }}">
            @csrf
            @method('DELETE')
            <button type="submit">削除</button>
        </form>
    @endcan
@empty
    <p>コメントはまだありません。</p>
@endforelse

    <a href="{{ route('posts.index') }}">
        投稿一覧へ
    </a>
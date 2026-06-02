<h1>投稿一覧</h1>

<a href="{{ route('posts.create') }}">新規投稿</a>

@foreach ($posts as $post)
    <h2>{{ $post->title }}</h2>
    <p>{{ $post->user->name }}</p>
    <p>コメント数: {{ $post->comments_count }}</p>
    <p>いいね数: {{ $post->likes_count }}</p>
    <a href="{{ route('posts.show', $post) }}">詳細</a>
@endforeach

{{ $posts->links() }}
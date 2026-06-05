@auth
    <p>ログイン中: {{ auth()->user()->name }} さん</p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">ログアウト</button>
    </form>
@endauth

@guest
    <a href="{{ route('login') }}">ログイン</a>
    <a href="{{ route('register') }}">会員登録</a>
@endguest

<h1>投稿一覧</h1>

<form method="GET" action="{{ route('posts.index') }}">
    <input
        type="text"
        name="keyword"
        value="{{ request('keyword') }}"
        placeholder="キーワードを入力"
    >

    <select name="sort">
        <option value="">並び順を選択</option>
        <option value="latest"{{ request('sort') === "latest" ? ' selected' : '' }}>新着順</option>
        <option value="oldest"{{ request('sort') === "oldest" ? ' selected' : '' }}>古い順</option>
        <option value="likes"{{ request('sort') === "likes" ? ' selected' : '' }}>いいね順</option>
        <option value="comments"{{ request('sort') === "comments" ? ' selected' : '' }}>コメント順</option>
    </select>

    <button type="submit">適用</button>
</form>

    <p>
        表示投稿 {{ $posts->total() }}件
    </p>

@if (request()->filled('keyword') || request()->filled('sort'))
    <a href="{{ route('posts.index') }}">
        一覧へ戻る
    </a>
@endif

@auth
    <a href="{{ route('posts.create') }}">新規投稿</a>
@endauth

@foreach ($posts as $post)
    <h2>{{ $post->title }}</h2>
    <p>{{ $post->user->name }}</p>
    <p>
        タグ:
        @foreach ($post->tags as $tag)
            <span class="tag">{{ $tag->name }}</span>
        @endforeach
    </p>
    <p>コメント数: {{ $post->comments_count }}</p>
    <p>いいね数: {{ $post->likes_count }}</p>
    <a href="{{ route('posts.show', $post) }}">詳細</a>
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
@endforeach

{{ $posts->links() }}
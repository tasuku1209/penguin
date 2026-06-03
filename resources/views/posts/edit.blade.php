<h1>投稿編集</h1>

<form method="POST" action="{{ route('posts.update', $post) }}">
    @csrf
    @method('PUT')

    <div>
        <input
            type="text"
            name="title"
            value="{{ old('title', $post->title) }}"
        >

        @error('title')
            <p>{{ $message }}</p>
        @enderror
    </div>

    <div>
        <textarea name="body">{{ old('body', $post->body) }}</textarea>

        @error('body')
            <p>{{ $message }}</p>
        @enderror
    </div>

    <button type="submit">更新</button>

    <a href="{{ route('posts.index') }}">
        キャンセル
    </a>
</form>
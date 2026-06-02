<h1>新規投稿</h1>

<form method="POST" action="{{ route('posts.store') }}">
    @csrf

    <div>
        <input
            type="text"
            name="title"
            value="{{ old('title') }}"
        >

        @error('title')
            <p>{{ $message }}</p>
        @enderror
    </div>

    <div>
        <textarea name="body">{{ old('body') }}</textarea>

        @error('body')
            <p>{{ $message }}</p>
        @enderror
    </div>

    <button type="submit">投稿</button>

    <a href="{{ route('posts.index') }}">
        キャンセル
    </a>
</form>
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

    <div>
        <h3>タグ</h3>
        @foreach ($tags as $tag)
            <label>
                <input
                    type="checkbox"
                    name="tags[]"
                    value="{{ $tag->id }}"
                    {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}
                >
                {{ $tag->name }}
            </label>
        @endforeach
    </div>

    <div>
    <button type="submit">投稿</button>
    </div>

    <a href="{{ route('posts.index') }}">
        キャンセル
    </a>
</form>
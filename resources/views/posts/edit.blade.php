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

    <div>
        <h3>タグ</h3>
        @foreach ($tags as $tag)
            <label>
                <input
                    type="checkbox"
                    name="tags[]"
                    value="{{ $tag->id }}"
                    {{ in_array($tag->id, old('tags', $post->tags->pluck('id')->toArray())) ? 'checked' : '' }}
                >
                {{ $tag->name }}
            </label>
        @endforeach
    </div>

    <button type="submit">更新</button>

    <a href="{{ route('posts.index') }}">
        キャンセル
    </a>
</form>
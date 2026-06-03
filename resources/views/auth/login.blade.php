<h1>ログイン</h1>

<form method="POST" action="/login">
    @csrf

    <input type="email" name="email">

    <input type="password" name="password">

    <button type="submit">
        ログイン
    </button>
</form>

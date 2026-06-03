<h1>会員登録</h1>

<form method="POST" action="/register">
    @csrf

    <input type="text" name="name">

    <input type="email" name="email">

    <input type="password" name="password">

    <input type="password" name="password_confirmation">

    <button type="submit">
        登録
    </button>
</form>

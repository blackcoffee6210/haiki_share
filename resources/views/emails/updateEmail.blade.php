{{ $params['name'] }}さん<br>
平素よりhaiki_share(ハイキシェア)をご利用いただき、ありがとうございます。<br>
Eメールアドレスが変更されました。<br>
<br>
変更前のEメールアドレス: {{ $params['old_email'] }}<br>
変更後のEメールアドレス: {{ $params['new_email'] }}<br>
<br>
<a href="http://localhost:8000/users/{{ $params['user_id'] }}/my-page">
    マイページを表示する
</a>
{{--todo: URLを本番環境に変更する--}}

{{ $params['sender_name'] }} さん<br>
平素よりhaiki_share(ハイキシェア)をご利用いただき、ありがとうございます。<br>
投稿したレビューの詳細は以下のとおりです。<br>
<br>
出品者: {{ $params['receiver_name'] }}<br>
ユーザーの評価: {{ $params['recommendation'] }}<br>
レビュータイトル: {{ $params['title'] }}<br>
レビューの内容: {{ $params['detail'] }}<br>
投稿日時: {{ $params['reviewed_at'] }}<br>
<br>
<a href="http://127.0.0.1:8000/users/{{ $params['receiver_id'] }}">
    出品者のプロフィールを表示する
</a>

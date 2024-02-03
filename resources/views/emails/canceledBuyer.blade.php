{{ $params['user_name'] }}さん<br>
平素よりhaiki_share(ハイキシェア)をご利用いただき、ありがとうございます。<br>
キャンセルした商品の詳細は以下の通りです。<br>
<br>
キャンセル日時: {{ $params['canceled_at'] }}<br>
商品名: {{ $params['product_name'] }}<br>
内容: {{ $params['detail'] }}<br>
金額: {{ $params['price'] }}円<br>
賞味期限: {{ $params['expire'] }}<br>
<br>
<a href="http://127.0.0.1:8000/products/{{ $params['product_id'] }}">
    キャンセルした商品を表示する
</a>

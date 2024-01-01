{{ $params['user_name'] }} さん<br>
haiki_share(ハイキシェア)をご利用いただき、ありがとうございます。<br>
ご購入の詳細は以下のとおりです。<br>
<br>
商品名: {{ $params['product_name'] }}<br>
内容: {{ $params['detail'] }}<br>
金額: {{ $params['price'] }}円<br>
賞味期限: {{ $params['expire'] }}<br>
購入日時: {{ $params['purchased_at'] }}<br>
<br>
<a href="http://127.0.0.1:8001/products/{{ $params['product_id'] }}">
    商品を表示する
</a>

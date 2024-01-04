{{ $params['shop_name'] }}さん<br>
商品がキャンセルされました。<br>
<br>
ユーザー名: {{ $params['user_name'] }}<br>
キャンセル日時: {{ $params['canceled_at'] }}<br>
商品名: {{ $params['product_name'] }}<br>
内容: {{ $params['detail'] }}<br>
金額: {{ $params['price'] }}円<br>
賞味期限: {{ $params['expire'] }}<br>
<br>
<a href="http://127.0.0.1:8001/products/{{ $params['product_id'] }}">
    キャンセルされた商品を表示する
</a>

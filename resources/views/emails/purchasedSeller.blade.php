{{ $params['shop_name'] }}さん<br>
商品が購入されました！<br>
<br>
商品名: {{ $params['product_name'] }}<br>
内容: {{ $params['detail'] }}<br>
金額: {{ $params['price'] }}円<br>
購入日時: {{ $params['purchased_at'] }}<br>
<br>
<a href="http://127.0.0.1:8001/products/{{ $params['product_id'] }}">
    商品を表示する
</a>

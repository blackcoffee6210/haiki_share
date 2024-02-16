{{ $params['shop_name'] }}さん<br>
商品が購入されました！<br>
商品を用意してお待ちください。<br>
<br>
ユーザー名: {{ $params['user_name'] }}<br>
商品名: {{ $params['product_name'] }}<br>
内容: {{ $params['detail'] }}<br>
金額: {{ $params['price'] }}円<br>
購入日時: {{ $params['purchased_at'] }}<br>
<br>
<a href="https://haiki-share.net/products/{{ $params['product_id'] }}">
    購入された商品を表示する
</a>

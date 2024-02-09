<!doctype html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
	      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<!-- Scripts -->
	<script src="{{ asset('js/app.js') }}" defer></script>

    <!-- favicon -->
    <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}">




	<!-- Fonts -->
	<link rel="dns-prefetch" href="//fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

	<!-- Styles -->
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<title>Haiki Share</title>
    <style>
        [v-cloak] {
            display: none;
        }
    </style>
</head>
<body>
	<div id="app"></div>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>TASTACK | HINTORUのタスク管理</title>

<!-- OGP -->
<meta property="og:url" content="https://tastack.hintoru.com/">
<meta property="og:type" content="article">
<meta property="og:title" content="HINTORUのタスク管理">
<meta property="og:description" content="HINTORUのタスク管理。やるべきことが明確になり、毎日の生活や業務が驚くほど整う。">
<meta property="og:site_name" content="HINTORUのタスク管理">
<meta property="og:image" content="../images/ogpimage.png">

<!-- ICON -->
<link rel="icon" href="{{ asset('/images/favicon.ico') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/images/apple-touch-icon.png') }}">

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+JP:wght@400;500;600;700&display=swap&family=Roboto:wght@900&display=swap&family=Murecho:wght@500;900&display=swap" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="{{ asset('/js/global.js') }}"></script>

</head>
<body class="">
<div id="Wrapper">
<div class="Container">
    
    <!-- Page Content -->
    <main id="main">
    {{ $slot }}
    </main>
    
</div>
</div>
</body>
</html>

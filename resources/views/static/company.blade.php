<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>運営会社 | TASTACK</title>

<!-- OGP -->
<meta property="og:url" content="https://tastack.hintoru.com/company/">
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
<body class="static">
<div id="Wrapper">
<div class="Container">

<main id="main">
<div class="inner">

<div class="head">
<p class="copy">タスクをつめこみタスクをこなせ</p>
<p class="sitename">TASTACK</p>
<p class="pagetitle">運営会社</p>
</div>

<!-- ###### 運営会社 -->
<div class="modal" id="companyinfo_modal"><div class="inner"><div class="modal_block">
<div class="modalcontents mcs-area-dark">
<div class="about">
<div class="outline">
<p><strong>社名</strong><span>株式会社NEIGE</span></p>
<p><strong>本社</strong><span>東京都中野区東中野1-1-2 プライマリービル3F</span></p>
<p><strong>設立</strong><span>2010年1月</span></p>
<p><strong>代表者</strong><span>遠山 野歩人</span></p>
<p><strong>事業内容</strong><span>ウェブコンテンツを中心とした広告ツール全般の企画・制作・開発、ウェブシステム開発、アプリ開発、ドローン事業、ECサイト運営</span></p>
</div>
</div>
</div>
</div></div><div class="modalclose"></div></div>

</div>
</main>

<div class="footer"><p><a href="/">ホームに戻る</a></p></div>
    
</div>
</div>
</body>
</html>
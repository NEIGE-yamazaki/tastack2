<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
<body class="home">
<div id="Wrapper">
<div class="Container">

<main id="main">
<div class="inner">

<div class="servicelogo">
<p class="copy">タスクをつめこみタスクをこなせ</p>
<p class="sitename">TASTACK</p>
</div>

<div class="loginset"><p><a href="/register">会員登録</a></p><p><a href="/login">ログイン</a></p></div>

<div class="about">
  <p>TASTACKは、個人および法人、チーム向けのToDo＆共有タスク管理ツールです。</p>
  <p>個人の予定管理や、家族・チーム・企業内のタスク共有を簡単に行うことが可能です。</p>
  <h2>主な機能</h2>
  <ul>
    <li><strong>カテゴリごとのタスク作成</strong><span>AIアドバイザー機能を使用することでタスクに対してのアドバイスが自動生成されます。</span></li>
    <li><strong>タスク完了チェックとメモ機能</strong><span>完了したタスクは一覧よりワンタップで簡単に完了へ切り替えが可能です。</span></li>
    <li><strong>Googleカレンダー連携によるタスク登録</strong><span>タスクごとにGoogleカレンダー連携機能を使用しGoogleカレンダーへの登録が可能です。</span></li>
    <li><strong>カテゴリごとのタスク共有URL発行＆タスク共有</strong><span>作成したカテゴリは別のTASTACKユーザーへ共有が可能です。</span></li>
  </ul>
  <h2>Googleカレンダーとの連携について</h2>
  <p>TASTACKでは、タスクをGoogleカレンダーに自動登録する機能を提供しています。<br>この機能を利用するため、GoogleカレンダーAPIへのアクセス許可が必要です。</p>

  <!-- 審査対策として付記 -->
  <p class="notes">
  <span>※本アプリでは以下のスコープを使用しています。</span><br>
  <code>https://www.googleapis.com/auth/calendar.events</code>
  </p>
</div>

</div>
</main>

<div class="footer"><p><a href="/company/">運営会社</a></p><p><a href="/termsofservice/">利用規約</a></p><p><a href="/privacypolicy/">プライバシーポリシー</a></p></div>
    
</div>
</div>
</body>
</html>
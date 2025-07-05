
//--------------------common set--------------------

window.addEventListener('popstate', function () {
    location.reload();
});

let lastTouchEnd = 0;
document.addEventListener('touchend', function (event) {
  const now = new Date().getTime();
  if (now - lastTouchEnd <= 300) {
    event.preventDefault(); // ダブルタップによるズームをキャンセル
  }
  lastTouchEnd = now;
}, false);

function setRealVh() {
  const vh = window.innerHeight * 0.01;
  document.documentElement.style.setProperty('--vh', `${vh}px`);
}

// 初回 & リサイズ時に実行
window.addEventListener('resize', setRealVh);
window.addEventListener('orientationchange', setRealVh);
setRealVh();

//--------------------nav set--------------------

$(function () {
  const $nav = $('#header nav');
  const $overlay = $('#nav_overlay');
  let isTransitioning = false;

  function openNav() {
    if (isTransitioning || $nav.hasClass('open')) return;
    isTransitioning = true;

    // オーバーレイ表示
    $overlay.css({ display: 'block', opacity: 0 });
    requestAnimationFrame(() => {
      $overlay.css('opacity', 1);
    });

    // nav表示（まず display だけ）
    $nav.css({ display: 'block' }).removeClass('nav-leave');

    // ★ 次フレームでクラス追加（確実にtransitionさせる）
    requestAnimationFrame(() => {
      $nav.addClass('nav-enter open');
    });

    setTimeout(() => {
      isTransitioning = false;
    }, 850);
  }

  function closeNav() {
    if (isTransitioning || !$nav.hasClass('open')) return;
    isTransitioning = true;

    $overlay.css('opacity', 0);
    $nav.removeClass('nav-enter open').addClass('nav-leave');

    setTimeout(() => {
      $overlay.css('display', 'none');
      $nav.css('display', 'none').removeClass('nav-leave');
      isTransitioning = false;
    }, 850);
  }

  $('#header .navline').on('click', openNav);
  $('#header nav .close, #header nav .link a, #nav_overlay').on('click', closeNav);
});

//--------------------video set--------------------

$(function () {
  
  $('.modalclose, .close').on('click',function(){
    $('div.modal video').each(function () {
      this.pause();       // 停止
      this.currentTime = 0; // 最初に戻す
    });
  });
  
// 動画が終了したらモーダルを閉じる
$('video').on('ended', function () {
  const modal = $(this).closest('.modal');
  modal.fadeOut('fast');

  // 動画を停止＆巻き戻し（保険）
  this.pause();
  this.currentTime = 0;
});
  
});

$(window).on('scroll resize', function () {
  var windowScrollTop = $(window).scrollTop();
  var windowInnerHeight = window.innerHeight;

  // autoplay クラスを持つ要素内のすべての video を対象に処理
  $('.autoplay video').each(function () {
    var $video = $(this);
    var videoTop = $video.offset().top;
    var videoHeight = $video.innerHeight();
    var videoBottom = videoTop + videoHeight;

    // 再生判定：videoが停止中で、画面内に入ってきたら再生
    if ($video[0].paused && (windowScrollTop + windowInnerHeight > videoTop) && (windowScrollTop < videoBottom)) {
      $video[0].play();
    }

    // 停止判定：videoが再生中で、画面外に出たら停止
    if (!$video[0].paused && ((windowScrollTop + windowInnerHeight < videoTop) || (windowScrollTop > videoBottom))) {
      $video[0].pause();
    }
  });
});

// ページ読み込み時に最初の1本だけ再生（任意）
/*
$(window).on('load', function () {
  var $firstVideo = $('.autoplay video').first();
  if ($firstVideo.length) {
    $firstVideo[0].play();
  }
});
*/


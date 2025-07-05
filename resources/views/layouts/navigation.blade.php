
<div class="headnav">
<nav>
<!-- Navigation Links -->
<p class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><a href="{{ route('dashboard') }}">ダッシュボード</a></p>
<p class="{{ request()->routeIs('categories.index') ? 'active' : '' }}"><a href="{{ route('categories.index') }}">タスク管理</a></p>
<p class="{{ request()->routeIs('categories.shared') ? 'active' : '' }}"><a href="{{ route('categories.shared') }}">タスク受信</a></p>
<p class="{{ request()->routeIs('share-groups.index') ? 'active' : '' }}"><a href="{{ route('share-groups.index') }}">グループ管理</a></p>
</nav>
</div>

<div class="setting">
<!-- Settings -->
<div class="account"><p><strong>{{ Auth::user()->name }}</strong><strong>{{ Auth::user()->email }}</strong></p></div>
<div class="qr"><p><button id="show-qr">QR</button><span>表示</span></p></div>
<p class="{{ request()->routeIs('profile.edit') ? 'profile active' : 'profile' }}"><a href="{{ route('profile.edit') }}"><span>設定</span></a></p>
<form method="POST" action="{{ route('logout') }}">
@csrf
<p class="logout"><a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"><span>ログアウト</span></a></p>
</form>
</div>

<!-- QRモーダル -->
<div id="qr-modal">
<div class="inner"><div class="block">
    <div id="qr-code-output"></div>
    {{--<button id="close-qr">閉じる</button>--}}
</div></div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const showBtn = document.getElementById('show-qr');
    const closeBtn = document.getElementById('close-qr'); // 存在しない場合も考慮
    const modal = document.getElementById('qr-modal');
    const qrOutput = document.getElementById('qr-code-output');

    if (!showBtn || !modal || !qrOutput) return;

    showBtn.addEventListener('click', function (e) {
        e.preventDefault(); // aタグ対策

        const userId = "{{ auth()->user()->email }}"; // または ID 等
        qrOutput.innerHTML = ''; // 初期化

        const canvas = document.createElement('canvas');
        QRCode.toCanvas(canvas, userId, function (error) {
            if (error) {
                console.error("QR生成エラー:", error);
                return;
            }
            qrOutput.appendChild(canvas);
            modal.style.display = 'block';
        });
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            modal.style.display = 'none';
        });
    }

    // canvas以外をクリックで閉じる
    modal.addEventListener('click', function (e) {
        const isOutside = !e.target.closest('#qr-code-output') && !e.target.closest('#close-qr');
        if (isOutside) {
            modal.style.display = 'none';
        }
    });

    // ESCキーでも閉じる
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            modal.style.display = 'none';
        }
    });
});
</script>

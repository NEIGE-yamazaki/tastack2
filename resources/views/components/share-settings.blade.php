@if ($isOwner && $category->sharedUsers->count())
    <div class="taskshare" id="shared-users-block">
        <p class="title">共有中のユーザー</p>
        <ul>
            @foreach($category->sharedUsers as $user)
                <li>
                    <form action="{{ route('categories.share.update', [$category->id, $user->id]) }}"
                          method="POST"
                          class="share-edit-form">
                        @csrf
                        @method('PATCH')
                        <p class="name">
                        <strong>{{ $user->name }}</strong>
                        @if (!$user->pivot->is_confirmed)
                        <span>未承認</span>
                        @endif
                        </p>
                        <p class="email">{{ $user->email }}</p>
                        <select name="permission">
                            <option value="view" {{ $user->pivot->permission === 'view' ? 'selected' : '' }}>閲覧のみ</option>
                            <option value="edit" {{ $user->pivot->permission === 'edit' ? 'selected' : '' }}>編集可能</option>
                            <option value="full" {{ $user->pivot->permission === 'full' ? 'selected' : '' }}>全権</option>
                        </select>
                        <button type="submit" class="edit">更新</button>
                    </form>
                    <form action="{{ route('categories.share.delete', [$category->id, $user->id]) }}"
                          method="POST"
                          class="share-delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete">削除</button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>
@endif

@if ($isOwner)
    <div class="taskshare">
        <p class="title">このカテゴリの共有</p>

        <!-- ▼ グループから選択 -->
        @if (!empty($groups) && $groups->count())
            <div class="group">
                <div class="inner">
                <label>共有グループから選択：</label>
                <select id="group-selector">
                    <option value="">-- グループを選択 --</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}" data-members='@json($group->members->map(fn($m) => $m->user->account_id ?? $m->user->email))'>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>
                </div>
                <button type="button" onclick="insertGroupMembers()" class="add">追加</button>
                <button id="scan-qr-btn">QR</button>
<div id="qr-scan-modal" class="modal">
    <div class="modal-inner"><div class="block">
        {{--<h3>QRコードを読み取る</h3>--}}
        <div id="qr-reader" style="width: 100%; max-width: 300px; margin: auto;"></div>
    </div></div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    function waitForHtml5Qrcode(retries = 10) {
        if (typeof Html5Qrcode !== 'undefined') {
            initQRScanner();
        } else if (retries > 0) {
            console.log("html5-qrcode 待機中...");
            setTimeout(() => waitForHtml5Qrcode(retries - 1), 300);
        } else {
            console.warn("QRボタンまたはライブラリが存在しません");
        }
    }

    waitForHtml5Qrcode();

    function initQRScanner() {
        const scanBtn = document.getElementById('scan-qr-btn');
        const modal = document.getElementById('qr-scan-modal');
        const qrReader = document.getElementById('qr-reader');
        let qrScanner = null;

        if (!scanBtn || !modal || !qrReader) return;

        scanBtn.addEventListener('click', function () {
            modal.style.display = 'block';
            qrScanner = new Html5Qrcode("qr-reader");

            qrScanner.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: 250 },
                (decodedText) => {
                    const inputs = document.querySelectorAll('input[name="identifiers[]"]');
                    const lastInput = inputs[inputs.length - 1];
                    lastInput.value = decodedText;

                    qrScanner.stop().then(() => {
                        modal.style.display = 'none';
                        qrScanner.clear();
                        qrScanner = null;
                    });
                },
                (error) => {
                    console.log("QR読み取り失敗", error);
                }
            );
        });

        // QRリーダー以外をクリックで閉じる
        modal.addEventListener('click', function (e) {
            if (!e.target.closest('#qr-reader')) {
                if (qrScanner) {
                    qrScanner.stop().then(() => {
                        modal.style.display = 'none';
                        qrScanner.clear();
                        qrScanner = null;
                    });
                } else {
                    modal.style.display = 'none';
                }
            }
        });

        // ESCキーでも閉じる
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal.style.display === 'block') {
                if (qrScanner) {
                    qrScanner.stop().then(() => {
                        modal.style.display = 'none';
                        qrScanner.clear();
                        qrScanner = null;
                    });
                } else {
                    modal.style.display = 'none';
                }
            }
        });
    }
});
</script>
            </div>
        @endif

        <form id="share-form" action="{{ route('categories.share', $category) }}" method="POST">
            @csrf

            <div id="share-user-list">
                <div class="share-user-row">
                    <input type="text" name="identifiers[]" placeholder="アカウントID または メールアドレス">
                    <select name="permissions[]">
                        <option value="view">閲覧のみ</option>
                        <option value="edit">編集可能</option>
                        <option value="full">全権</option>
                    </select>
                </div>
            </div>
            
            <div class="addform leftright">
            <button type="button" onclick="addShareUserRow()" class="add">共有先を追加</button>
            <button type="submit" class="send">共有リンクを送信</button>
            </div>
            
        </form>

    </div>
@endif

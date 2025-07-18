@if ($isOwner)
<div class="taskshare">
    <p class="title">このカテゴリの共有</p>

    <form action="{{ route('categories.share', $category) }}" method="POST">
        @csrf

        <!-- ▼ グループ選択 -->
        @if (!empty($groups) && $groups->count())
            <div class="group">
                <label>共有グループから選択：</label>
                <select id="group-selector">
                    <option value="">-- グループを選択 --</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}" data-members='@json($group->members->pluck("identifier"))'>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>
                <button type="button" onclick="insertGroupMembers()">追加</button>
<button id="scan-qr-btn">QR</button>
<div id="qr-scan-modal" class="modal" style="display:none;">
    <div class="modal-inner">
        <h3>QRコードを読み取る</h3>
        <div id="qr-reader" style="width: 300px;"></div>
        <button id="close-qr-scan-modal">閉じる</button>
    </div>
</div>
<script>
document.getElementById('scan-qr-btn').addEventListener('click', function () {
    document.getElementById('qr-scan-modal').style.display = 'block';
    const html5QrCode = new Html5Qrcode("qr-reader");
    html5QrCode.start(
        { facingMode: "environment" },
        {
            fps: 10,
            qrbox: 250
        },
        (decodedText) => {
            document.getElementById('share-input').value = decodedText;
            html5QrCode.stop();
            document.getElementById('qr-scan-modal').style.display = 'none';
        },
        (errorMessage) => {
            // console.log("読み取り失敗", errorMessage);
        }
    );
});

document.getElementById('close-qr-scan-modal').addEventListener('click', function () {
    Html5Qrcode.getCameras().then(devices => {
        const html5QrCode = new Html5Qrcode("qr-reader");
        html5QrCode.stop().then(() => {
            document.getElementById('qr-scan-modal').style.display = 'none';
        });
    });
});
</script>
            </div>
        @endif

        <!-- ▼ 個別指定リスト -->
        <div id="share-user-list">
            <div class="share-user-row">
                <input type="text" name="identifiers[]" placeholder="アカウントID または メールアドレス" required>
                <select name="permissions[]">
                    <option value="view">閲覧のみ</option>
                    <option value="edit">編集可能</option>
                    <option value="full">全権</option>
                </select>
            </div>
        </div>

        <button type="button" onclick="addShareUserRow()" class="add">共有先を追加</button>
        <button type="submit" class="send">共有リンクを送信</button>
        
    </form>
    
<script>
    function addShareUserRow() {
        const list = document.getElementById('share-user-list');
        const original = document.querySelector('.share-user-row');
        const clone = original.cloneNode(true);

        clone.querySelector('input').value = '';
        clone.querySelector('select').value = 'view';

        // 「全権」オプションがなければ追加
        const select = clone.querySelector('select');
        const hasFullOption = Array.from(select.options).some(opt => opt.value === 'full');
        if (!hasFullOption) {
            const option = document.createElement('option');
            option.value = 'full';
            option.text = '全権';
            select.appendChild(option);
        }

        if (!clone.querySelector('.remove-button')) {
            const btn = document.createElement('button');
            btn.textContent = '削除';
            btn.type = 'button';
            btn.className = 'remove-button';
            btn.style = 'margin-left: 0.5rem; background-color: #dc3545; color: #fff; border: none; border-radius: 4px; padding: 0.2rem 0.5rem;';
            btn.onclick = function () {
                list.removeChild(clone);
            };
            clone.appendChild(btn);
        }

        list.appendChild(clone);
    }

    function insertGroupMembers() {
        const select = document.getElementById('group-selector');
        const selected = select.options[select.selectedIndex];
        const members = JSON.parse(selected.dataset.members || '[]');

        if (!members.length) {
            alert('メンバーが見つかりません');
            return;
        }

        const list = document.getElementById('share-user-list');

        members.forEach(identifier => {
            const row = document.querySelector('.share-user-row').cloneNode(true);
            row.querySelector('input').value = identifier;
            row.querySelector('select').value = 'view';

            // 「全権」オプションがなければ追加
            const selectEl = row.querySelector('select');
            const hasFullOption = Array.from(selectEl.options).some(opt => opt.value === 'full');
            if (!hasFullOption) {
                const option = document.createElement('option');
                option.value = 'full';
                option.text = '全権';
                selectEl.appendChild(option);
            }

            if (!row.querySelector('.remove-button')) {
                const btn = document.createElement('button');
                btn.textContent = '削除';
                btn.type = 'button';
                btn.className = 'remove-button';
                btn.style = 'margin-left: 0.5rem; background-color: #dc3545; color: #fff; border: none; border-radius: 4px; padding: 0.2rem 0.5rem;';
                btn.onclick = function () {
                    list.removeChild(row);
                };
                row.appendChild(btn);
            }

            list.appendChild(row);
        });
    }
</script>
    
</div>
@endif

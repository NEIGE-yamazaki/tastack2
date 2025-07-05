document.addEventListener('DOMContentLoaded', function () {

    const taskList = document.getElementById('task-list');

    // --- チェックボックスを .taskcheck 全体で反応させる ---
    taskList.addEventListener('click', function (e) {
        const checkArea = e.target.closest('.taskcheck');
        if (!checkArea) return;

        const checkbox = checkArea.querySelector('.task-toggle-checkbox');
        if (!checkbox || e.target === checkbox) return; // 直接クリックは除外

        checkbox.click(); // チェック切替をトリガー
    });

    // --- タスク完了状態トグル ---
    taskList.addEventListener('change', function (e) {
        const checkbox = e.target.closest('.task-toggle-checkbox');
        if (!checkbox) return;

        const form = checkbox.closest('form');
        fetch(form.action, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({})
        })
        .then(res => res.json())
        .then(data => {
            checkbox.checked = data.is_done;
            const taskCheck = form.closest('.taskcheck');
            const titleDiv = taskCheck?.querySelector('.title');
            if (titleDiv) {
                titleDiv.classList.toggle('end', data.is_done);
            }
        })
        .catch(() => {
            alert('更新に失敗しました');
            checkbox.checked = !checkbox.checked;
        });
    });

    // --- 編集リンク ---
    taskList.addEventListener('click', function (e) {
        const link = e.target.closest('.edit-task-link');
        if (!link) return;
        e.preventDefault();

        const li = link.closest('li');
        const id = link.dataset.id;
        const title = link.dataset.title;
        const due = link.dataset.due_date || '';
        const note = link.dataset.note || '';
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        // 他の編集フォームを強制的に閉じて復元
        document.querySelectorAll('.task-edit-form').forEach(form => {
            const parentLi = form.closest('li');
            if (parentLi?.dataset.originalHtml) {
                parentLi.innerHTML = parentLi.dataset.originalHtml;
                const checkbox = parentLi.querySelector('.task-toggle-checkbox');
                const titleEl = parentLi.querySelector('.title');
                if (parentLi.dataset.originalChecked === 'true') {
                    if (checkbox) checkbox.checked = true;
                    if (titleEl && parentLi.dataset.originalEnd === 'true') titleEl.classList.add('end');
                } else {
                    if (checkbox) checkbox.checked = false;
                    if (titleEl) titleEl.classList.remove('end');
                }
            }
        });

        // 編集前の状態保存
        const originalHtml = li.innerHTML;
        const checkboxEl = li.querySelector('.task-toggle-checkbox');
        const titleEl = li.querySelector('.title');
        li.dataset.originalHtml = originalHtml;
        li.dataset.originalChecked = checkboxEl?.checked ? 'true' : 'false';
        li.dataset.originalEnd = titleEl?.classList.contains('end') ? 'true' : 'false';

// 編集フォーム差し替え
li.innerHTML = `
    <div class="taskform commonform">
        <form class="task-edit-form" method="POST" action="/tasks/${id}">
            <input type="hidden" name="_token" value="${csrf}">
            <input type="hidden" name="_method" value="PATCH">
            <input type="text" name="title" value="${title}" required>
            <input type="text" name="due_date" id="due_date_edit_${id}" value="${due}" placeholder="期限日時を選択">
            <textarea name="note">${note}</textarea>
            <button type="submit" class="save">保存</button>
            <button type="button" class="cancel-edit">キャンセル</button>
        </form>
    </div>
`;

// flatpickr 初期化
flatpickr(`#due_date_edit_${id}`, {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    time_24hr: true,
    locale: "ja",
    defaultHour: 0,
    defaultMinute: 0,
    disableMobile: true
});

        // 保存処理
        li.querySelector('.task-edit-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(this).entries());
            fetch(this.action, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(() => location.reload())
            .catch(() => alert('保存に失敗しました'));
        });

        // キャンセル処理
        li.querySelector('.cancel-edit').addEventListener('click', () => {
            li.innerHTML = li.dataset.originalHtml;
            const checkbox = li.querySelector('.task-toggle-checkbox');
            const titleEl = li.querySelector('.title');
            if (li.dataset.originalChecked === 'true') {
                if (checkbox) checkbox.checked = true;
                if (titleEl && li.dataset.originalEnd === 'true') titleEl.classList.add('end');
            } else {
                if (checkbox) checkbox.checked = false;
                if (titleEl) titleEl.classList.remove('end');
            }
        });
    });

    // --- タスク削除 ---
    taskList.addEventListener('submit', function (e) {
        const form = e.target.closest('.task-delete-form');
        if (!form) return;
        e.preventDefault();
        if (!confirm('このタスクを本当に削除しますか？')) return;

        fetch(form.action, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
        })
        .then(() => {
            form.closest('li').remove();
        });
    });

    // --- 共有ユーザー：権限更新 ---
    document.querySelectorAll('.share-edit-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(form).entries());
            fetch(form.action, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(data => alert(data.message))
            .catch(() => alert('更新に失敗しました'));
        });
    });


    // 削除ボタンを追加する関数（共有先追加時などで使用）
    function addRemoveButton(row, listSelector = '#share-user-list') {
        const list = document.querySelector(listSelector);

        const btn = document.createElement('button');
        btn.textContent = '削除';
        btn.type = 'button';
        btn.className = 'remove-button';
        btn.onclick = function () {
            list.removeChild(row);

            if (list.children.length === 0) {
                const sharedBlock = document.getElementById('shared-users-block');
                if (sharedBlock) sharedBlock.remove();
            }
        };
        row.appendChild(btn);
    }

    // 「共有先を追加」ボタンで呼び出される
    window.addShareUserRow = function () {
        const list = document.getElementById('share-user-list');
        const row = document.querySelector('.share-user-row');
        const clone = row.cloneNode(true);

        clone.querySelector('input').value = '';
        clone.querySelector('select').value = 'view';

        addRemoveButton(clone);
        list.appendChild(clone);
    }

    // グループから一括追加
    window.insertGroupMembers = function () {
        const select = document.getElementById('group-selector');
        const members = JSON.parse(select.options[select.selectedIndex]?.dataset.members || '[]');

        if (!members.length) {
            alert('メンバーが見つかりません');
            return;
        }

        const list = document.getElementById('share-user-list');

        members.forEach(identifier => {
            const row = document.querySelector('.share-user-row').cloneNode(true);
            row.querySelector('input').value = identifier;
            row.querySelector('select').value = 'view';

            addRemoveButton(row);
            list.appendChild(row);
        });
    }

    // フォーム送信時に空欄を除外
    const shareForm = document.getElementById('share-form');
    if (shareForm) {
        shareForm.addEventListener('submit', function (e) {
            const rows = document.querySelectorAll('.share-user-row');
            let hasValidInput = false;

            rows.forEach(row => {
                const input = row.querySelector('input');
                if (input.value.trim() === '') {
                    input.disabled = true;
                    row.querySelector('select').disabled = true;
                } else {
                    hasValidInput = true;
                }
            });

            if (!hasValidInput) {
                e.preventDefault();
                alert('少なくとも1件の共有先を入力してください');
            }
        });
    }

    // 既存の共有削除ボタンにAjax処理を付与
    document.querySelectorAll('.share-delete-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!confirm('この共有を解除しますか？')) return;

            const formData = new FormData(form);
            const action = form.getAttribute('action');

            fetch(action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    const li = form.closest('li');
                    li.remove();

                    const list = document.querySelector('#shared-users-block ul');
                    if (list.children.length === 0) {
                        const block = document.getElementById('shared-users-block');
                        if (block) block.remove();
                    }
                } else {
                    alert('削除に失敗しました');
                }
            })
            .catch(error => {
                console.error(error);
                alert('通信エラーが発生しました');
            });
        });
    });
    
});

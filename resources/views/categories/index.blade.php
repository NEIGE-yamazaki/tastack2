<x-app-layout>
    <div class="maincontents">

        <div class="headfix">
            <p class="title">カテゴリ一覧</p>
        </div>

        <div class="taskform commonform category">
            @if(session('success'))
                <div class="result"><p class="success">{{ session('success') }}</p></div>
            @endif

<form action="{{ route('categories.store') }}" method="POST" id="category-create-form" enctype="multipart/form-data">
    @csrf
    <div class="category_name">
    <input
        type="text"
        name="name"
        placeholder="カテゴリ名"
        required
    >
    </div>
    <div class="category_icon">
    
<div class="file-upload-wrapper">
  <label class="file-upload-label" for="icon"><strong>画像を選択</strong><span id="file-name">アイコン画像未選択</span></label>
  <input type="file" id="icon" name="icon" accept="image/*" class="file-upload-input" onchange="checkSize(this)">
</div>
<script>
function checkSize(input) {
  const file = input.files[0];
  if (file) {
    document.getElementById('file-name').textContent = file.name;
    // サイズチェック処理（例：5MB制限）
    if (file.size > 5 * 1024 * 1024) {
      alert("ファイルサイズは5MB以内にしてください。");
      input.value = ""; // リセット
      document.getElementById('file-name').textContent = "未選択";
    }
  }
}
</script>

    </div>
    <div class="category_add">
    <button type="submit">追加</button>
    </div>
</form>
            
        </div>

        <div class="categorylist owner">
            <ul id="category-list">
                @forelse($categories as $category)
                    <li data-id="{{ $category->id }}" data-icon="{{ $category->icon_path }}" data-incomplete="{{ $category->incomplete_tasks_count }}" data-name="{{ $category->name }}">
                        <p>
                            <a href="{{ route('categories.show', $category) }}" class="category-name">
                                <span class="img"><img src="{{ asset('storage/' . $category->icon_path) }}" alt="アイコン"></span>
                                @if($category->incomplete_tasks_count > 0)
                                    <span class="status">未完了<i>{{ $category->incomplete_tasks_count }}</i></span>
                                @endif
                                <strong class="title">{{ $category->name }}</strong>
                            </a>
                        </p>
                        <button class="edit-category" data-id="{{ $category->id }}" data-name="{{ $category->name }}" data-icon="{{ $category->icon_path }}">編集</button>
                        <form action="{{ route('categories.destroy', $category) }}"
                              method="POST"
                              class="delete-category-form"
                              style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-category">削除</button>
                        </form>
                    </li>
                @empty
                    <li class="none"><p>まだカテゴリがありません。</p></li>
                @endforelse
            </ul>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const list = document.getElementById('category-list');

    // 編集ボタンクリック時
    list.addEventListener('click', function (e) {
        const btn = e.target.closest('.edit-category');
        if (!btn) return;

        const li = btn.closest('li');
        const id = btn.dataset.id;
        const name = btn.dataset.name;

// 編集フォームに差し替え
li.innerHTML = `
    <form class="edit-category-form" data-id="${id}" method="POST" action="/categories/${id}" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
        <input type="hidden" name="_method" value="PATCH">
        <input type="text" name="name" value="${name}" required>

        <div class="file-upload-wrapper">
            <label class="file-upload-label" for="icon-${id}">
                <strong>画像を選択</strong>
                <span id="file-name-${id}">アイコン画像未選択</span>
            </label>
            <input type="file" id="icon-${id}" name="icon" accept="image/*" class="file-upload-input" onchange="checkSize(this, ${id})">
        </div>

        <button type="submit" class="save">保存</button>
        <button type="button" class="cancel-edit">キャンセル</button>
    </form>
`;
        
    });

    // 編集フォーム送信（POST + FormData + _method=PATCH）
    list.addEventListener('submit', function (e) {
        const form = e.target.closest('.edit-category-form');
        if (!form) return;

        e.preventDefault();
        const id = form.dataset.id;
        const formData = new FormData(form);
        formData.append('_method', 'PATCH'); // LaravelにPATCHとして認識させる

        fetch(`/categories/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(res => {
            if (!res.ok) throw new Error('更新失敗');
            return res.json();
        })
.then(data => {
    const li = form.closest('li'); // li要素を取得

    li.innerHTML = `
        <p>
            <a href="/categories/${id}" class="category-name">
                <span class="img"><img src="/storage/${data.icon_path}" alt="アイコン"></span>
                ${data.incomplete_tasks_count > 0
                    ? `<span class="status">未完了<i>${data.incomplete_tasks_count}</i></span>`
                    : ''}
                <strong class="title">${data.name}</strong>
            </a>
        </p>
        <button class="edit-category" data-id="${id}" data-name="${data.name}" data-icon="${data.icon_path}">編集</button>
        <form action="/categories/${id}" method="POST" class="delete-category-form" style="display:inline;">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="delete-category">削除</button>
        </form>
    `;

    // ← ここが追加箇所（保存後にdata属性を更新）
    li.setAttribute('data-name', data.name);
    li.setAttribute('data-icon', data.icon_path);
    li.setAttribute('data-incomplete', data.incomplete_tasks_count);
})
        .catch(() => alert('更新に失敗しました'));
    });

// キャンセルボタン処理
list.addEventListener('click', function (e) {
    const btn = e.target.closest('.cancel-edit');
    if (!btn) return;

    const form = btn.closest('form');
    const li = form.closest('li');
    const id = li.dataset.id;
    const name = li.dataset.name; // ← 元の名前を取得（inputからではなく）
    const iconPath = li.dataset.icon || 'images/default_icon.png';
    const incompleteCount = li.dataset.incomplete || 0;

    const statusHtml = incompleteCount > 0
        ? `<span class="status">未完了<i>${incompleteCount}</i></span>`
        : '';

    li.innerHTML = `
        <p>
            <a href="/categories/${id}" class="category-name">
                <span class="img"><img src="/storage/${iconPath}" alt="アイコン"></span>
                ${statusHtml}
                <strong class="title">${name}</strong>
            </a>
        </p>
        <button class="edit-category" data-id="${id}" data-name="${name}" data-icon="${iconPath}">編集</button>
        <form action="/categories/${id}" method="POST" class="delete-category-form" style="display:inline;">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="delete-category">削除</button>
        </form>
    `;
});

    // 削除フォーム送信（Ajax）
    list.addEventListener('submit', function (e) {
        const form = e.target.closest('.delete-category-form');
        if (!form) return;

        e.preventDefault();
        if (!confirm('このカテゴリを削除しますか？（タスクも削除されます）')) return;

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new FormData(form)
        })
        .then(res => {
            if (res.ok) {
                form.closest('li').remove();
            } else {
                alert('削除に失敗しました');
            }
        })
        .catch(() => alert('通信エラーが発生しました'));
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('category-list');

    new Sortable(list, {
        delay: 200,              // 200ms長押しでドラッグ開始
        delayOnTouchOnly: true,  // タッチデバイスのみdelay有効
        animation: 150,
        onEnd: function () {
            const orders = [...list.querySelectorAll('li')].map((el, index) => ({
                id: el.dataset.id,
                order: index + 1
            }));

            fetch("{{ route('categories.reorder') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ orders: orders })
            })
            .then(response => response.json())
            .then(data => {
                console.log('順序保存成功:', data);
            })
            .catch(error => {
                console.error('順序保存エラー:', error);
            });
        }
    });
});
</script>
    
</x-app-layout>

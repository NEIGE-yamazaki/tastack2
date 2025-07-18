<x-app-layout>

    <div class="maincontents">
    
        <div class="headfix">
        <p class="title">共有グループの管理</p>
        </div>
        
        @if(session('success'))
            <div class="result"><p class="success">{{ session('success') }}</p></div>
        @endif

        @if(session('error'))
            <div class="result"><p class="error">{{ session('error') }}</p></div>
        @endif

    <div class="groupform">
    
        <!-- 新規グループ作成フォーム -->
        <div class="groupcreate">
            <p class="title">新しい共有グループを作成</p>

            <form id="group-form" action="{{ route('share-groups.store') }}" method="POST">
                @csrf

                <div class="groupset">
                    <label class="intitle">グループ名</label>
                    <input type="text" name="name" class="" required>
                    @error('name') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div id="group-members">
                    <label class="intitle">共有先（アカウントIDまたはメールアドレス）</label>
                    <div class="member-row">
                        <input type="text" name="identifiers[]" class="edit">
                    </div>
                </div>
                
                <div class="addform leftright">
                <button type="button" onclick="addMemberInput()" class="add">メンバー追加</button>
                <script>
                    function addMemberInput() {
                        const container = document.getElementById('group-members');
                        const div = document.createElement('div');
                        div.className = 'member-row';
                        div.style.display = 'flex';
                        div.style.gap = '0.5rem';
                        div.style.alignItems = 'center';

                        const input = document.createElement('input');
                        input.type = 'text';
                        input.name = 'identifiers[]';
                        input.className = 'edit';

                        const button = document.createElement('button');
                        button.type = 'button';
                        button.textContent = '削除';
                        button.className = 'delete';
                        button.onclick = function () {
                            container.removeChild(div);
                        };

                        div.appendChild(input);
                        div.appendChild(button);
                        container.appendChild(div);
                    }
                    
document.getElementById('group-form').addEventListener('submit', function (e) {
    const rows = document.querySelectorAll('.member-row');
    let hasValid = false;

    rows.forEach(row => {
        const input = row.querySelector('input');
        if (input.value.trim() !== '') {
            hasValid = true;
        }
    });

    if (!hasValid) {
        e.preventDefault();
        alert('少なくとも1人の共有先を入力してください');
    }
});
                    
                </script>
                <button type="submit" class="create">
                    グループを作成
                </button>
                </div>
                
            </form>
        </div>

        <!-- 既存グループ一覧 -->
        <div class="grouplist">
            <p class="title">登録済みの共有グループ</p>

            @forelse ($groups as $group)
                <div class="groupblock">
                        <div class="inner">
                            <p class="name">{{ $group->name }}</p>
                            <ul>
                                @foreach ($group->members as $member)
                                    <li>
                                        <p>
                                        @if ($member->user)
                                            <strong>{{ $member->user->account_id ?? $member->user->email }}</strong>
                                            <span>（{{ $member->identifier }}）</span>
                                        @else
                                            <strong>{{ $member->identifier }}</strong>
                                        @endif
                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="control">
                            <a href="{{ route('share-groups.edit', $group) }}" class="edit">編集</a>

                            <form action="{{ route('share-groups.destroy', $group) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete">削除</button>
                            </form>
                        </div>
                </div>
            @empty
                <p class="none">まだグループはありません。</p>
            @endforelse
        </div>
        
    </div>
    
    </div>
    
</x-app-layout>

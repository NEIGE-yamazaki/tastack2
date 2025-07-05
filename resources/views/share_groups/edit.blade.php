<x-app-layout>

    <div class="maincontents">
        <div class="headfix">
        <p class="title">共有グループの編集</p>
        </div>
        
        @if(session('success'))
            <div class="result"><p class="success">{{ session('success') }}</p></div>
        @endif

        @if(session('error'))
            <div class="result"><p class="error">{{ session('error') }}</p></div>
        @endif

    <div class="groupform">

        <div class="groupcreate">
            <form id="edit-group-form" action="{{ route('share-groups.update', $group) }}" method="POST">
                @csrf
                @method('PATCH')

                <!-- グループ名 -->
                <div class="groupset">
                    <label class="intitle">グループ名</label>
                    <input type="text" name="name" class=""
                           value="{{ old('name', $group->name) }}" required>
                    @error('name') <p class="error">{{ $message }}</p> @enderror
                </div>

                <!-- 共有先 -->
                <div id="group-members">
                    <label class="intitle">共有先（アカウントIDまたはメールアドレス）</label>

                    @foreach (old('identifiers', $group->members->pluck('identifier')->toArray()) as $identifier)
                        <div class="member-row">
                            <input type="text" name="identifiers[]" class="edit"
                                   value="{{ $identifier }}">
                            <button type="button" class="delete" onclick="this.parentNode.remove()">削除</button>
                        </div>
                    @endforeach
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
                        button.className = 'delete';
                        button.textContent = '削除';
                        button.onclick = function () {
                            container.removeChild(div);
                        };

                        div.appendChild(input);
                        div.appendChild(button);
                        container.appendChild(div);
                    }

                    document.getElementById('edit-group-form').addEventListener('submit', function (e) {
                        const rows = document.querySelectorAll('.member-row');
                        let hasValid = false;

                        rows.forEach(row => {
                            const input = row.querySelector('input');
                            if (input.value.trim() === '') {
                                input.disabled = true;
                            } else {
                                hasValid = true;
                            }
                        });

                        if (!hasValid) {
                            e.preventDefault();
                            alert('少なくとも1人の共有先を入力してください');
                        }
                    });
                </script>
                <button type="submit" class="edit">更新する</button>
                </div>

                <div class="addform">
                    <a href="{{ route('share-groups.index') }}" class="back">戻る</a>
                </div>
                
            </form>
        </div>
    </div>
    
    </div>
</x-app-layout>

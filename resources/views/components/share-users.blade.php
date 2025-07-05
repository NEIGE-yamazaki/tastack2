<!-- resources/views/components/share-users.blade.php -->
@if ($isOwner && $category->sharedUsers->count())
    <div class="taskshare">
        <p class="title">共有中のユーザー</p>
        <ul>
            @foreach($category->sharedUsers as $user)
                <li>
                    <form action="{{ route('categories.share.update', [$category->id, $user->id]) }}" method="POST" class="share-edit-form">
                        @csrf
                        @method('PATCH')
                        <p class="name">{{ $user->name }}</p><p class="email">{{ $user->email }}</p>
                        <select name="permission">
                            <option value="view" {{ $user->pivot->permission === 'view' ? 'selected' : '' }}>閲覧のみ</option>
                            <option value="edit" {{ $user->pivot->permission === 'edit' ? 'selected' : '' }}>編集可能</option>
                            <option value="full" {{ $user->pivot->permission === 'full' ? 'selected' : '' }}>全権</option> <!-- 追加 -->
                        </select>
                        <button type="submit" class="edit">更新</button>
                    </form>
                    <form action="{{ route('categories.share.delete', [$category->id, $user->id]) }}" method="POST" class="share-delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete">削除</button>
                    </form>
                    @if(!$user->pivot->is_confirmed)
                        <p class="status"><span>（未承認）</span></p>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endif
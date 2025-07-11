@php
    $editingId = request('edit');
@endphp

@if ($editingId == $task->id && ($isOwner || $sharedPermission === 'full'))
<div class="taskform commonform">
    <!-- 編集フォーム表示 -->
    <form action="{{ route('tasks.update', $task) }}" method="POST" class="task-edit-form">
        @csrf
        @method('PATCH')

        <input type="text" name="title" value="{{ old('title', $task->title) }}" required>

        <input type="text"
               name="due_date"
               id="due_date_edit_{{ $task->id }}"
               value="{{ old('due_date', \Carbon\Carbon::parse($task->due_date)->format('Y-m-d H:i')) }}"
               placeholder="期限日時を選択">

        <textarea name="note">{{ old('note', $task->note) }}</textarea>

        <button type="submit" class="save">保存</button>
        <a href="{{ route('categories.show', $category) }}" class="cancel">キャンセル</a>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    flatpickr("#due_date_edit_{{ $task->id }}", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        locale: "ja",
        defaultHour: 0,
        defaultMinute: 0,
        disableMobile: true
    });
});
</script>
@else

<div class="taskcheck clickable-task {{ $sharedPermission === 'view' ? 'readonly' : '' }}" data-task-id="{{ $task->id }}">
    @if ($canEdit)
        <!-- 編集権限者のチェックボックスフォーム（非表示） -->
        <div class="check">
            <form action="{{ route('tasks.toggle', $task) }}" method="POST" class="task-toggle-form">
                @csrf
                @method('PATCH')
                <input
                    type="checkbox"
                    class="task-toggle-checkbox"
                    {{ $task->is_done ? 'checked' : '' }}
                    style="margin-right: 0.5rem;"
                >
                <strong></strong>
            </form>
        </div>
    @else
        <!-- 閲覧専用ユーザー向け：変更不可マーク -->
        <div class="{{ $task->is_done ? 'check end' : 'check' }}"><strong></strong></div>
    @endif

    <!-- タイトル表示（完了時は取消線） -->
    <div class="{{ $task->is_done ? 'title end' : 'title' }}">
        <strong>{{ $task->title }}</strong>
        @if ($task->due_date)
            <span>（期限: {{ \Carbon\Carbon::parse($task->due_date)->format('Y年n月j日 H:i') }}）</span>
        @endif
    </div>
</div>

<div class="taskedit">
    <!-- 編集・削除はオーナーまたは全権共有者のみ -->
    @if ($isOwner || $sharedPermission === 'full')
        <a href="#" class="edit-task-link"
           data-id="{{ $task->id }}"
           data-title="{{ $task->title }}"
           data-due_date="{{ $task->due_date }}"
           data-note="{{ $task->note }}">
            編集
        </a>

        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="task-delete-form">
            @csrf
            @method('DELETE')
            <button type="submit">
                削除
            </button>
        </form>
    @endif
</div>

@if ($task->note)
    <div class="tasknote">
        <p>{{ $task->note }}</p>
    </div>
@endif

@if ($task->used_ai_advisor && $task->ai_advice)
    <div class="taskai">
        <p><strong>AI</strong></p><p>{{ $task->ai_advice }}</p>
    </div>
@endif

@endif

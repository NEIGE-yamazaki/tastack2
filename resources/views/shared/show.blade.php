<x-app-layout>
    <div class="maincontents">
    
        <div class="headfix">
            <span class="img"><img src="{{ asset('storage/' . $category->icon_path) }}" alt="アイコン"></span>
            <p class="title">{{ $category->name }} タスク一覧</p>
        </div>

    <div class="shared_readonly">

@forelse($category->tasks as $task)

<div class="taskcheck clickable-task readonly">
    <div class="{{ $task->is_done ? 'check end' : 'check' }}"><strong></strong></div>
    <!-- タイトル表示（完了時は取消線） -->
    <div class="{{ $task->is_done ? 'title end' : 'title' }}">
        <strong>{{ $task->title }}</strong>
        @if ($task->due_date)
            <span>（期限: {{ $task->due_date }}）</span>
        @endif
    </div>
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

@empty
タスクはありません。
@endforelse

    </div>
    </div>
</x-app-layout>

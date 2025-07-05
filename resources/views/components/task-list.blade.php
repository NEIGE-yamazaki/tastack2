<!-- resources/views/components/task-list.blade.php -->

<ul id="task-list" style="padding-left: 1rem;">
    @php $editingId = request('edit'); @endphp

    @forelse($tasks as $task)
        <li data-task-id="{{ $task->id }}" style="margin-bottom: 1.5rem;">
            @include('components.task-item', ['task' => $task, 'isOwner' => $isOwner, 'category' => $category])
        </li>
    @empty
        <li>タスクはまだありません。</li>
    @endforelse
</ul>

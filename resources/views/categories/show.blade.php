<x-app-layout>

    <div class="maincontents">
        <div class="headfix">
        <span class="img"><img src="{{ asset('storage/' . $category->icon_path) }}" alt="アイコン"></span>
        <p class="title">{{ $category->name }} のタスク一覧</p>
        </div>
        @if(session('success'))
            <div class="result"><p class="success">{{ session('success') }}</p></div>
        @endif

        @if(session('error'))
            <div class="result"><p class="error">{{ session('error') }}</p></div>
        @endif
        
        <ul id="task-list">
            @foreach($tasks as $task)
                <li data-task-id="{{ $task->id }}">
                    @include('components.task-item', ['task' => $task, 'isOwner' => $isOwner, 'category' => $category])
                </li>
            @endforeach
        </ul>

        @if ($isOwner)
            @include('components.task-form', ['category' => $category])
        @endif

        @include('components.share-settings', ['category' => $category, 'isOwner' => $isOwner])
    </div>

    <script src="{{ asset('js/tasks.js') }}"></script>
</x-app-layout>

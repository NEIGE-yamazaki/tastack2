<x-app-layout>
    <div class="maincontents">
    
        <div class="headfix">
            <p class="title">タスク一覧</p>
        </div>

        <div class="categorylist space">
            <ul id="category-list">
                @forelse ($ownCategories as $category)
                    <li data-id="{{ $category->id }}">
                        <p>
    <a href="{{ route('categories.show', $category) }}">
        <span class="img"><img src="{{ asset('storage/' . $category->icon_path) }}" alt="アイコン"></span>
        @if($category->incomplete_tasks_count > 0)
          <span class="status">未完了<i>{{ $category->incomplete_tasks_count }}</i></span>
        @endif
        <strong class="title">{{ $category->name }}</strong>
    </a>
                        </p>
                    </li>
                @empty
                    <li class="none"><p>タスクはありません</p></li>
                @endforelse
            </ul>
        </div>

        <div class="headfix">
            <p class="title">共有されたタスク一覧</p>
        </div>
        
<div class="categorylist">
    <ul id="category-list">
        @forelse ($sharedCategories as $category)
            <li data-id="{{ $category->id }}">
                <p>
<a href="{{ route('categories.show', $category) }}">
    <span class="img"><img src="{{ asset('storage/' . $category->icon_path) }}" alt="アイコン"></span>
    @if($category->incomplete_tasks_count > 0)
      <span class="status">未完了<i>{{ $category->incomplete_tasks_count }}</i></span>
    @endif
    <strong class="title">
        {{ $category->name }}
        <i>
            @if ($category->pivot->permission === 'full')
                全権
            @elseif ($category->pivot->permission === 'edit')
                編集可能
            @else
                閲覧のみ
            @endif
        </i>
    </strong>
</a>
                </p>
            </li>
        @empty
            <li class="none"><p>タスクはありません</p></li>
        @endforelse
    </ul>
</div>
        
</x-app-layout>

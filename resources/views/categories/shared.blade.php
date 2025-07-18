<x-app-layout>
    <div class="maincontents">
    
        <div class="headfix">
            <p class="title">共有されたタスク一覧</p>
        </div>

        <div class="categorylist">
            <ul id="category-list">
                @forelse($sharedCategories as $category)
                    <li data-id="{{ $category->id }}">
                        <p>
@if ($category->pivot->permission === 'full')
    <a href="{{ route('categories.show', $category->id) }}" class="category-name">
        <span class="img"><img src="{{ asset('storage/' . $category->icon_path) }}" alt="アイコン"></span>
        @if($category->incomplete_tasks_count > 0)
          <span class="status">未完了<i>{{ $category->incomplete_tasks_count }}</i></span>
        @endif
        <strong class="title">{{ $category->name }}<i>全権</i></strong>
    </a>
@elseif ($category->pivot->permission === 'edit')
    <a href="{{ route('categories.show', $category->id) }}" class="category-name">
        <span class="img"><img src="{{ asset('storage/' . $category->icon_path) }}" alt="アイコン"></span>
        @if($category->incomplete_tasks_count > 0)
          <span class="status">未完了<i>{{ $category->incomplete_tasks_count }}</i></span>
        @endif
        <strong class="title">{{ $category->name }}<i>編集可能</i></strong>
    </a>
@else
    <a href="{{ route('shared.categories.show', ['token' => $category->pivot->confirmation_token]) }}" class="category-name">
        <span class="img"><img src="{{ asset('storage/' . $category->icon_path) }}" alt="アイコン"></span>
        @if($category->incomplete_tasks_count > 0)
          <span class="status">未完了<i>{{ $category->incomplete_tasks_count }}</i></span>
        @endif
        <strong class="title">{{ $category->name }}<i>閲覧のみ</i></strong>
    </a>
@endif
                        </p>
                    </li>
                @empty
                    <li class="none"><p>共有されたカテゴリはありません。</p></li>
                @endforelse
            </ul>
        </div>

    </div>
</x-app-layout>
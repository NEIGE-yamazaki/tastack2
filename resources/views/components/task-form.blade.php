{{-- resources/views/components/task-form.blade.php --}}
<div class="taskform commonform">
<form action="{{ route('categories.tasks.store', $category) }}" method="POST">
    @csrf

    <div>
        <input
            type="text"
            name="title"
            placeholder="タスク名"
            required
        >
    </div>

    <div>
        <input
            type="text"
            name="due_date"
            id="due_date"
            placeholder="期限日時を選択"
        >
    </div>

    <div>
        <textarea
            name="note"
            placeholder="メモ（任意）"
        ></textarea>
    </div>

    {{-- Googleカレンダー追加オプション（Google連携済ユーザーのみ） --}}
    {{-- Google審査完了後 --}}
    @if (Auth::user()->google_token)
        <div>
            <label>
                <input type="checkbox" name="add_to_google_calendar" value="1">
                Googleカレンダーに追加する
            </label>
        </div>
    @endif

@if (Auth::user()->ai_advisor_used_today < Auth::user()->ai_advisor_limit_per_day)
    <div class="ai_check">
    <label>
        <input type="checkbox" name="use_ai_advisor" value="1"><strong></strong><span>AIアドバイザー</span></label>
    </div>
@endif

    <button type="submit" class="add">タスクを追加</button>
</form>
</div>

{{-- 画面ロック用オーバーレイ --}}
<div id="ai-overlay">
<div class="inner"><p>タスク追加中･･･</p></div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.taskform form');
    const checkbox = form.querySelector('input[name="use_ai_advisor"]');
    const overlay = document.getElementById('ai-overlay');

    form.addEventListener('submit', function (e) {
        // AIアドバイザーが使われるときのみ表示
        if (checkbox && checkbox.checked) {
            overlay.style.display = 'block';
        }
    });
    
    flatpickr("#due_date", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        locale: "ja",
        defaultHour: 0,   // ← 00:00を指定
        defaultMinute: 0, // ← 00:00を指定
        disableMobile: true
    });
});
</script>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            プロフィール情報
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("アカウントのプロフィール情報とメールアドレスを更新してください。") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('お名前')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Eメール')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('あなたのメールアドレスは未確認です。') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('確認メールを再送するにはこちらをクリックしてください。') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('新しい確認用リンクがあなたのメールアドレスに送信されました。') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
        
<div>
    <x-input-label for="account_id" :value="__('アカウントID')" />
    <x-text-input
        id="account_id"
        name="account_id"
        type="text"
        class="mt-1 block w-full"
        maxlength="10"
        :value="old('account_id', $user->account_id)"
        required
    />
    <x-input-error class="mt-2" :messages="$errors->get('account_id')" />
    <p class="text-sm text-gray-500 mt-1">半角英数字10文字以内。重複不可。</p>
</div>

        <h2 class="text-lg font-medium text-gray-900">
            各種設定
        </h2>

            <div class="form-group">
                <x-input-label for="button_layout" :value="__('レイアウト')" />
                <div class="space-y-2 mt-2">
                    <label class="flex items-center">
                        <input type="radio" name="button_layout" value="menu_above"
                            {{ old('button_layout', $user->button_layout) === 'menu_above' ? 'checked' : '' }}>
                        <span class="ml-2">メニューバー上</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="button_layout" value="menu_under"
                            {{ old('button_layout', $user->button_layout) === 'menu_under' ? 'checked' : '' }}>
                        <span class="ml-2">メニューバー下</span>
                    </label>
                </div>
            </div>

<div class="form-group mt-4">
    <x-input-label for="google_calendar_color" :value="__('Googleカレンダー色')" />
    <div class="calendar-color-options">
        @foreach (range(1, 11) as $colorId)
            @php
                $isSelected = old('google_calendar_color', $user->google_calendar_color) == $colorId;
            @endphp
            <label class="calendar-color-label {{ $isSelected ? 'selected' : '' }}">
                <input
                    type="radio"
                    name="google_calendar_color"
                    value="{{ $colorId }}"
                    class="calendar-color-input-hidden"
                    {{ $isSelected ? 'checked' : '' }}
                >
                <span class="calendar-color-circle"
                      style="background-color: {{ googleCalendarColorCode($colorId) }}"></span>
            </label>
        @endforeach
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const colorLabels = document.querySelectorAll('.calendar-color-label');

    colorLabels.forEach(label => {
        const input = label.querySelector('input[type="radio"]');
        input.addEventListener('change', () => {
            // すべてのラベルから選択クラスを除去
            document.querySelectorAll('.calendar-color-label').forEach(l => {
                l.classList.remove('selected');
            });

            // チェックされたラベルに選択クラスを付与
            if (input.checked) {
                label.classList.add('selected');
            }
        });
    });
});
</script>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('保存') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('保存しました') }}</p>
            @endif
        </div>
    </form>
</section>

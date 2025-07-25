<x-login-layout>

<!-- Session Status -->
<x-auth-session-status class="mb-4" :status="session('status')" />

<div class="login_inner">
<section class="loginset">

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="email input_set">
            <x-input-label for="email" :value="__('Eメール')" />
            <x-text-input id="email" class="" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="" />
        </div>
        <div class="password input_set">
            <x-input-label for="password" :value="__('パスワード')" />
            <x-text-input id="password" class="" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="" />
        </div>
        <div class="rememberme">
            <label for="remember_me" class="">
                <input id="remember_me" type="checkbox" class="" name="remember">
                <span class="">{{ __('ログイン状態を保存する') }}</span>
            </label>
        </div>
        <div class="loginbtn submit">
            <x-primary-button class="">
                {{ __('ログイン') }}
            </x-primary-button>
        </div>
        <div class="passwordrequest">
            @if (Route::has('password.request'))
                <a class="" href="{{ route('password.request') }}">
                    {{ __('パスワードをお忘れですか？') }}
                </a>
            @endif
        </div>
    </form>

{{-- SNSログインボタン --}}
<div class="snslogin">
<p class="google"><a href="{{ route('login.provider', 'google') }}" class="inline-block px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition">Googleでログイン</a></p>
</div>

</section>
</div>

</x-login-layout>
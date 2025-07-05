<x-login-layout>

<div class="login_inner">
<section class="loginset">

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="name input_set">
            <x-input-label for="name" :value="__('お名前')" />
            <x-text-input id="name" class="" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="" />
        </div>
        <div class="email input_set">
            <x-input-label for="email" :value="__('Eメール')" />
            <x-text-input id="email" class="" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="" />
        </div>
        <div class="password input_set">
            <x-input-label for="password" :value="__('パスワード')" />
            <x-text-input id="password" class="" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="" />
        </div>
        <div class="password input_set">
            <x-input-label for="password_confirmation" :value="__('パスワード（確認）')" />
            <x-text-input id="password_confirmation" class="" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="" />
        </div>
        <div class="loginbtn submit">
            <x-primary-button class="ms-4">
                {{ __('登録する') }}
            </x-primary-button>
        </div>
        <div class="passwordrequest">
            <a class="" href="{{ route('login') }}">
                {{ __('アカウントをお持ちの方はこちら') }}
            </a>
        </div>
    </form>

</section>
</div>

</x-login-layout>
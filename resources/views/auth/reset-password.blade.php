<x-login-layout>

<div class="login_inner">
<section class="loginset">

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        
        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        
        <div class="email input_set">
            <x-input-label for="email" :value="__('Eメール')" />
            <x-text-input id="email" class="" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
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
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>

</section>
</div>

</x-login-layout>
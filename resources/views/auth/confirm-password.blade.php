<x-login-layout>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

<div class="login_inner">
<section class="loginset">

    <div class="message">
        {{ __('この操作はセキュリティ保護されています。続行するには、パスワードを再入力してください。') }}
    </div>
    
    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div class="password input_set">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="" />
        </div>
        <div class="loginbtn submit">
            <x-primary-button>
                {{ __('確認する') }}
            </x-primary-button>
        </div>
    </form>

</section>
</div>

</x-login-layout>
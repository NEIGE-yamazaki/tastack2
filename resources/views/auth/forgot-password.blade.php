<x-login-layout>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

<div class="login_inner">
<section class="loginset">

    <div class="message">
        {{ __('ご登録のメールアドレスを入力いただければ、新しいパスワードを設定できるリンクをお送りします。') }}
    </div>
    
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="email input_set">
            <x-input-label for="email" :value="__('Eメール')" />
            <x-text-input id="email" class="" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="" />
        </div>
        <div class="loginbtn submit">
            <x-primary-button>
                {{ __('パスワードリセット') }}
            </x-primary-button>
        </div>
    </form>

</section>
</div>

</x-login-layout>

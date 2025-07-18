<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SocialiteController extends Controller
{
    
public function redirectToProvider($provider)
{
    return Socialite::driver($provider)
        ->scopes([
            'openid',
            'profile',
            'email',
            'https://www.googleapis.com/auth/calendar.events', // ← Googleカレンダー連携の追加
        ])
        ->with([
            'access_type' => 'offline', // refresh_token取得のため
            'prompt' => 'consent',      // 同意画面の再表示
        ])
        ->redirect();
}

    public function handleProviderCallback($provider)
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();

        // emailが一致するユーザーを取得または新規作成（保存はしない）
        $user = User::firstOrNew(['email' => $socialUser->getEmail()]);

        // 必須情報を上書き
        $user->name = $socialUser->getName() ?? $socialUser->getNickname();
        $user->password = bcrypt(Str::random(24)); // SNSログイン用のダミーパスワード
        $user->email_verified_at = now();
        $user->provider = $provider;
        $user->provider_id = $socialUser->getId();
        // カレンダー連携用トークン
        $user->google_token = $socialUser->token;
        $user->google_refresh_token = $socialUser->refreshToken;
        $user->save();

        // ログイン処理
        Auth::login($user, true);

        return redirect()->route('dashboard'); // 任意のリダイレクト先
    }
}

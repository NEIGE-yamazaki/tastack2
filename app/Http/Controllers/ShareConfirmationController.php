<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CategoryUserShare;

class ShareConfirmationController extends Controller
{
    public function confirm(Request $request)
    {
        $userId = Auth::id();
        $token = $request->query('token');

        // トークン検証
        $share = CategoryUserShare::where('shared_user_id', $userId)
            ->where('confirmation_token', $token)
            ->where('is_confirmed', false)
            ->first();

        if (!$share) {
            return redirect()->route('dashboard')->with('error', '無効な招待リンクです。');
        }

        // 承認処理
        $share->is_confirmed = true;
        $share->save();

        return redirect()->route('dashboard')->with('success', '共有タスクが承認されました！');
    }
}

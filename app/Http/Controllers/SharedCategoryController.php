<?php

// app/Http/Controllers/SharedCategoryController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CategoryUserShare;

class SharedCategoryController extends Controller
{
    public function show($token)
    {
        $share = CategoryUserShare::where('confirmation_token', $token)
            ->where('is_confirmed', true)
            ->firstOrFail();

        // ログイン中のユーザー以外がアクセスした場合は拒否
        if ($share->shared_user_id !== Auth::id()) {
            abort(403);
        }

        $category = $share->category;
        $category->load('tasks');

        return view('shared.show', compact('category'));
    }
}

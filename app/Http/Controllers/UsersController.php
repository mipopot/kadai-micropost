<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    public function index()                                 // 追加       
    {                                                       // 追加
        // ユーザ一覧をidの降順で取得
        $users = User::orderBy('id', 'desc')->paginate(10); // 追加

        // ユーザ一覧ビューでそれを表示
        return view('users.index', [                        // 追加
            'users' => $users,                              // 追加
        ]);                                                 // 追加
    }                                                       // 追加
    
    public function show($id)                               // 追加
    {                                                       // 追加
        // idの値でユーザを検索して取得
        $user = User::findOrFail($id);
        
        // 関係するモデルの件数をロード
        $user->loadRelationshipCounts();
        
        // ユーザーの投稿一覧を作成日時の降順で取得
        $microposts = $user->microposts()->orderBy('created_at', 'desc')->paginate(10);

        // ユーザ詳細ビューでそれを表示
        return view('users.show', [
            'user' => $user,
            'microposts' => $microposts,
        ]);
    }
    
    /**
     * ユーザのフォロー一覧ページを表示するアクション。
     *
     * @param  $id  ユーザのid
     * @return \Illuminate\Http\Response
     */
    public function followings($id)
    {
        // idの値でユーザを検索して取得
        $user = User::findOrFail($id);

        // 関係するモデルの件数をロード
        $user->loadRelationshipCounts();

        // ユーザのフォロー一覧を取得
        $followings = $user->followings()->paginate(10);

        // フォロー一覧ビューでそれらを表示
        return view('users.followings', [
            'user' => $user,
            'users' => $followings,
        ]);
    }

    /**
     * ユーザのフォロワー一覧ページを表示するアクション。
     *
     * @param  $id  ユーザのid
     * @return \Illuminate\Http\Response
     */
    public function followers($id)
    {
        // idの値でユーザを検索して取得
        $user = User::findOrFail($id);

        // 関係するモデルの件数をロード
        $user->loadRelationshipCounts();

        // ユーザのフォロワー一覧を取得
        $followers = $user->followers()->paginate(10);

        // フォロワー一覧ビューでそれらを表示
        return view('users.followers', [
            'user' => $user,
            'users' => $followers,
        ]);
    }
    
    
    /**
     * ログインしているユーザが
     * お気に入り登録した投稿の一覧ページを表示するアクション。
     *
     * @param  $id  投稿のid
     * @return \Illuminate\Http\Response
     */
        public function favorites($id)
    {
        // idの値でログインしているユーザを検索して取得
        $user = User::findOrFail($id);
        
        // ログインしているユーザのお気に入り登録した投稿数を読み込む
        $user->loadRelationshipCounts();

        // ログインしているユーザのお気に入り登録した投稿一覧を取得（10投稿まで1ページにおさめる）
        $favorites = $user->favoritings()->paginate(10);

        // お気に入り一覧ビューでログインしているユーザのお気に入り登録した投稿一式を表示
        return view('users.favorites', [
            'user' => $user, // ログインしているユーザ
            'microposts' => $favorites, // ログインしているユーザ複数の投稿
        ]);
    }
}
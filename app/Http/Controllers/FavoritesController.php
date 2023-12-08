<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class FavoritesController extends Controller
{
    /**
     * ログイン済みユーザーが投稿をお気に入り登録する。
     *
     * @param  $id  相手ユーザのid
     * @return \Illuminate\Http\Response
     */
    public function store($id)
    {
        // ログイン済みユーザが投稿をお気に入り登録する
        \Auth::user()->favorite($id);
        // 登録後は前のURLへリダイレクトさせる
        return back();
    }

    /**
     * ログイン済みユーザがお気に入り登録後にお気に入りを解除するアクション。
     *
     * @param  $id  相手ユーザのid
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // ログイン済みユーザがお気に入り投稿を解除する
        \Auth::user()->unfavorite($id);
        // 外した後は前のURLへリダイレクトさせる
        return back();
    }
}

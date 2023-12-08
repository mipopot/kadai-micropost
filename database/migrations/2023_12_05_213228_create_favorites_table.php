<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // favoritesテーブル（userテーブルとmicropostsテーブルの中間テーブル）
        Schema::create('favorites', function (Blueprint $table) {
            // idを格納するカラム
            $table->id();
            // 左側の外部キー
            $table->unsignedBigInteger('user_id');
            // 右側の外部キー
            $table->unsignedBigInteger('micropost_id');
            // 日時を格納するカラム
            $table->timestamps();
            
            // 左側の外部キー制約：userテーブル
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // 右側の外部キー制約：micropostsテーブル
            $table->foreign('micropost_id')->references('id')->on('microposts')->onDelete('cascade');

            // user_idとfollow_idの組み合わせの重複を許さない
            $table->unique(['user_id', 'micropost_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //ロールバックしたときなどにfavoritesテーブルを削除する
        Schema::dropIfExists('favorites');
    }
};

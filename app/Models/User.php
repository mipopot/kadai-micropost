<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // 追記
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * このユーザが所有する投稿。（ Micropostモデルとの関係を定義）
     */
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    /**
     * このユーザに関係するモデルの件数をロードする。
     */
    public function loadRelationshipCounts()
    {
        $this->loadCount(['microposts', 'followings', 'followers', 'favoritings']);
    }
    
    /**
     * このユーザがフォロー中のユーザ。（Userモデルとの関係を定義）
     */
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    /**
     * このユーザをフォロー中のユーザ。（Userモデルとの関係を定義）
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    public function follow($userId)
    {
        $exist = $this->is_following($userId);
        $its_me = $this->id == $userId;
        
        if ($exist || $its_me) {
            return false;
        } else {
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    /**
     * $userIdで指定されたユーザをアンフォローする。
     * 
     * @param  int $usereId
     * @return bool
     */
    public function unfollow($userId)
    {
        $exist = $this->is_following($userId);
        $its_me = $this->id == $userId;
        
        if ($exist && !$its_me) {
            $this->followings()->detach($userId);
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 指定された$userIdのユーザをこのユーザがフォロー中であるか調べる。フォロー中ならtrueを返す。
     * 
     * @param  int $userId
     * @return bool
     */
    public function is_following($userId)
    {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    public function feed_microposts()
    {
        // このユーザがフォロー中のユーザのidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();

        // このユーザのidもその配列に追加
        $userIds[] = $this->id;
        // それらのユーザが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
    }

    /**
     * このユーザのお気に入りの投稿。
     */
    public function favoritings()
    {
        // 多対多（Micropostモデル, favorites中間テーブル,左側の外部キー,右側の外部キー）->投稿日
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
    
        /**
     * $userIdで指定された投稿をお気に入り登録する。
     *
     * @param  int  $postId
     * @return bool
     */
    public function favorite($micropostId)
    {
        // お気に入り登録されているか確認
        $exist = $this->is_favorite($micropostId);
    
        // お気に入り登録されていない場合は
        if ($exist) {
            // お気に入り登録する
            return false;
        } else {
            // 既にお気に入りに登録されている場合は
            $this->favoritings()->attach($micropostId);
            // お気に入り登録をしない
            return true;
        }
    }
    
    /**
     * $userIdで指定された投稿をお気に入り解除する。
     * 
     * @param  int $postId
     * @return bool
     */
    public function unfavorite($micropostId)
    {
        // お気に入り登録されているか確認
        $exist = $this->is_favorite($micropostId);
        
        // 〇〇かつ××
        if ($exist) {
            // お気に入り登録されている場合は
            $this->favoritings()->detach($micropostId);
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 指定された$userIdの投稿をこのユーザがお気に入り登録済みであるか調べる。
     * お気に入り登録済みならtrueを返す。
     * 
     * @param  int $userId
     * @return bool
     */
    public function is_favorite($micropostId)
    {
        return $this->favoritings()->where('micropost_id', $micropostId)->exists();
    }
}

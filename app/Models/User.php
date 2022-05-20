<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\hasManyThrough;

// class User extends Authenticatable implements MustVerifyEmail
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function articles()
    {
        return $this->hasMany(Article::class);
        // return $this->hasMany('App\Models\Article');
    }

    public function postArticles()
    {
    //第二引数には多側のキー(外部キー)であるuser_idを指定,これによりpostArticlesメソッドで投稿したArticleを取得できる。
        return $this->hasMany(Article::class, 'user_id');
    }

    // public function likeArticles()
    // {
    //     return $this->hasMany(Like::class, 'user_id');
    // }

    public function likeArticles(): hasManyThrough
    //  Has Many Through （〜経由で多数へ紐づく）
    // hasManyThroughメソッドの第一引数は最終的にアクセスしたいモデル名で、第２引数は仲介するモデル名
    {
        return $this->hasManyThrough('App\Models\Article', //つなげる先のテーブルクラス
                                    'App\Models\Like', //中間テーブルクラス
                                    'user_id', //仲介するモデルの外部キー名
                                    'id', // 最終的に取得したいモデルのローカルキー名
                                    null, // 
                                    'article_id' // usersテーブルのローカルキー
                                    );
    }
}

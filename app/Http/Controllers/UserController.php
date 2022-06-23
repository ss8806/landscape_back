<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
    public function mypage()
    {
        $user = Auth::user();
        $posts = $user->postArticles()->orderBy('id', 'DESC')->take(5)->get();
        $likes = $user->likeArticles()->orderBy('id', 'DESC')->take(5)->get();
        return response()->json(
            [$posts, $likes]
        );
    }

    // public function posts()
    // {
    //     $user = Auth::user();
    //     $posts = $user->postArticles()->orderBy('id', 'DESC')->take(5)->get();
    //     return response()->json(
    //         $posts
    //     );
    // }

    // public function likes()
    // {
    //     $user = Auth::user();
    //     $likes = $user->likeArticles()->orderBy('id', 'DESC')->take(5)->get();
    //     return response()->json(
    //         // $likes
    //     );
    // }

    public function showPosts()
    {
        $user = Auth::user();
        $posts = $user->postArticles()->orderBy('id', 'DESC')->get();

        return response()->json(
            $posts
        );
    }

    // public function showPosts()
    // {
    //     $users = DB::select("SELECT * FROM users");
    //     // $article = DB::table('articles')
    //     // ->join('users', 'articles.user_id', '=', 'users.id')
    //     // ->select('articles.id as article_id', 'title', 'body', 'user_id','users.id', 'users.name')
    //     // ->orderBy('articles.created_at', 'desc')
    //     // ->get();
    //     return response()->json(
    //         [$users]
    //     );
    // }

    public function showLikes(){
        $user = Auth::user();
        $likes = $user->likeArticles()
        // ->whereNull('deleted_at') なくてもよい
        ->orderBy('id', 'DESC')->get();
        return response()->json(
            $likes
        );
    }

    public function editIcon(Request $request)
    {
        try{
            $user = Auth::user();
            $base64File = $request->input('icon');
            // Log::info($$base64File);
            // "data:{拡張子}"と"base64,"で区切る
            list($fileInfo, $fileData) = explode(';', $base64File);
            // 拡張子を取得
            $extension = explode('/', $fileInfo)[1];
            // $fileDataにある"base64,"を削除する
            list(, $fileData) = explode(',', $fileData);
            // base64をデコード
            $fileData = base64_decode($fileData);
            // ランダムなファイル名生成
            $fileName = md5(uniqid(rand(), true)). ".$extension";
            // AWS S3 に保存する
            Storage::disk('s3')->put($fileName, $fileData);
            // DBに保存
            $user->icon = $fileName;                
            $user->save();
            // return response()->json(compact('article'),200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(
                "error"
            );
        }
    }

    public function editName(ProfileRequest $request)
    {
        $user = Auth::user();
        $user->name = $request->input('editName');
        $user->update();
    }
    
    public function editEmail(ProfileRequest $request)
    {
        $user = Auth::user();
        $user->email = $request->input('editEmail'); 
        $user->update();
    }
    public function editPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $user = Auth::user();
        // $request->only('password', 'password_confirmation');
        $user->password = Hash::make($request->password);
        $user->save();
    }

    public function deleteUser(Request $request)
    {
        try{
            $user = Auth::user();
            $user->likeArticlesDelete();
            $user->postArticles()->delete();
            $user->delete();
            return response()->json("delete");
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(
                "error"
            );
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use App\Models\User;
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

    public function posts()
    {
        $user = Auth::user();
        $posts = $user->postArticles()->orderBy('id', 'DESC')->take(5)->get();
        return response()->json(
            $posts
        );
    }

    public function likes()
    {
        $user = Auth::user();
        $likes = $user->likeArticles()->orderBy('id', 'DESC')->take(5)->get();
        return response()->json(
            $likes
        );
    }

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

    public function showLikes()
    {
        $user = Auth::user();
        $likes = $user->likeArticles()->orderBy('id', 'DESC')->get();
        return response()->json(
            $likes
        );
    }

    // public function editAvatar(Request $request)
    // {
    //     $user = Auth::user();
         
    //      if ($request->has('editicon')) { 
    //          $fileName = $this->saveIcon($request->file('editicon')); //アップロードされた画像の情報を取得
    //          $user->icon = $fileName; // ファイル名をDBに保存
    //      }

    //      $user->save();
 
    //      return redirect()->back()
    //          ->with('success', 'プロフィールを変更しました。');
    // }

    // public function editIcon(Request $request)
    // {
    //     $user = Auth::user();
    //     $file_base64 = $request->input('icon');
    //     Log::info($file_base64);
    //     // Base64文字列をデコードしてバイナリに変換
    //     list(, $fileData) = explode(';', $file_base64);
    //     list(, $fileData) = explode(',', $fileData);
    //     $fileData = base64_decode($fileData);

    //     // ランダムなファイル名 + 拡張子
    //     $fileName = Str::random(20).'.jpg';

    //     // 保存するパスを決める
    //     $path = 'mydata/'.$fileName; 

    //     // AWS S3 に保存する
    //     Storage::disk('s3')->put($path, $fileData);
    //     // DBに保存
    //     $user->icon = $fileName;
    //     $user->save();
    //     User::where('id', $request->id)->update(['icon' => $fileName]);
    //     return redirect()->back();
    // }

    public function editIcon(ProfileRequest $request)
    {
        $user = Auth::user();
        if($file = $request->file('icon')){
            $path = 'mydata'; 
            //     // AWS S3 に保存する
            $s3_file_name = Storage::disk('s3')->put($path, $file);
            $user->icon  = $s3_file_name;
        }
        $user->update();
        return back()->with('success', 'アイコンを変更しました。');
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

        // $status = Password::reset(
        //     $request->only('email', 'password', 'password_confirmation', 'token'),
        //     function ($user) use ($request) {
        //         $user->forceFill([
        //             'password' => Hash::make($request->password),
        //             'remember_token' => Str::random(60),
        //         ])->save();        
        //     }
        // );

        $user = Auth::user();
        // $inputPass = $request->input('password');
        // $length = strlen($inputPass);

        // if($length >= 4 ){
        // $user->password = Hash::make($request->password);
        // $user->save();
        // }

        Password::reset(
            $user->forceFill([
                'password' => Hash::make($request->password)
            ])->save()
        );
        // $user->password = Hash::make($request->password);
    }
}

// $user = User::update([
//     'name' => $request->name,
//     'email' => $request->email,
//     'password' => Hash::make($request->password),
// ]);

// $status = Password::reset(
//     $request->only('email', 'password', 'password_confirmation', 'token'),
//     function ($user) use ($request) {
//         $user->forceFill([
//             'password' => Hash::make($request->password),
//             'remember_token' => Str::random(60),
//         ])->save();

//         event(new PasswordReset($user));
//     }
// );

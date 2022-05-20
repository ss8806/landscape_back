<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Support\Facades\App;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use App\Article\UseCase\IndexArticleUseCase;
use App\Article\UseCase\ShowArticleUseCase;
use App\Article\UseCase\EditArticleUseCase;
use App\Http\Requests\CreateRequest;
use App\Http\Requests\EditRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class ArticleController extends Controller
{
    // ポリシーを設定したがうまくいかなかった
        // public function __construct()
        // {
        //     $user = auth()->user();
        //     $this->middleware('can:, article')->only([
        //         'edit','update','destroy'
        //     ]);
        // }

        // public function __construct()
        // {
        // $this->authorizeResource(Article::class, 'article');
        // }
        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
    
    public function index()
    {
        $articles = Article::all();
        return response()->json(
            $articles, 200
        );
    }

    public function create()
    {
        $categories = Category::all();
        return response()->json(
            $categories, 200
        );
    }

      /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     $articles = Article::create($request->all());
    //     return response()->json(
    //         $articles, 201
    //     );
    // }

    public function store(Request $request, Article $article)
    {
        try{
            // DBに保存
            $user = Auth::user();
            $article->user_id   = $user->id;
            $article->title     = $request->input('title');
            $article->category_id     = $request->input('category_id');

            // if($file = $request->hasFile('pic1')){
            //     $path = 'mydata'; 
            //     //     // AWS S3 に保存する
            //     $s3_file_name = Storage::disk('s3')->put($path, $file);
            //     $article->pic1  = $s3_file_name;
            // }

            $base64File = $request->input('pic1');
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
                
                // データベースに保存するためのパスを返す
                // return Storage::disk('s3')->url($fileName);
                // Base64文字列をデコードしてバイナリに変換
                // list(, $fileData) = explode(';', $file_base64);
                // list(, $fileData) = explode(',', $fileData);
                // $fileData = base64_decode($file_base64);

        
                // // ランダムなファイル名 + 拡張子
                // $fileName = Str::random(20).'.png';
        
                // // 保存するパスを決める
                // $path = 'mydata/'.$fileName; 
        
                // // AWS S3 に保存する
                // Storage::disk('s3')->put($path, $fileData);
                // DBに保存
                $article->pic1 = $fileName;
                
            $article->body     = $request->input('body');
            $article->save();
            
            // Auth::user()->articles()->save($article->fill($request->all()));

            // return redirect()->route('articles')->with('success', __('Registered'));

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw ValidationException::withMessages([
                'url' => 'エラー登録できませんでした。'
            ]);
        }

    }

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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $update = [
            'title' => $request->title,
            'body' => $request->body,
            'category_id' => $request->category_id,
        ];
        $articles = Article::where('id', $id)->update($update);
        $articless = Article::all();
        if ($articles) {
            return response()->json(
                $articless
            , 200);
        } else {
            return response()->json([
                'message' => 'articles not found',
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $articles = Article::where('id', $id)->delete();
        if ($articles) {
            return response()->json([
                'message' => 'articles deleted successfully',
            ], 200);
        } else {
            return response()->json([
                'message' => 'articles not found',
            ], 404);
        }
    }

   

    public function like(Request $request, Article $article)
    {
        //モデルを結びつけている中間テーブルnoレコードを削除する。 
        $article->likes()->detach($request->user()->id);
        // モデルを結びつけている中間テーブルにレコードを挿入する。
        $article->likes()->attach($request->user()->id);
    }

    // 気になるリストから削除する処理
    public function unlike(Request $request, Article $article)
    {
        //モデルを結びつけている中間テーブルnoレコードを削除する。 
        $article->likes()->detach($request->user()->id);
    }   
}

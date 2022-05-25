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

    public function index(){
        // $articles = Article::all();
        return Article::with(['category'])->get()->toJson();
    }

    public function show(Request $request, $id)
    {  
        $article = Article::find($id);
        $cate = $article->category()->get();
        // $cate[] = $article; // 配列を追加してもreactではうまくいかない
        // return response()->json(compact('article'),200);
        // return response()->json(
        // );
        return response()->json(
            [$article] 
        );
    }

    public function c_name(Request $request, $id)
    {  
            $article = Article::find($id);
            $c_name = $article->category()->get();          
            return response()->json(
                $c_name      
            );
    }

    public function u_name(Request $request, $id)
    {  
        $article = Article::find($id);
        $u_name = $article->user()->get();          
        return response()->json(
            $u_name      
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

    public function store(Request $request, Article $article)
    {
        try{
            $user = Auth::user();
            $article->user_id   = $user->id;
            $article->title     = $request->input('title');
            $article->category_id     = $request->input('category_id');

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
                // DBに保存
                $article->pic1 = $fileName;
                
            $article->body     = $request->input('body');
            $article->save();
            // return response()->json(compact('article'),200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(
                $article
            );
        }
    }

    public function edit(Request $request, $id)
    {  
            $user = Auth::user();
            $article = Article::find($id);
            $user_id = $article->user()->get();
            $c_name = $article->category()->get();
            
            $categories = Category::orderBy('sort_no')->get();
            // return response()->json(compact('article'),200);
            return response()->json(
                [$article, $user_id, $c_name, 200]
            );

            // $categories = Category::all();
            //     return response()->json(
            //     $categories, 200
            // );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    // public function update(Request $request, Article $article, $id)
    // {
    //     try{
    //         $user = Auth::user();
    //         $article->user_id   = $user->id;
    //         $article->title     = $request->input('title');
    //         $article->category_id     = $request->input('category_id');
    //         $base64File = $request->input('pic1');
    //             // Log::info($$base64File);
    //              // "data:{拡張子}"と"base64,"で区切る
    //             list($fileInfo, $fileData) = explode(';', $base64File);
    //             // 拡張子を取得
    //             $extension = explode('/', $fileInfo)[1];
    //             // $fileDataにある"base64,"を削除する
    //             list(, $fileData) = explode(',', $fileData);
    //             // base64をデコード
    //             $fileData = base64_decode($fileData);
    //             // ランダムなファイル名生成
    //             $fileName = md5(uniqid(rand(), true)). ".$extension";
    //             // AWS S3 に保存する
    //             Storage::disk('s3')->put($fileName, $fileData);
    //             // DBに保存
    //             $article->pic1 = $fileName;
                
    //         $article->body     = $request->input('body');
    //         $article->save();
    //         // return response()->json(compact('article'),200);
    //     } catch (\Exception $e) {
    //         Log::error($e->getMessage());
    //         return response()->json(
    //             $article
    //         );
    //     }
    //     // $update = [
    //     //     'title' => $request->title,
    //     //     'body' => $request->body,
    //     //     'category_id' => $request->category_id,
    //     // ];
        
    //     // $articles = Article::where('id', $id)->update($update);
    //     // $articless = Article::all();
    //     // if ($articles) {
    //     //     return response()->json(
    //     //         $articless
    //     //     , 200);
    //     // } else {
    //     //     return response()->json([
    //     //         'message' => 'articles not found',
    //     //     ], 404);
    //     // }
    // }

    public function update(Request $request, Article $article, $id)
    {
        $article = Article::find($id);
        $article->title = $request->input('title');
        $article->category_id = $request->input('category_id');
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
            // DBに保存
        $article->pic1 = $fileName;
        $article->body = $request->input('body');        
        $article->update();
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

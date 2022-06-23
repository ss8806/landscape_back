<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\CreateRequest;
use App\Http\Requests\EditRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public function index(Request $request){
          // return Article::with(['category'])->get()->toJson();
          try{
            $query = Article::query();

            $query
            ->join('users', 'users.id', '=', 'articles.user_id')
            // ->join('users', 'deleted_at', '=', null)
            ->join('categories', 'categories.id', '=', 'articles.category_id',)
            ->select('articles.id as article_id', 'articles.updated_at as updated',
                'title','pic1','avgrate', 'body', 'category_id', 
                'users.name as u_name','categories.name as c_name',)
            ->whereNull('users.deleted_at')// ソフトデリートしたユーザーのは表示しない
            ->orderBy('articles.created_at', 'desc');

            // キーワードで絞り込み
            if ($request->filled('keyword')) {
                $keyword = '%' . $this->escape($request->input('keyword')) . '%';
                $query->where(function ($query) use ($keyword) {
                    $query->where('title', 'LIKE', $keyword);
                });
            }
            // カテゴリで絞り込み 0は真の処理になるので注意
            if ($request->filled('category') ) {
                $categoryID = $request->input('category');           
                $query->where('category_id', $categoryID);
            }

            $article = $query->paginate(6);

            $categories = Category::all();
            return response()->json(       
                [$article, $categories]
            );
        }catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(
                "error"
            );
        }
    }

    private function escape(string $value) // このクラスでのみメソッドを呼び出せる
    {
        return str_replace(
            ['\\', '%', '_'],
            ['\\\\', '\\%', '\\_'],
            $value
        );
    }

    public function show(Request $request, $id)
    {  
        $article = Article::find($id);
        $c_name = $article->category()->get();
        $u_name = $article->user()->get();           
        $initial_is_liked = $article->isLiked(Auth::user());
        $endpoint = route('like', $article); //  "http:localhst/api/article/1/like"
        return response()->json(
            [$article, $c_name, $u_name, $initial_is_liked, $endpoint]
        );
        // return response()->json(compact('article'),200);
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
                "error"
            );
        }
    }

    public function edit(Request $request, $id)
    {
        $article = Article::find($id);
        $c_name = $article->category()->get();
        $categories = Category::all();
        return response()->json(
            [$article, $c_name, $categories]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Article $article, $id)
    {
        $article = Article::find($id);
        if($article->title !== "") {
            $article->title = $request->input('title');
        }
        if($article->category_id !== 0 ){
            $article->category_id = $request->input('category_id');
        }
        if($base64File = $request->input('pic1')){
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
        }
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

    public function getReview(Request $request, Review $review, $id)
    {
        $user = Auth::user();
        $article = Article::find($id);
        $query = Review::query();

        $query
        ->select('id','reviews.article_id as article_id', 'reviews.user_id as user_id',  'reviews.rate as rate')
        ->where('article_id', $article->id)
        ->where('user_id', $user->id );

        $review = $query->get();
        return response()->json(
            $review
            // [$review, $user->id, $article->id] //デバック
        );
    }

    public function upsertReview(Request $request, Review $review, $id)
    {
        if ($search_review = Review::find($id)){
            $search_review->rate = $request->input('value');
            $search_review->update(); 
        } else {
            $user = Auth::user();
            $review->rate = $request->input('value');
            $review->user_id = $user->id;
            $review->article_id = $request->input('a_id');
            $review->save(); 
        }

        $a_id = $request->input('a_id'); // putされたパラメータ
        $article = Article::find($a_id);
            // アイディアの平均評価を算出する
            $articles = Article::query()->get();
            // 生SQL SELECT AVG(rates) as avgrate FROM reviews GROUP BY review_id
            $avg = DB::table('reviews')
            ->select(DB::raw('article_id, avg(rate) as avgrate'))
            ->where('article_id', $a_id) // article_idを絞る
            ->groupBy('article_id')
            ->get();      
            $article->avgrate = $avg[0]->avgrate;
            $article->save();
            return response()->json($article);
    }

    public function destroyReview($id)
    { 
        // $reviews = Review::where('id', $id)->delete();
        // if ($reviews) {
        //     return response()->json([
        //         'message' => 'review deleted successfully',
        //     ], 200);
        // } else {
        //     return response()->json([
        //         'message' => 'review not found',
        //     ], 500);
        // }

        $user = Auth::user();
        $article = Article::find($id);
        $query = Review::query();

        $query
        ->select('id','reviews.article_id as article_id', 'reviews.user_id as user_id',  'reviews.rate as rate')
        ->where('article_id', $article->id)
        ->where('user_id', $user->id )
        ->delete();
    }
}

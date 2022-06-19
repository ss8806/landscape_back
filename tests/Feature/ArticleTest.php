<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Article;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        
        // setUp()はテストを実行するときに最初に実行される
        parent::setUp();
        // userデータを作成
        Category::factory()->create();
        $user =  User::factory()->create([
            'name' => 'a',
            'email' => 'a@a.com',
            'password' => 'a',
        ]);
        // $article = Article::factory()->count(1)->make([
        //     'title' => 'aaaaa',
        //     'body' => 'aaaaaa',
        //     'avgrate' => '1',
        //     'user_id' => '1',
        //     'category_id' => '1',
        // ]); 
        // ログインした状態になる
        $this->actingAs($user);
    }

    public function testIndex()
    {
        $article = Article::factory()->count(1)->create([
            'id' => '1',
            'title' => 'aaaaa',
            'body' => 'aaaaaa',
            'avgrate' => '1',
            'user_id' => '1',
            'category_id' => '1',
        ]);  
        $response = $this->get('api/articles');
        $response->assertStatus(200);  
        $this->assertCount(1, $article);

        $response = $this->from('api/articles')->get('api/article/1/show');
        $response->assertStatus(200);
        
        $response = $this->from('mypage')->get(route('edit', ['id' => 1]));
        $response->assertStatus(200);
        
        $response = $this->from('api/articles')->get('api/article/1/edit');
        $response->assertStatus(200);

        $response = $this->from('/article/1/edit')->delete('api/article/1/delete');
        $response->assertStatus(200);
    }
    // ゲストログイン
    // public function testGuestCreate()
    // {//特にログインするための処理を行なっていませんので、変数$responseには未ログイン状態で記事投稿画面にアクセスした時のレスポンスが代入されます。
    //     $response = $this->get(route('create'));
    // //assertRedirectメソッドでは、引数として渡したURLにリダイレクトされたかどうかをテストします。
    //     $response->assertRedirect(route('login'));
    // }    

    public function testCreate()
    {   
        $response = $this->get('api/article/create');
        $response->assertStatus(200);
    }

    public function testStore()
    {
        $data = [
            'title' => 'テスト投稿',
            'body' => 'テスト投稿です',
            'user_id' => 1,
            'category_id' => 1,
        ];
        $response = $this->from('api/article/create')->post('api/article/store', $data);
        $response->assertStatus(200);
    }


    // public function testDestroy()
    // {
    //     $response = $this->from('/article/1/edit')->delete('/article/1/delete');
    //     $response->assertStatus(302);
    // }
}

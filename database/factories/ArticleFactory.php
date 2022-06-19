<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;



class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // $user_id = $this->faker->numberBetween(1,3);
        $category_id = $this->faker->numberBetween(1,5);
        return [
            'title' => $this->faker->realText(rand(15,15)),
            'body' => $this->faker->realText(rand(40,100)),
            'avgrate' => 2,
            'user_id' => 1,
            'category_id' => $category_id,                      
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}

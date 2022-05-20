<?php

namespace Database\Factories;

use App\Models\Article;
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
        $user_id = $this->faker->numberBetween(1,3);
        $category_id = $this->faker->numberBetween(1,5);
        return [
            'title' => $this->faker->realText(rand(15,30)),
            'body' => $this->faker->realText(rand(40,100)),
            'user_id' => $user_id,
            'category_id' => $category_id,                      
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}

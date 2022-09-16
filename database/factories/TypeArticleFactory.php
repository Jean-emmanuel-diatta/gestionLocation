<?php

namespace Database\Factories;

use App\Models\TypeArticle;
use Illuminate\Database\Eloquent\Factories\Factory;

class TypeArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model =TypeArticle::class;
    public function definition()
    {
        return [
            "nom"=>array_rand(["Immobilier","Television","Salle","Voiture"],1)
        ];
    }
}

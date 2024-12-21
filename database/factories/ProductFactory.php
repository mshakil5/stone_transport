<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        static $counter = 1;
        return [
            'name' => 'Product ' . $counter++,
            'slug' => Str::slug($this->faker->sentence),
            'short_description' => $this->faker->sentences(2, true),
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'category_id' => 1,
            'sku' => $this->faker->numberBetween(100000, 999999),
            'is_featured' => $this->faker->boolean,
            'is_recent' => $this->faker->boolean,
            'is_popular' => $this->faker->boolean,
            'is_trending' => $this->faker->boolean,
            'status' => 1,
            'feature_image' => $this->fetchRandomImage(),
        ];
    }

    public function configure()
    {
        return $this;
    }

    private function fetchRandomImage()
    {
        $randomImages = ['random.jpg', 'random2.jpg'];
        $randomImage = $randomImages[array_rand($randomImages)];

        $localImagePath = public_path('images/' . $randomImage);
        $imageName = uniqid() . '.jpg';
        $destinationPath = public_path('images/products/' . $imageName);

        copy($localImagePath, $destinationPath);

        return $imageName;
    }


}
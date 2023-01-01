<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $slide = Slider::create([
            'title' => 'Allah Adalah Pemberi Rezeki Terbaik',
            'description' => 'Ketika segala sesuatu terasa berat untuk dihadapi, berhentilah sejenak dan hitunglah berkat yang telah diberikan-Nya',
            'link' => '#',
        ]);

        // Add media to the slide
        $slide->addMedia(public_path('assets/img/hero-img-back-1.jpg'))->toMediaCollection('background_image');
        $slide->addMedia(public_path('assets/img/hero-img-1.png'))->toMediaCollection('hero_image');
    }
}

<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Seeder;

class HeroSlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $slides = [
            [
                'title' => 'Nouveautés 2025',
                'subtitle' => 'Découvrez notre nouvelle collection avec les dernières tendances mode.',
                'button_text' => 'Explorer',
                'button_link' => '/shop',
                'image' => null,
                'background_color' => 'linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%)',
                'text_color' => '#ffffff',
                'order' => 1,
                'status' => true,
            ],
            [
                'title' => 'Best Sellers',
                'subtitle' => 'Les produits les plus appréciés par nos clients.',
                'button_text' => 'Voir les tops',
                'button_link' => '/shop',
                'image' => null,
                'background_color' => 'linear-gradient(135deg, #0f3460 0%, #16213e 50%, #1a1a2e 100%)',
                'text_color' => '#ffffff',
                'order' => 2,
                'status' => true,
            ],
            [
                'title' => '-50% Promos',
                'subtitle' => 'Profitez de réductions exceptionnelles sur des centaines d\'articles.',
                'button_text' => 'Voir les promos',
                'button_link' => '/shop',
                'image' => null,
                'background_color' => 'linear-gradient(135deg, #1a1a2e 0%, #4a1942 50%, #6b2450 100%)',
                'text_color' => '#ffffff',
                'order' => 3,
                'status' => true,
            ],
        ];

        foreach ($slides as $slide) {
            HeroSlide::create($slide);
        }
    }
}

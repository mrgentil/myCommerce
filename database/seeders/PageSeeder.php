<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Page;
use App\Models\PageTranslation;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = Language::where('active', 1)->get();

        $pages = [
            [
                'slug' => 'home',
                'translations' => [
                    'en' => ['title' => 'Home', 'content' => '<p>Welcome to our store!</p>'],
                    'fr' => ['title' => 'Accueil', 'content' => '<p>Bienvenue dans notre boutique!</p>'],
                    'es' => ['title' => 'Inicio', 'content' => '<p>¡Bienvenido a nuestra tienda!</p>'],
                    'de' => ['title' => 'Startseite', 'content' => '<p>Willkommen in unserem Shop!</p>'],
                ],
            ],
            [
                'slug' => 'about',
                'translations' => [
                    'en' => ['title' => 'About Us', 'content' => '<p>Learn more about our company and mission.</p>'],
                    'fr' => ['title' => 'À propos', 'content' => '<p>En savoir plus sur notre entreprise et notre mission.</p>'],
                    'es' => ['title' => 'Sobre nosotros', 'content' => '<p>Conozca más sobre nuestra empresa y misión.</p>'],
                    'de' => ['title' => 'Über uns', 'content' => '<p>Erfahren Sie mehr über unser Unternehmen und unsere Mission.</p>'],
                ],
            ],
            [
                'slug' => 'services',
                'translations' => [
                    'en' => ['title' => 'Our Services', 'content' => '<p>Discover the services we offer.</p>'],
                    'fr' => ['title' => 'Nos services', 'content' => '<p>Découvrez les services que nous proposons.</p>'],
                    'es' => ['title' => 'Nuestros servicios', 'content' => '<p>Descubra los servicios que ofrecemos.</p>'],
                    'de' => ['title' => 'Unsere Dienstleistungen', 'content' => '<p>Entdecken Sie die Dienstleistungen, die wir anbieten.</p>'],
                ],
            ],
            [
                'slug' => 'blog',
                'translations' => [
                    'en' => ['title' => 'Blog', 'content' => '<p>Read our latest articles and news.</p>'],
                    'fr' => ['title' => 'Blog', 'content' => '<p>Lisez nos derniers articles et actualités.</p>'],
                    'es' => ['title' => 'Blog', 'content' => '<p>Lea nuestros últimos artículos y noticias.</p>'],
                    'de' => ['title' => 'Blog', 'content' => '<p>Lesen Sie unsere neuesten Artikel und Neuigkeiten.</p>'],
                ],
            ],
            [
                'slug' => 'contact',
                'translations' => [
                    'en' => ['title' => 'Contact Us', 'content' => '<p>Get in touch with us.</p><p>Email: contact@velstore.com</p>'],
                    'fr' => ['title' => 'Contact', 'content' => '<p>Contactez-nous.</p><p>Email: contact@velstore.com</p>'],
                    'es' => ['title' => 'Contacto', 'content' => '<p>Póngase en contacto con nosotros.</p><p>Email: contact@velstore.com</p>'],
                    'de' => ['title' => 'Kontakt', 'content' => '<p>Nehmen Sie Kontakt mit uns auf.</p><p>Email: contact@velstore.com</p>'],
                ],
            ],
        ];

        foreach ($pages as $pageData) {
            $page = Page::firstOrCreate(
                ['slug' => $pageData['slug']],
                ['status' => true]
            );

            foreach ($languages as $lang) {
                $trans = $pageData['translations'][$lang->code] ?? $pageData['translations']['en'];
                
                PageTranslation::firstOrCreate(
                    [
                        'page_id' => $page->id,
                        'language_code' => $lang->code,
                    ],
                    [
                        'title' => $trans['title'],
                        'content' => $trans['content'],
                    ]
                );
            }
        }
    }
}

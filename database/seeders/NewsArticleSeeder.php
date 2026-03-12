<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\NewsArticle;
use Illuminate\Database\Seeder;

class NewsArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Sigurnosni savjeti za kupce i prodavače',
                'slug' => 'sigurnosni-savjeti-za-kupce',
                'excerpt' => 'Kako provjeriti artikl, komunikaciju i rokove prije završetka transakcije.',
                'body' => "<p>Uvijek provjerite naslov, opis, fotografije i reputaciju druge strane prije završetka aukcije. Koristite poruke unutar platforme kako bi komunikacija ostala evidentirana.</p><p>Nakon završetka aukcije pratite rok za uplatu, status narudžbe i tracking podatke bez izlaska sa platforme.</p>",
            ],
            [
                'title' => 'Nova pravila za verifikovane prodavače',
                'slug' => 'nova-pravila-za-verifikovane-prodavace',
                'excerpt' => 'Pregled KYC procesa, dokumentacije i seller obaveza za profesionalne naloge.',
                'body' => "<p>Verifikovani prodavači trebaju imati potpunu dokumentaciju, uredne rokove isporuke i ažurne podatke o poslovnom subjektu kad god je to primjenjivo.</p><p>Status verifikacije utiče na nivo povjerenja, limite i operativne privilegije unutar platforme.</p>",
            ],
            [
                'title' => 'Poboljšanja pretrage i sadržajnog centra',
                'slug' => 'poboljsanja-pretrage-i-sadrzajnog-centra',
                'excerpt' => 'Objavljen je novi blok vijesti, pravne stranice i pomoćni vodiči za kupce i prodavače.',
                'body' => "<p>Nova verzija platforme donosi javni centar vijesti, uređivanje statičnih stranica iz admina i lakši pristup pravnim i pomoćnim informacijama.</p><p>Ovo omogućava bržu objavu obavijesti i konzistentnije upravljanje javnim sadržajem.</p>",
            ],
        ];

        foreach ($articles as $index => $article) {
            NewsArticle::query()->updateOrCreate(
                ['slug' => $article['slug']],
                array_merge($article, [
                    'is_published' => true,
                    'published_at' => now()->subDays(($index + 1) * 2),
                ])
            );
        }
    }
}

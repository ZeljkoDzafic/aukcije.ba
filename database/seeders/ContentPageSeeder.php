<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ContentPage;
use Illuminate\Database\Seeder;

class ContentPageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'O nama',
                'slug' => 'o-nama',
                'page_type' => 'company',
                'excerpt' => 'Aukcije.ba vodi Techentis s.p. kao odgovorni subjekt platforme.',
                'body' => "<p><strong>Aukcije.ba</strong> je digitalna aukcijska platforma koju vodi <strong>Techentis s.p.</strong>. Platforma je namijenjena sigurnom povezivanju kupaca i prodavača kroz javno licitiranje, jasna pravila objave i transparentnu komunikaciju.</p><p>Naš cilj je da korisnici u jednoj aplikaciji dobiju kvalitetnu pretragu, sigurnu kupovinu, profesionalan seller studio i operativnu podršku za cijeli tok transakcije.</p>",
            ],
            [
                'title' => 'Uslovi korištenja',
                'slug' => 'uvjeti-koristenja',
                'page_type' => 'legal',
                'excerpt' => 'Pravila registracije, objave aukcija, licitiranja, plaćanja i moderacije.',
                'body' => "<p>Korisnik je dužan da daje tačne podatke, poštuje rokove za uplatu i dostavu, te da ne koristi zabranjene obrasce ponašanja kao što su lažne ponude, manipulacija licitacijom ili objava nedopuštenog sadržaja.</p><p>Techentis s.p. može ograničiti, suspendovati ili trajno ukloniti nalog i sadržaj koji ugrožavaju sigurnost korisnika ili integritet platforme.</p>",
            ],
            [
                'title' => 'Politika privatnosti',
                'slug' => 'politika-privatnosti',
                'page_type' => 'legal',
                'excerpt' => 'Način prikupljanja, obrade, čuvanja i zaštite korisničkih podataka.',
                'body' => "<p>Techentis s.p. obrađuje podatke koji su nužni za registraciju, verifikaciju naloga, realizaciju transakcija, slanje obavijesti i zaštitu od zloupotreba.</p><p>Korisnik može zatražiti uvid, ispravku ili brisanje podataka u skladu sa zakonom i operativnim obavezama platforme.</p>",
            ],
            [
                'title' => 'Kako kupovati',
                'slug' => 'kako-kupovati',
                'page_type' => 'help',
                'excerpt' => 'Koraci od registracije do prijema artikla.',
                'body' => "<p>Prije licitiranja pregledajte opis, fotografije, ocjene prodavača i postavite pitanja putem poruka ako nešto nije dovoljno jasno.</p><p>Nakon osvajanja aukcije pratite rok za uplatu, status narudžbe i dostavne podatke direktno iz korisničkog naloga.</p>",
            ],
            [
                'title' => 'Kako prodavati',
                'slug' => 'kako-prodavati',
                'page_type' => 'help',
                'excerpt' => 'Smjernice za kvalitetnu objavu i uredno izvršenje narudžbe.',
                'body' => "<p>Prodavač treba objaviti tačan naslov, jasan opis stanja i kvalitetne fotografije artikla, uz precizno definisanu dostavu i lokaciju slanja.</p><p>Nakon završetka aukcije prodavač je dužan da na vrijeme unese tracking podatke i isporuči artikal u dogovorenom roku.</p>",
            ],
            [
                'title' => 'Sigurna kupovina',
                'slug' => 'sigurna-kupovina',
                'page_type' => 'help',
                'excerpt' => 'Savjeti za sigurnu komunikaciju, provjeru artikla i završetak transakcije.',
                'body' => "<p>Koristite poruke unutar platforme, pažljivo čitajte opis artikla i provjerite fotografije prije nego što date ponudu. Obratite pažnju na ocjene prodavača i na rokove za uplatu i dostavu.</p><p>Ako nešto nije jasno, postavite pitanje prije licitacije. Kod svake sumnje na zloupotrebu koristite prijavu sadržaja i kontakt sa podrškom.</p>",
            ],
            [
                'title' => 'Ocjene i saradnja',
                'slug' => 'ocjene-i-saradnja',
                'page_type' => 'help',
                'excerpt' => 'Kako funkcionišu ocjene, reputacija i međusobno povjerenje na platformi.',
                'body' => "<p>Ocjene pomažu kupcima i prodavačima da procijene pouzdanost druge strane. Nakon završene transakcije ostavite korektnu i preciznu ocjenu koja opisuje iskustvo saradnje.</p><p>Kontinuitet uredne isporuke, jasna komunikacija i tačni opisi artikala direktno utiču na reputaciju naloga.</p>",
            ],
            [
                'title' => 'Kontakt',
                'slug' => 'kontakt',
                'page_type' => 'company',
                'excerpt' => 'Kontakt podaci i osnovne informacije o odgovornom subjektu Techentis s.p.',
                'body' => "<p>Za opštu podršku, pravna pitanja i prijavu problema možete kontaktirati <strong>Techentis s.p.</strong> putem administrativnih kanala definisanih na platformi.</p><p>U komunikaciji navedite ID aukcije, narudžbe ili korisničkog naloga kako bi obrada bila brža i preciznija.</p>",
            ],
        ];

        foreach ($pages as $page) {
            ContentPage::query()->updateOrCreate(
                ['slug' => $page['slug']],
                array_merge($page, [
                    'is_published' => true,
                    'published_at' => now(),
                ])
            );
        }
    }
}

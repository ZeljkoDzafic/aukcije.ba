<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AuctionStatus;
use App\Enums\AuctionType;
use App\Models\Auction;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AuctionSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch sellers by email
        $mirza = User::where('email', 'mirza@seller.ba')->first();
        $amra = User::where('email', 'amra@seller.ba')->first();
        $edin = User::where('email', 'edin@seller.ba')->first();
        $selma = User::where('email', 'selma@seller.ba')->first();
        $damir = User::where('email', 'damir@seller.ba')->first();

        // Fetch categories by slug
        $catElektronika = Category::where('slug', 'mobiteli-i-tableti')->first()
            ?? Category::where('slug', 'elektronika')->first();
        $catLaptopi = Category::where('slug', 'laptopi-i-racunari')->first()
            ?? $catElektronika;
        $catTV = Category::where('slug', 'tv-i-audio')->first()
            ?? $catElektronika;
        $catGaming = Category::where('slug', 'gaming')->first()
            ?? $catElektronika;
        $catFoto = Category::where('slug', 'foto-i-video')->first()
            ?? $catElektronika;
        $catAutomobili = Category::where('slug', 'osobni-automobili')->first()
            ?? Category::where('slug', 'automobili-i-vozila')->first();
        $catMotocikli = Category::where('slug', 'motocikli')->first()
            ?? $catAutomobili;
        $catDijelovi = Category::where('slug', 'dijelovi-i-oprema')->first()
            ?? $catAutomobili;
        $catKolekcija = Category::where('slug', 'kolekcionarstvo')->first();
        $catSatovi = Category::where('slug', 'vintage-satovi')->first()
            ?? $catKolekcija;
        $catNovac = Category::where('slug', 'stari-novac-i-marke')->first()
            ?? $catKolekcija;
        $catUmjetnost = Category::where('slug', 'umetnine')->first()
            ?? $catKolekcija;
        $catNamjestaj = Category::where('slug', 'namjestaj')->first()
            ?? Category::where('slug', 'kuca-i-basta')->first();
        $catAlati = Category::where('slug', 'alati')->first()
            ?? $catNamjestaj;
        $catSportOdjeca = Category::where('slug', 'sportska-odjeca')->first()
            ?? Category::where('slug', 'odjeca-i-obuca')->first();
        $catBicikli = Category::where('slug', 'bicikli')->first()
            ?? Category::where('slug', 'sport-i-rekreacija')->first();
        $catZimski = Category::where('slug', 'zimski-sportovi')->first()
            ?? $catBicikli;
        $catLegoGames = Category::where('slug', 'lego-i-konstruktori')->first()
            ?? Category::where('slug', 'igracke-i-igre')->first();
        $catVideoIgre = Category::where('slug', 'video-igre')->first()
            ?? $catLegoGames;
        $catNakit = Category::where('slug', 'zlatni-nakit')->first()
            ?? Category::where('slug', 'nakit-i-satovi')->first();
        $catSrebro = Category::where('slug', 'srebrni-nakit')->first()
            ?? $catNakit;
        $catSatNakit = Category::where('slug', 'satovi')->first()
            ?? $catNakit;

        $now = Carbon::now();

        $auctions = [
            // ── 20 ACTIVE (end 1–7 days from now) ─────────────────────────────
            [
                'seller' => $mirza,
                'category' => $catElektronika,
                'title' => 'Samsung Galaxy S23 Ultra 256GB Phantom Black',
                'description' => 'Mobitel u odličnom stanju, korišten 6 mjeseci. Dolazi sa originalnom kutijom, punjačem i zaštitnom folijom. Bez ogrebotina, baterija drži cijeli dan.',
                'condition' => 'like_new',
                'start_price' => 800.00,
                'current_price' => 920.00,
                'buy_now_price' => 1400.00,
                'status' => AuctionStatus::Active,
                'starts_at' => $now->copy()->subHours(12),
                'ends_at' => $now->copy()->addDays(3),
                'location_city' => 'Sarajevo',
                'bids_count' => 7,
                'is_featured' => true,
            ],
            [
                'seller' => $mirza,
                'category' => $catElektronika,
                'title' => 'iPhone 14 Pro 128GB Space Black',
                'description' => 'Apple iPhone 14 Pro, kupljen u Njemačkoj. Kompletan set sa originalnom kutijom i svim kablovima. Face ID radi besprijekorno, kamera u top stanju.',
                'condition' => 'used',
                'start_price' => 700.00,
                'current_price' => 850.00,
                'buy_now_price' => 1300.00,
                'status' => AuctionStatus::Active,
                'starts_at' => $now->copy()->subDay(),
                'ends_at' => $now->copy()->addDays(2),
                'location_city' => 'Sarajevo',
                'bids_count' => 5,
                'is_featured' => true,
            ],
            [
                'seller' => $edin,
                'category' => $catLaptopi,
                'title' => 'Lenovo ThinkPad X1 Carbon Gen 10 i7-1265U 16GB 512GB',
                'description' => 'Poslovni laptop u odličnom stanju. Intel Core i7-1265U, 16GB RAM, 512GB NVMe SSD, 14" IPS ekran FHD+. Baterija drži do 10h. Dolazi sa adapterom.',
                'condition' => 'like_new',
                'start_price' => 1200.00,
                'current_price' => 1350.00,
                'buy_now_price' => 2200.00,
                'status' => AuctionStatus::Active,
                'starts_at' => $now->copy()->subHours(6),
                'ends_at' => $now->copy()->addDays(5),
                'location_city' => 'Tuzla',
                'bids_count' => 4,
                'is_featured' => false,
            ],
            [
                'seller' => $edin,
                'category' => $catTV,
                'title' => 'Samsung QLED 65" 4K Smart TV QE65Q80C 2023',
                'description' => 'Samsung QLED televizor 65 inča, model 2023. HDMI 2.1, 120Hz, Quantum Processor 4K, Gaming Hub. Korišten samo 3 mjeseca, kao nov. Originalnu kutiju nemam.',
                'condition' => 'like_new',
                'start_price' => 900.00,
                'current_price' => 1050.00,
                'buy_now_price' => 1800.00,
                'status' => AuctionStatus::Active,
                'starts_at' => $now->copy()->subHours(3),
                'ends_at' => $now->copy()->addDays(4),
                'location_city' => 'Tuzla',
                'bids_count' => 3,
                'is_featured' => false,
            ],
            [
                'seller' => $mirza,
                'category' => $catGaming,
                'title' => 'PlayStation 5 Disc Edition + 2 kontrolera + 5 igara',
                'description' => 'PS5 konzola u odličnom stanju, kupljena u januaru 2023. Dolazi sa dva DualSense kontrolera i igrama: God of War Ragnarök, Spider-Man 2, FIFA 24, GTA V, Horizon.',
                'condition' => 'used',
                'start_price' => 500.00,
                'current_price' => 620.00,
                'buy_now_price' => 950.00,
                'status' => AuctionStatus::Active,
                'starts_at' => $now->copy()->subHours(18),
                'ends_at' => $now->copy()->addDays(1),
                'location_city' => 'Sarajevo',
                'bids_count' => 9,
                'is_featured' => false,
            ],
            [
                'seller' => $amra,
                'category' => $catSatovi,
                'title' => 'Vintage sat Omega Seamaster Automatik 1968',
                'description' => 'Originalni Omega Seamaster automatik iz 1968. godine. Kućište od nehrđajućeg čelika, originalni kaiš kožni. Sat je servisiran 2021. i radi besprijekorno. Uz sat dolazi dokument servisa.',
                'condition' => 'used',
                'start_price' => 800.00,
                'current_price' => 1100.00,
                'buy_now_price' => null,
                'status' => AuctionStatus::Active,
                'starts_at' => $now->copy()->subDays(2),
                'ends_at' => $now->copy()->addDays(5),
                'location_city' => 'Banja Luka',
                'bids_count' => 11,
                'is_featured' => true,
            ],
            [
                'seller' => $amra,
                'category' => $catNovac,
                'title' => 'Kolekcija jugoslavenskih novčanica 1945-1991 (47 komada)',
                'description' => 'Kompletna kolekcija jugoslavenskih novčanica od 1945. do 1991. godine. Sve su u stanju VF do UNC. Uključuje raritetne denominacije od 1000 dinara iz 1955.',
                'condition' => 'used',
                'start_price' => 150.00,
                'current_price' => 210.00,
                'buy_now_price' => 400.00,
                'status' => AuctionStatus::Active,
                'starts_at' => $now->copy()->subHours(30),
                'ends_at' => $now->copy()->addDays(6),
                'location_city' => 'Banja Luka',
                'bids_count' => 6,
                'is_featured' => false,
            ],
            [
                'seller' => $damir,
                'category' => $catAutomobili,
                'title' => 'VW Golf 5 2.0 TDI 103kW DSG 2007 — Sarajevo',
                'description' => 'Volkswagen Golf 5 generacija, 2007. godište. Motor 2.0 TDI 103kW (140KS), DSG automatski mjenjač. Prijeđeno 185 000 km. Servisna knjiga uredna, urađen veliki servis. Registrovan do marta 2025.',
                'condition' => 'used',
                'start_price' => 5500.00,
                'current_price' => 6200.00,
                'buy_now_price' => 8500.00,
                'status' => AuctionStatus::Active,
                'starts_at' => $now->copy()->subDays(3),
                'ends_at' => $now->copy()->addDays(4),
                'location_city' => 'Mostar',
                'bids_count' => 8,
                'is_featured' => true,
            ],
            [
                'seller' => $damir,
                'category' => $catMotocikli,
                'title' => 'Honda CB500F 2019 ABS – 18 000 km',
                'description' => 'Honda CB500F model 2019. sa ABS sistemom. Motor 471cc parallel twin. Prijeđeno 18 000 km, redovno servisiran. Gume mijenjane prošle sezone. Idealan za početnike i svakodnevnu vožnju.',
                'condition' => 'used',
                'start_price' => 4000.00,
                'current_price' => 4500.00,
                'buy_now_price' => 6200.00,
                'status' => AuctionStatus::Active,
                'starts_at' => $now->copy()->subDays(1),
                'ends_at' => $now->copy()->addDays(3),
                'location_city' => 'Mostar',
                'bids_count' => 5,
                'is_featured' => false,
            ],
            [
                'seller' => $edin,
                'category' => $catFoto,
                'title' => 'Canon EOS R6 Mark II + RF 24-105mm f/4L IS USM',
                'description' => 'Canon EOS R6 Mark II tijelo sa objektivom RF 24-105mm f/4L. Okidač tijela: ~8 000 ekspozicija. Kompletan set: tijelo, objektiv, 2x LP-E6NH baterija, punjač, originalna kutija.',
                'condition' => 'like_new',
                'start_price' => 2000.00,
                'current_price' => 2300.00,
                'buy_now_price' => 3500.00,
                'status' => AuctionStatus::Active,
                'starts_at' => $now->copy()->subHours(8),
                'ends_at' => $now->copy()->addDays(7),
                'location_city' => 'Tuzla',
                'bids_count' => 6,
                'is_featured' => false,
            ],
            [
                'seller' => $selma,
                'category' => $catSportOdjeca,
                'title' => 'Nike Air Jordan 1 Retro High OG "Chicago" – vel. 43',
                'description' => 'Original Nike Air Jordan 1 Retro High OG Chicago colorway, veličina EU 43 / US 9.5. Nošene samo 2 puta, u odličnom stanju. Originalna kutija i sve papir-vrećice.',
                'condition' => 'like_new',
                'start_price' => 200.00,
                'current_price' => 280.00,
                'buy_now_price' => 500.00,
                'status' => AuctionStatus::Active,
                'starts_at' => $now->copy()->subHours(5),
                'ends_at' => $now->copy()->addDays(2),
                'location_city' => 'Zenica',
                'bids_count' => 10,
                'is_featured' => false,
            ],
            [
                'seller' => $selma,
                'category' => $catSportOdjeca,
                'title' => 'The North Face Gore-Tex jakna muška XL — zimska',
                'description' => 'The North Face muška zimska jakna sa Gore-Tex membranom, veličina XL. Boja: crna. Korišćena jednu zimu, odlično stanje. Vodootporna, vjetrovita, idealna za planinarenje.',
                'condition' => 'used',
                'start_price' => 80.00,
                'current_price' => 110.00,
                'buy_now_price' => 200.00,
                'status' => AuctionStatus::Active,
                'starts_at' => $now->copy()->subHours(10),
                'ends_at' => $now->copy()->addDays(4),
                'location_city' => 'Zenica',
                'bids_count' => 4,
                'is_featured' => false,
            ],
            [
                'seller' => $amra,
                'category' => $catUmjetnost,
                'title' => 'Uljana slika "Stari grad Mostar" — original, 60x80cm',
                'description' => 'Originalna uljana slika nepoznatog bh. autora, prikazuje Stari grad Mostar sa Starim mostom. Dimenzije 60x80 cm, rama uključena. Procijenjeno na starinu iz 1980-ih.',
                'condition' => 'used',
                'start_price' => 200.00,
                'current_price' => 350.00,
                'buy_now_price' => null,
                'status' => AuctionStatus::Active,
                'starts_at' => $now->copy()->subDays(4),
                'ends_at' => $now->copy()->addDays(3),
                'location_city' => 'Banja Luka',
                'bids_count' => 8,
                'is_featured' => false,
            ],
            [
                'seller' => $mirza,
                'category' => $catLaptopi,
                'title' => 'MacBook Pro 14" M3 Pro 18GB 512GB Space Black 2023',
                'description' => 'Apple MacBook Pro 14 inča sa M3 Pro čipom. 18GB unified memory, 512GB SSD. Koristio se za razvoj softvera, u perfektnom stanju. Garancija Apple Care+ do maja 2026.',
                'condition' => 'like_new',
                'start_price' => 1800.00,
                'current_price' => 2100.00,
                'buy_now_price' => 3200.00,
                'status' => AuctionStatus::Active,
                'starts_at' => $now->copy()->subHours(24),
                'ends_at' => $now->copy()->addDays(6),
                'location_city' => 'Sarajevo',
                'bids_count' => 7,
                'is_featured' => true,
            ],
            [
                'seller' => $edin,
                'category' => $catGaming,
                'title' => 'Xbox Series X 1TB + Game Pass Ultimate 3 mj.',
                'description' => 'Microsoft Xbox Series X 1TB konzola u odličnom stanju. Uz konzolu dobijate Game Pass Ultimate pretplatu na 3 mjeseca i jedan bežični kontroler.',
                'condition' => 'used',
                'start_price' => 400.00,
                'current_price' => 470.00,
                'buy_now_price' => 750.00,
                'status' => AuctionStatus::Active,
                'starts_at' => $now->copy()->subHours(15),
                'ends_at' => $now->copy()->addDays(2),
                'location_city' => 'Tuzla',
                'bids_count' => 5,
                'is_featured' => false,
            ],
            [
                'seller' => $amra,
                'category' => $catNakit,
                'title' => 'Zlatna narukvica 18K žuto zlato, 18cm, 8g',
                'description' => 'Narukvica od 18-karatnog žutog zlata, dužina 18 cm, težina 8 grama. Zahtijeva čišćenje. Uz narukvicu dolazi certifikat o karat-vrijednosti.',
                'condition' => 'used',
                'start_price' => 400.00,
                'current_price' => 490.00,
                'buy_now_price' => null,
                'status' => AuctionStatus::Active,
                'starts_at' => $now->copy()->subDays(2),
                'ends_at' => $now->copy()->addDays(5),
                'location_city' => 'Banja Luka',
                'bids_count' => 6,
                'is_featured' => false,
            ],
            [
                'seller' => $damir,
                'category' => $catDijelovi,
                'title' => 'Alloy felge 17" 5x112 Audi/VW/Škoda — set od 4',
                'description' => 'Set od 4 alu-felge 17 inča, rupe 5x112, offset ET35. Odgovaraju za Audi A4/A6, VW Passat/Golf 7, Škoda Superb. Bez guma. Vizuelno u dobrom stanju, manji kozmetički tragovi.',
                'condition' => 'used',
                'start_price' => 150.00,
                'current_price' => 200.00,
                'buy_now_price' => 350.00,
                'status' => AuctionStatus::Active,
                'starts_at' => $now->copy()->subHours(20),
                'ends_at' => $now->copy()->addDays(3),
                'location_city' => 'Mostar',
                'bids_count' => 4,
                'is_featured' => false,
            ],
            [
                'seller' => $selma,
                'category' => $catBicikli,
                'title' => 'Trek Marlin 7 MTB bicikl 29" vel. L — 2022',
                'description' => 'Trek Marlin 7 planinski bicikl, točkovi 29 inča, veličina L. Model 2022, korišten samo 3 sezone za vikend vožnje. Shimano Deore 12-brzina, hidrauličke disk kočnice.',
                'condition' => 'used',
                'start_price' => 600.00,
                'current_price' => 720.00,
                'buy_now_price' => 1100.00,
                'status' => AuctionStatus::Active,
                'starts_at' => $now->copy()->subHours(36),
                'ends_at' => $now->copy()->addDays(4),
                'location_city' => 'Zenica',
                'bids_count' => 5,
                'is_featured' => false,
            ],
            [
                'seller' => $amra,
                'category' => $catSrebro,
                'title' => 'Srebrni prsteni ručna izrada — set 5 komada 925',
                'description' => 'Pet srebrnih prstena ručne izrade od sterling srebra 925. Različiti dizajni: keltski, minimalistički, vintage. Veličine 17-19mm. Svaki prstен dolazi u poklon kutiji.',
                'condition' => 'new',
                'start_price' => 60.00,
                'current_price' => 85.00,
                'buy_now_price' => 150.00,
                'status' => AuctionStatus::Active,
                'starts_at' => $now->copy()->subHours(4),
                'ends_at' => $now->copy()->addDays(6),
                'location_city' => 'Banja Luka',
                'bids_count' => 3,
                'is_featured' => false,
            ],
            [
                'seller' => $edin,
                'category' => $catAlati,
                'title' => 'Bosch Professional set alata GSB 18V-55 + GDR 18V-160',
                'description' => 'Bosch Professional set: akumulatorska udarna bušilica GSB 18V-55 i udarni odvijač GDR 18V-160. Dvije baterije 2.0Ah, punjač u L-BOXX kofer. Korišten oko godinu dana.',
                'condition' => 'used',
                'start_price' => 150.00,
                'current_price' => 185.00,
                'buy_now_price' => 320.00,
                'status' => AuctionStatus::Active,
                'starts_at' => $now->copy()->subHours(7),
                'ends_at' => $now->copy()->addDays(5),
                'location_city' => 'Tuzla',
                'bids_count' => 3,
                'is_featured' => false,
            ],

            // ── 5 FINISHED (ended 1–3 days ago) ──────────────────────────────
            [
                'seller' => $mirza,
                'category' => $catElektronika,
                'title' => 'Samsung Galaxy A54 128GB Awesome Graphite',
                'description' => 'Samsung Galaxy A54 5G, 128GB memorije, 8GB RAM. Odlično stanje, korišten 4 mjeseca. Dolazi sa originalnom kutijom i punjačem.',
                'condition' => 'like_new',
                'start_price' => 300.00,
                'current_price' => 390.00,
                'buy_now_price' => 600.00,
                'status' => AuctionStatus::Finished,
                'starts_at' => $now->copy()->subDays(8),
                'ends_at' => $now->copy()->subDays(1),
                'location_city' => 'Sarajevo',
                'bids_count' => 6,
                'is_featured' => false,
            ],
            [
                'seller' => $damir,
                'category' => $catAutomobili,
                'title' => 'Toyota Yaris 1.5 Hybrid 2020 — 42 000 km',
                'description' => 'Toyota Yaris Hybrid 2020, 42 000 km. Automatski mjenjač, 3 vrata, klima, senzori parkinga. Ekonomičan gradski auto, odlično stanje.',
                'condition' => 'used',
                'start_price' => 12000.00,
                'current_price' => 14500.00,
                'buy_now_price' => null,
                'status' => AuctionStatus::Finished,
                'starts_at' => $now->copy()->subDays(10),
                'ends_at' => $now->copy()->subDays(3),
                'location_city' => 'Mostar',
                'bids_count' => 12,
                'is_featured' => false,
            ],
            [
                'seller' => $amra,
                'category' => $catSatovi,
                'title' => 'Rolex Oyster Perpetual Date 34mm Steel 1985',
                'description' => 'Rolex Oyster Perpetual Date ref. 15000, 1985. godina. Kućište nehrđajući čelik 34mm, bijeli ciferblat sa datumarom. Funkcionira besprijekorno.',
                'condition' => 'used',
                'start_price' => 3000.00,
                'current_price' => 4800.00,
                'buy_now_price' => null,
                'status' => AuctionStatus::Finished,
                'starts_at' => $now->copy()->subDays(7),
                'ends_at' => $now->copy()->subDays(2),
                'location_city' => 'Banja Luka',
                'bids_count' => 14,
                'is_featured' => false,
            ],
            [
                'seller' => $selma,
                'category' => $catZimski,
                'title' => 'Head skije Supershape i.Speed 177cm + vezovi',
                'description' => 'Head Supershape i.Speed carving skije 177cm sa originalnim Head vezovima PRX 12. Korišćene 3 sezone, u dobrom stanju. Rubovi su nabrušeni prošle sezone.',
                'condition' => 'used',
                'start_price' => 180.00,
                'current_price' => 260.00,
                'buy_now_price' => 400.00,
                'status' => AuctionStatus::Finished,
                'starts_at' => $now->copy()->subDays(9),
                'ends_at' => $now->copy()->subDays(2),
                'location_city' => 'Zenica',
                'bids_count' => 5,
                'is_featured' => false,
            ],
            [
                'seller' => $edin,
                'category' => $catLaptopi,
                'title' => 'ASUS ROG Zephyrus G14 Ryzen 9 RTX 3060 16GB',
                'description' => 'ASUS ROG Zephyrus G14 gaming laptop. AMD Ryzen 9 5900HS, RTX 3060 6GB, 16GB DDR5, 1TB NVMe. 14" QHD 120Hz displej. Dolazi sa adapterom i originlnom kutijom.',
                'condition' => 'used',
                'start_price' => 900.00,
                'current_price' => 1150.00,
                'buy_now_price' => 1700.00,
                'status' => AuctionStatus::Finished,
                'starts_at' => $now->copy()->subDays(6),
                'ends_at' => $now->copy()->subDays(1),
                'location_city' => 'Tuzla',
                'bids_count' => 8,
                'is_featured' => false,
            ],

            // ── 5 DRAFT ───────────────────────────────────────────────────────
            [
                'seller' => $mirza,
                'category' => $catElektronika,
                'title' => 'DJI Mini 4 Pro drone sa RC2 kontrolerom',
                'description' => 'DJI Mini 4 Pro dron, RC2 kontroler sa ekranom, 3 baterije, hub za punjenje. Snima 4K/60fps, težina ispod 249g. Jako malo korišten, sve u originalnoj kutiji.',
                'condition' => 'like_new',
                'start_price' => 700.00,
                'current_price' => 700.00,
                'buy_now_price' => 1200.00,
                'status' => AuctionStatus::Draft,
                'starts_at' => $now->copy()->addDays(2),
                'ends_at' => $now->copy()->addDays(9),
                'location_city' => 'Sarajevo',
                'bids_count' => 0,
                'is_featured' => false,
            ],
            [
                'seller' => $damir,
                'category' => $catAutomobili,
                'title' => 'Audi A4 2.0 TDI 110kW quattro 2016',
                'description' => 'Audi A4 B9 generacija, 2016. godište, motor 2.0 TDI 110kW quattro pogon. Prijeđeno 145 000 km. Full oprema: xenon far, navigacija, kožna sjedišta, adaptivni tempomat.',
                'condition' => 'used',
                'start_price' => 18000.00,
                'current_price' => 18000.00,
                'buy_now_price' => 25000.00,
                'status' => AuctionStatus::Draft,
                'starts_at' => $now->copy()->addDays(3),
                'ends_at' => $now->copy()->addDays(10),
                'location_city' => 'Mostar',
                'bids_count' => 0,
                'is_featured' => false,
            ],
            [
                'seller' => $amra,
                'category' => $catNovac,
                'title' => 'Srebrni novac Austro-Ugarska — 12 komada',
                'description' => 'Kolekcija od 12 srebrnih novčića iz Austro-Ugarske monarhije (1857-1916). Nominale od 1 do 5 kruna. Svi u VF ili boljoj kondicionoj klasi.',
                'condition' => 'used',
                'start_price' => 300.00,
                'current_price' => 300.00,
                'buy_now_price' => 700.00,
                'status' => AuctionStatus::Draft,
                'starts_at' => $now->copy()->addDays(1),
                'ends_at' => $now->copy()->addDays(8),
                'location_city' => 'Banja Luka',
                'bids_count' => 0,
                'is_featured' => false,
            ],
            [
                'seller' => $selma,
                'category' => $catSportOdjeca,
                'title' => 'Adidas Ultraboost 22 tenisice – vel. 40 – NOVA',
                'description' => 'Brand nove Adidas Ultraboost 22 tenisice, veličina EU 40. Nikad nošene, u originalnoj kutiji. Boja Core Black / Cloud White.',
                'condition' => 'new',
                'start_price' => 80.00,
                'current_price' => 80.00,
                'buy_now_price' => 160.00,
                'status' => AuctionStatus::Draft,
                'starts_at' => $now->copy()->addDays(4),
                'ends_at' => $now->copy()->addDays(11),
                'location_city' => 'Zenica',
                'bids_count' => 0,
                'is_featured' => false,
            ],
            [
                'seller' => $edin,
                'category' => $catVideoIgre,
                'title' => 'Nintendo Switch OLED + 8 igara + dock',
                'description' => 'Nintendo Switch OLED model, bijela verzija. Uključeno 8 igara: Zelda TotK, Mario Kart 8, Animal Crossing, Pokémon Scarlet, Kirby, Metroid Dread, Luigi\'s Mansion 3, Splatoon 3.',
                'condition' => 'used',
                'start_price' => 320.00,
                'current_price' => 320.00,
                'buy_now_price' => 580.00,
                'status' => AuctionStatus::Draft,
                'starts_at' => $now->copy()->addDays(5),
                'ends_at' => $now->copy()->addDays(12),
                'location_city' => 'Tuzla',
                'bids_count' => 0,
                'is_featured' => false,
            ],
        ];

        foreach ($auctions as $data) {
            $seller = $data['seller'];
            $category = $data['category'];

            if (! $seller || ! $category) {
                $this->command->warn("Skipping auction '{$data['title']}' — seller or category not found.");

                continue;
            }

            Auction::create([
                'seller_id' => $seller->id,
                'category_id' => $category->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'condition' => $data['condition'],
                'start_price' => $data['start_price'],
                'current_price' => $data['current_price'],
                'buy_now_price' => $data['buy_now_price'] ?? null,
                'reserve_price' => null,
                'type' => AuctionType::Standard,
                'status' => $data['status'],
                'starts_at' => $data['starts_at'],
                'ends_at' => $data['ends_at'],
                'auto_extension' => true,
                'extension_minutes' => 3,
                'location_city' => $data['location_city'],
                'shipping_available' => true,
                'shipping_cost' => 8.00,
                'views_count' => rand(20, 400),
                'bids_count' => $data['bids_count'],
                'is_featured' => $data['is_featured'],
            ]);
        }

        $this->command->info('30 auctions seeded (20 active, 5 finished, 5 draft).');
    }
}

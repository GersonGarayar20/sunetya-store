<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ElectronicsStoreSeeder extends Seeder
{
    protected Carbon $now;

    protected string $ts;

    /**
     * Electronics categories with nested-set values.
     * Parent IDs reference the IDs defined here; _lft/_rgt encode the tree.
     *
     * Tree structure:
     *   1 Root (preserved)
     *   └─ 100 Electrónica (parent=1)
     *      ├─ 101 Smartphones & Tablets
     *      │   ├─ 102 Smartphones
     *      │   └─ 103 Tablets
     *      ├─ 104 Computadoras
     *      │   ├─ 105 Laptops
     *      │   └─ 106 Desktops
     *      ├─ 107 Audio & Video
     *      │   ├─ 108 Auriculares
     *      │   └─ 109 Parlantes
     *      ├─ 110 Televisores
     *      ├─ 111 Cámaras
     *      └─ 112 Accesorios
     *
     * _lft/_rgt computed for this subtree starting at 2 (root occupies 1..26).
     */
    protected array $categories = [
        ['id' => 100, 'position' => 1, 'status' => 1, '_lft' => 2,  '_rgt' => 25, 'parent_id' => 1,   'display_mode' => 'products_and_description'],
        ['id' => 101, 'position' => 1, 'status' => 1, '_lft' => 3,  '_rgt' => 8,  'parent_id' => 100, 'display_mode' => 'products_and_description'],
        ['id' => 102, 'position' => 1, 'status' => 1, '_lft' => 4,  '_rgt' => 5,  'parent_id' => 101, 'display_mode' => 'products_and_description'],
        ['id' => 103, 'position' => 2, 'status' => 1, '_lft' => 6,  '_rgt' => 7,  'parent_id' => 101, 'display_mode' => 'products_and_description'],
        ['id' => 104, 'position' => 2, 'status' => 1, '_lft' => 9,  '_rgt' => 14, 'parent_id' => 100, 'display_mode' => 'products_and_description'],
        ['id' => 105, 'position' => 1, 'status' => 1, '_lft' => 10, '_rgt' => 11, 'parent_id' => 104, 'display_mode' => 'products_and_description'],
        ['id' => 106, 'position' => 2, 'status' => 1, '_lft' => 12, '_rgt' => 13, 'parent_id' => 104, 'display_mode' => 'products_and_description'],
        ['id' => 107, 'position' => 3, 'status' => 1, '_lft' => 15, '_rgt' => 20, 'parent_id' => 100, 'display_mode' => 'products_and_description'],
        ['id' => 108, 'position' => 1, 'status' => 1, '_lft' => 16, '_rgt' => 17, 'parent_id' => 107, 'display_mode' => 'products_and_description'],
        ['id' => 109, 'position' => 2, 'status' => 1, '_lft' => 18, '_rgt' => 19, 'parent_id' => 107, 'display_mode' => 'products_and_description'],
        ['id' => 110, 'position' => 4, 'status' => 1, '_lft' => 21, '_rgt' => 22, 'parent_id' => 100, 'display_mode' => 'products_and_description'],
        ['id' => 111, 'position' => 5, 'status' => 1, '_lft' => 23, '_rgt' => 24, 'parent_id' => 100, 'display_mode' => 'products_and_description'],
        ['id' => 112, 'position' => 6, 'status' => 1, '_lft' => 25, '_rgt' => 26, 'parent_id' => 100, 'display_mode' => 'products_and_description'],
    ];

    /** [id => [en_name, es_name, slug]] */
    protected array $categoryTranslations = [
        100 => ['Electrónica',            'Electrónica',            'electronica'],
        101 => ['Smartphones & Tablets',  'Smartphones & Tablets',  'smartphones-tablets'],
        102 => ['Smartphones',            'Smartphones',            'smartphones'],
        103 => ['Tablets',                'Tablets',                'tablets'],
        104 => ['Computers',              'Computadoras',           'computadoras'],
        105 => ['Laptops',                'Laptops',                'laptops'],
        106 => ['Desktops',               'Desktops',               'desktops'],
        107 => ['Audio & Video',          'Audio & Video',          'audio-video'],
        108 => ['Headphones',             'Auriculares',            'auriculares'],
        109 => ['Speakers',               'Parlantes',              'parlantes'],
        110 => ['TVs',                    'Televisores',            'televisores'],
        111 => ['Cameras',                'Cámaras',                'camaras'],
        112 => ['Accessories',            'Accesorios',             'accesorios'],
    ];

    /**
     * Products: [sku, name_en, name_es, category_id, price, weight, short_desc_en, desc_en]
     * All products are type=simple, attribute_family_id=1 (default).
     */
    protected array $products = [
        // id=1001 Smartphones
        [
            'id' => 1001, 'sku' => 'ELEC-IPHONE15PRO', 'category_id' => 102,
            'name_en' => 'iPhone 15 Pro', 'name_es' => 'iPhone 15 Pro',
            'price' => 999.00, 'weight' => 0.19,
            'short_en' => '6.1" Super Retina XDR display, A17 Pro chip, 48MP camera system.',
            'short_es' => 'Pantalla Super Retina XDR de 6,1", chip A17 Pro, sistema de cámara de 48 MP.',
            'desc_en' => 'The iPhone 15 Pro features a titanium design, A17 Pro chip with 6-core GPU, 48MP main camera, and Action button.',
            'desc_es' => 'El iPhone 15 Pro tiene un diseño de titanio, chip A17 Pro con GPU de 6 núcleos, cámara principal de 48 MP y botón de Acción.',
        ],
        [
            'id' => 1002, 'sku' => 'ELEC-GALAXY-S24', 'category_id' => 102,
            'name_en' => 'Samsung Galaxy S24', 'name_es' => 'Samsung Galaxy S24',
            'price' => 849.00, 'weight' => 0.17,
            'short_en' => '6.2" Dynamic AMOLED display, Snapdragon 8 Gen 3, 50MP triple camera.',
            'short_es' => 'Pantalla Dynamic AMOLED de 6,2", Snapdragon 8 Gen 3, triple cámara de 50 MP.',
            'desc_en' => 'Samsung Galaxy S24 with AI-powered features, Snapdragon 8 Gen 3 processor, and a brilliant 6.2-inch display.',
            'desc_es' => 'Samsung Galaxy S24 con funciones impulsadas por IA, procesador Snapdragon 8 Gen 3 y una brillante pantalla de 6,2 pulgadas.',
        ],
        // Tablets
        [
            'id' => 1003, 'sku' => 'ELEC-IPAD-AIR-M2', 'category_id' => 103,
            'name_en' => 'iPad Air M2', 'name_es' => 'iPad Air M2',
            'price' => 599.00, 'weight' => 0.46,
            'short_en' => '11" Liquid Retina display, M2 chip, 12MP cameras, all-day battery.',
            'short_es' => 'Pantalla Liquid Retina de 11", chip M2, cámaras de 12 MP, batería para todo el día.',
            'desc_en' => 'iPad Air with M2 chip delivers up to 60% faster performance. Features a stunning 11-inch Liquid Retina display.',
            'desc_es' => 'El iPad Air con chip M2 ofrece hasta un 60% más de rendimiento. Cuenta con una impresionante pantalla Liquid Retina de 11 pulgadas.',
        ],
        [
            'id' => 1004, 'sku' => 'ELEC-TAB-S9', 'category_id' => 103,
            'name_en' => 'Samsung Galaxy Tab S9', 'name_es' => 'Samsung Galaxy Tab S9',
            'price' => 699.00, 'weight' => 0.50,
            'short_en' => '11" Dynamic AMOLED 2X, Snapdragon 8 Gen 2, IP68 water resistance.',
            'short_es' => 'AMOLED 2X dinámico de 11", Snapdragon 8 Gen 2, resistencia al agua IP68.',
            'desc_en' => 'Galaxy Tab S9 with an immersive 11-inch Dynamic AMOLED 2X display, powerful Snapdragon 8 Gen 2, and S Pen included.',
            'desc_es' => 'Galaxy Tab S9 con pantalla Dynamic AMOLED 2X de 11 pulgadas, potente Snapdragon 8 Gen 2 y S Pen incluido.',
        ],
        // Laptops
        [
            'id' => 1005, 'sku' => 'ELEC-MACBOOK-AIR-M3', 'category_id' => 105,
            'name_en' => 'MacBook Air M3', 'name_es' => 'MacBook Air M3',
            'price' => 1099.00, 'weight' => 1.24,
            'short_en' => '13.6" Liquid Retina, M3 chip, 18h battery, 8GB RAM, 256GB SSD.',
            'short_es' => 'Liquid Retina de 13,6", chip M3, 18 h de batería, 8 GB de RAM, SSD de 256 GB.',
            'desc_en' => 'MacBook Air with M3 chip is incredibly thin and light with up to 18 hours of battery life and a stunning Liquid Retina display.',
            'desc_es' => 'MacBook Air con chip M3 es increíblemente delgada y liviana con hasta 18 horas de batería y una impresionante pantalla Liquid Retina.',
        ],
        [
            'id' => 1006, 'sku' => 'ELEC-DELL-XPS15', 'category_id' => 105,
            'name_en' => 'Dell XPS 15', 'name_es' => 'Dell XPS 15',
            'price' => 1299.00, 'weight' => 1.86,
            'short_en' => '15.6" OLED display, Intel Core i7, 16GB RAM, 512GB SSD, NVIDIA RTX 4060.',
            'short_es' => 'Pantalla OLED de 15,6", Intel Core i7, 16 GB de RAM, SSD de 512 GB, NVIDIA RTX 4060.',
            'desc_en' => 'Dell XPS 15 with a stunning OLED display, powerful Intel Core i7 processor, and NVIDIA RTX 4060 graphics for creators and professionals.',
            'desc_es' => 'Dell XPS 15 con una impresionante pantalla OLED, potente procesador Intel Core i7 y gráficos NVIDIA RTX 4060 para creadores y profesionales.',
        ],
        // Desktops
        [
            'id' => 1007, 'sku' => 'ELEC-HP-PAVILION-DT', 'category_id' => 106,
            'name_en' => 'HP Pavilion Desktop', 'name_es' => 'HP Pavilion Desktop',
            'price' => 799.00, 'weight' => 7.00,
            'short_en' => 'Intel Core i5, 16GB RAM, 512GB SSD, Intel UHD Graphics 730.',
            'short_es' => 'Intel Core i5, 16 GB de RAM, SSD de 512 GB, Intel UHD Graphics 730.',
            'desc_en' => 'HP Pavilion Desktop delivers everyday performance for home and office use with Intel Core i5 and ample storage.',
            'desc_es' => 'HP Pavilion Desktop ofrece rendimiento diario para uso doméstico y de oficina con Intel Core i5 y amplio almacenamiento.',
        ],
        // Headphones
        [
            'id' => 1008, 'sku' => 'ELEC-SONY-WH1000XM5', 'category_id' => 108,
            'name_en' => 'Sony WH-1000XM5', 'name_es' => 'Sony WH-1000XM5',
            'price' => 349.00, 'weight' => 0.25,
            'short_en' => 'Industry-leading noise canceling, 30h battery, multipoint connection.',
            'short_es' => 'Cancelación de ruido líder en la industria, batería de 30 h, conexión multipunto.',
            'desc_en' => 'Sony WH-1000XM5 headphones feature industry-leading noise canceling, 30-hour battery life, and exceptional sound quality.',
            'desc_es' => 'Los auriculares Sony WH-1000XM5 cuentan con cancelación de ruido líder en la industria, 30 horas de batería y calidad de sonido excepcional.',
        ],
        [
            'id' => 1009, 'sku' => 'ELEC-AIRPODS-PRO2', 'category_id' => 108,
            'name_en' => 'AirPods Pro 2nd Gen', 'name_es' => 'AirPods Pro 2.ª generación',
            'price' => 249.00, 'weight' => 0.06,
            'short_en' => 'Active Noise Cancellation, Transparency mode, H2 chip, 30h total battery.',
            'short_es' => 'Cancelación activa de ruido, modo Transparencia, chip H2, 30 h de batería total.',
            'desc_en' => 'AirPods Pro with H2 chip deliver twice the active noise cancellation, Adaptive Transparency, and Personalized Spatial Audio.',
            'desc_es' => 'Los AirPods Pro con chip H2 ofrecen el doble de cancelación activa de ruido, Transparencia adaptable y Audio espacial personalizado.',
        ],
        // Speakers
        [
            'id' => 1010, 'sku' => 'ELEC-JBL-CHARGE5', 'category_id' => 109,
            'name_en' => 'JBL Charge 5', 'name_es' => 'JBL Charge 5',
            'price' => 179.00, 'weight' => 0.96,
            'short_en' => 'Waterproof Bluetooth speaker, 20h playtime, built-in power bank.',
            'short_es' => 'Altavoz Bluetooth resistente al agua, 20 h de reproducción, banco de energía integrado.',
            'desc_en' => 'JBL Charge 5 delivers powerful JBL Pro Sound, 20-hour battery life, and can charge your devices via USB.',
            'desc_es' => 'JBL Charge 5 ofrece potente sonido JBL Pro, 20 horas de batería y puede cargar tus dispositivos a través de USB.',
        ],
        [
            'id' => 1011, 'sku' => 'ELEC-BOSE-SOUNDLINK-MAX', 'category_id' => 109,
            'name_en' => 'Bose SoundLink Max', 'name_es' => 'Bose SoundLink Max',
            'price' => 329.00, 'weight' => 1.29,
            'short_en' => 'Premium portable Bluetooth speaker, 20h battery, IP67 waterproof.',
            'short_es' => 'Altavoz Bluetooth portátil premium, batería de 20 h, resistente al agua IP67.',
            'desc_en' => 'Bose SoundLink Max delivers the biggest, boldest sound from Bose in a portable speaker with up to 20 hours of battery life.',
            'desc_es' => 'Bose SoundLink Max ofrece el sonido más grande y potente de Bose en un altavoz portátil con hasta 20 horas de batería.',
        ],
        // TVs
        [
            'id' => 1012, 'sku' => 'ELEC-SAMSUNG-65QLED', 'category_id' => 110,
            'name_en' => 'Samsung 65" QLED 4K TV', 'name_es' => 'Samsung QLED 4K 65" TV',
            'price' => 1199.00, 'weight' => 22.80,
            'short_en' => '65" QLED 4K, Quantum HDR, 120Hz, Smart TV with Tizen OS.',
            'short_es' => 'QLED 4K de 65", Quantum HDR, 120 Hz, Smart TV con Tizen OS.',
            'desc_en' => 'Samsung QLED 4K TV with Quantum Dot technology delivers breathtaking 4K picture quality with vibrant colors and deep contrast.',
            'desc_es' => 'El TV Samsung QLED 4K con tecnología Quantum Dot ofrece una calidad de imagen 4K impresionante con colores vibrantes y contraste profundo.',
        ],
        [
            'id' => 1013, 'sku' => 'ELEC-LG-OLED-C3-55', 'category_id' => 110,
            'name_en' => 'LG OLED C3 55"', 'name_es' => 'LG OLED C3 55"',
            'price' => 1499.00, 'weight' => 17.20,
            'short_en' => '55" OLED evo, α9 AI Processor 4K Gen6, 120Hz, Dolby Vision IQ.',
            'short_es' => 'OLED evo de 55", procesador IA α9 4K Gen6, 120 Hz, Dolby Vision IQ.',
            'desc_en' => 'LG OLED C3 with self-lit OLED pixels delivers perfect black, over a billion colors, and cinema-quality picture with Dolby Vision.',
            'desc_es' => 'LG OLED C3 con píxeles OLED autoiluminados ofrece negro perfecto, más de mil millones de colores y calidad de imagen cinematográfica con Dolby Vision.',
        ],
        // Cameras
        [
            'id' => 1014, 'sku' => 'ELEC-SONY-A7IV', 'category_id' => 111,
            'name_en' => 'Sony Alpha A7 IV', 'name_es' => 'Sony Alpha A7 IV',
            'price' => 2499.00, 'weight' => 0.66,
            'short_en' => '33MP full-frame sensor, 4K 60fps video, 759-point AF, 10fps burst.',
            'short_es' => 'Sensor full-frame de 33 MP, video 4K 60 fps, AF de 759 puntos, ráfaga de 10 fps.',
            'desc_en' => 'Sony A7 IV is a versatile full-frame mirrorless camera with 33MP sensor, advanced autofocus, and professional-grade video capabilities.',
            'desc_es' => 'Sony A7 IV es una cámara sin espejo full-frame versátil con sensor de 33 MP, autoenfoque avanzado y capacidades de video de grado profesional.',
        ],
        [
            'id' => 1015, 'sku' => 'ELEC-CANON-EOS-R50', 'category_id' => 111,
            'name_en' => 'Canon EOS R50', 'name_es' => 'Canon EOS R50',
            'price' => 799.00, 'weight' => 0.38,
            'short_en' => '24.2MP APS-C sensor, 4K video, DIGIC X processor, dual pixel CMOS AF.',
            'short_es' => 'Sensor APS-C de 24,2 MP, video 4K, procesador DIGIC X, AF CMOS de píxel dual.',
            'desc_en' => 'Canon EOS R50 is a compact mirrorless camera perfect for content creators with 24.2MP sensor and advanced subject tracking.',
            'desc_es' => 'Canon EOS R50 es una cámara sin espejo compacta perfecta para creadores de contenido con sensor de 24,2 MP y seguimiento avanzado de sujetos.',
        ],
        // Accessories
        [
            'id' => 1016, 'sku' => 'ELEC-ANKER-USB-HUB', 'category_id' => 112,
            'name_en' => 'Anker 7-in-1 USB-C Hub', 'name_es' => 'Hub USB-C 7 en 1 Anker',
            'price' => 49.00, 'weight' => 0.10,
            'short_en' => '4K HDMI, 3x USB-A, SD/microSD reader, 100W Power Delivery.',
            'short_es' => 'HDMI 4K, 3x USB-A, lector SD/microSD, Power Delivery de 100 W.',
            'desc_en' => 'Anker 7-in-1 USB-C Hub expands your laptop with HDMI, USB ports, and SD card readers while charging at up to 100W.',
            'desc_es' => 'El Hub USB-C 7 en 1 de Anker expande tu laptop con HDMI, puertos USB y lectores de tarjetas SD mientras carga a hasta 100 W.',
        ],
        [
            'id' => 1017, 'sku' => 'ELEC-LOGITECH-MXM3', 'category_id' => 112,
            'name_en' => 'Logitech MX Master 3S', 'name_es' => 'Logitech MX Master 3S',
            'price' => 99.00, 'weight' => 0.14,
            'short_en' => '8K DPI sensor, MagSpeed scroll wheel, Quiet Click buttons, 70-day battery.',
            'short_es' => 'Sensor de 8K DPI, rueda de desplazamiento MagSpeed, botones silenciosos, batería de 70 días.',
            'desc_en' => 'Logitech MX Master 3S is the ultimate mouse for productivity with ultra-fast MagSpeed scrolling and an 8K DPI sensor.',
            'desc_es' => 'Logitech MX Master 3S es el ratón definitivo para la productividad con desplazamiento MagSpeed ultrarrápido y sensor de 8K DPI.',
        ],
        [
            'id' => 1018, 'sku' => 'ELEC-SAMSUNG-SSD-1TB', 'category_id' => 112,
            'name_en' => 'Samsung 990 Pro SSD 1TB', 'name_es' => 'SSD Samsung 990 Pro 1TB',
            'price' => 89.00, 'weight' => 0.01,
            'short_en' => 'NVMe M.2 SSD, up to 7450 MB/s read, PCIe 4.0, 1TB.',
            'short_es' => 'SSD NVMe M.2, hasta 7450 MB/s de lectura, PCIe 4.0, 1 TB.',
            'desc_en' => 'Samsung 990 Pro delivers blazing fast sequential read speeds up to 7,450 MB/s for gaming and professional workloads.',
            'desc_es' => 'Samsung 990 Pro ofrece velocidades de lectura secuencial ultrarrápidas de hasta 7450 MB/s para juegos y cargas de trabajo profesionales.',
        ],
        [
            'id' => 1019, 'sku' => 'ELEC-APPLE-WATCH-S9', 'category_id' => 112,
            'name_en' => 'Apple Watch Series 9', 'name_es' => 'Apple Watch Series 9',
            'price' => 399.00, 'weight' => 0.05,
            'short_en' => 'S9 chip, Always-On Retina display, Double Tap gesture, 18h battery.',
            'short_es' => 'Chip S9, pantalla Retina Always-On, gesto Double Tap, batería de 18 h.',
            'desc_en' => 'Apple Watch Series 9 features the new S9 chip, Double Tap gesture, and a brighter Always-On Retina display.',
            'desc_es' => 'Apple Watch Series 9 cuenta con el nuevo chip S9, gesto Double Tap y una pantalla Retina Always-On más brillante.',
        ],
        [
            'id' => 1020, 'sku' => 'ELEC-XIAOMI-BAND8', 'category_id' => 112,
            'name_en' => 'Xiaomi Smart Band 8', 'name_es' => 'Xiaomi Smart Band 8',
            'price' => 49.00, 'weight' => 0.03,
            'short_en' => 'AMOLED display, 150+ workout modes, 16-day battery, SpO2 monitoring.',
            'short_es' => 'Pantalla AMOLED, más de 150 modos de entrenamiento, batería de 16 días, monitoreo de SpO2.',
            'desc_en' => 'Xiaomi Smart Band 8 tracks your health and fitness with an AMOLED display, 150+ workout modes, and up to 16 days of battery life.',
            'desc_es' => 'Xiaomi Smart Band 8 rastrea tu salud y condición física con pantalla AMOLED, más de 150 modos de entrenamiento y hasta 16 días de batería.',
        ],
    ];

    public function run(): void
    {
        $this->now = Carbon::now();
        $this->ts  = $this->now->format('Y-m-d H:i:s');

        $this->command->info('Registering Spanish locale...');
        $this->seedLocaleAndChannel();

        $this->command->info('Seeding electronics categories...');
        $this->seedCategories();

        $this->command->info('Seeding electronics products...');
        $this->seedProducts();

        $this->command->info('Seeding attribute values...');
        $this->seedAttributeValues();

        $this->command->info('Seeding product_flat...');
        $this->seedProductFlat();

        $this->command->info('Seeding product channels...');
        $this->seedProductChannels();

        $this->command->info('Seeding product categories...');
        $this->seedProductCategories();

        $this->command->info('Seeding inventories...');
        $this->seedInventories();

        $this->command->info('Done! Electronics store seeded successfully.');
    }

    // -------------------------------------------------------------------------

    protected function seedLocaleAndChannel(): void
    {
        $localeId = DB::table('locales')->where('code', 'es')->value('id');

        if (! $localeId) {
            $localeId = DB::table('locales')->insertGetId([
                'code'       => 'es',
                'name'       => 'Spanish',
                'direction'  => 'ltr',
                'logo_path'  => null,
                'created_at' => $this->ts,
                'updated_at' => $this->ts,
            ]);
        }

        $exists = DB::table('channel_locales')
            ->where('channel_id', 1)
            ->where('locale_id', $localeId)
            ->exists();

        if (! $exists) {
            DB::table('channel_locales')->insert([
                'channel_id' => 1,
                'locale_id'  => $localeId,
            ]);
        }
    }

    protected function seedCategories(): void
    {
        // Remove only the electronics categories if they exist (safe re-run)
        $ids = array_column($this->categories, 'id');
        DB::table('category_translations')->whereIn('category_id', $ids)->delete();
        DB::table('categories')->whereIn('id', $ids)->delete();

        $rows = [];
        foreach ($this->categories as $cat) {
            $rows[] = array_merge($cat, [
                'logo_path'   => null,
                'banner_path' => null,
                'additional'  => null,
                'created_at'  => $this->ts,
                'updated_at'  => $this->ts,
            ]);
        }
        DB::table('categories')->insert($rows);

        // Update root _rgt to accommodate the new subtree
        DB::table('categories')->where('id', 1)->update(['_rgt' => 28]);

        $translationRows = [];
        foreach ($this->categoryTranslations as $catId => [$nameEn, $nameEs, $slug]) {
            foreach (['en' => $nameEn, 'es' => $nameEs] as $locale => $name) {
                $translationRows[] = [
                    'category_id'      => $catId,
                    'name'             => $name,
                    'slug'             => $locale === 'en' ? $slug : $slug,
                    'url_path'         => $slug,
                    'description'      => '',
                    'meta_title'       => $name,
                    'meta_description' => '',
                    'meta_keywords'    => '',
                    'locale'           => $locale,
                ];
            }
        }
        DB::table('category_translations')->insert($translationRows);
    }

    protected function seedProducts(): void
    {
        $ids = array_column($this->products, 'id');
        DB::table('products')->whereIn('id', $ids)->delete();

        $rows = [];
        foreach ($this->products as $p) {
            $rows[] = [
                'id'                  => $p['id'],
                'sku'                 => $p['sku'],
                'type'                => 'simple',
                'parent_id'           => null,
                'attribute_family_id' => 1,
                'additional'          => null,
                'created_at'          => $this->ts,
                'updated_at'          => $this->ts,
            ];
        }
        DB::table('products')->insert($rows);
    }

    protected function seedAttributeValues(): void
    {
        $ids = array_column($this->products, 'id');
        DB::table('product_attribute_values')->whereIn('product_id', $ids)->delete();

        // Load attributes once
        $attrs = DB::table('attributes')
            ->whereIn('code', ['name', 'url_key', 'short_description', 'description',
                'meta_title', 'meta_keywords', 'meta_description',
                'price', 'weight', 'status', 'new', 'featured', 'visible_individually'])
            ->get()
            ->keyBy('code');

        $typeMap = [
            'text'     => 'text_value',
            'textarea' => 'text_value',
            'price'    => 'float_value',
            'boolean'  => 'boolean_value',
            'select'   => 'integer_value',
        ];

        $nullCols = [
            'text_value' => null, 'float_value' => null, 'boolean_value' => null,
            'integer_value' => null, 'datetime_value' => null, 'date_value' => null,
            'json_value' => null,
        ];

        $rows = [];
        $seen = [];

        foreach (['en', 'es'] as $locale) {
            foreach ($this->products as $p) {
                $pid = $p['id'];
                $urlKey = Str::slug($p['sku']);

                $localeData = [
                    'name'             => $locale === 'en' ? $p['name_en'] : $p['name_es'],
                    'url_key'          => $urlKey . ($locale === 'es' ? '-es' : ''),
                    'short_description' => $locale === 'en' ? $p['short_en'] : $p['short_es'],
                    'description'      => $locale === 'en' ? $p['desc_en'] : $p['desc_es'],
                    'meta_title'       => $locale === 'en' ? $p['name_en'] : $p['name_es'],
                    'meta_keywords'    => $locale === 'en' ? $p['name_en'] : $p['name_es'],
                    'meta_description' => $locale === 'en' ? $p['short_en'] : $p['short_es'],
                ];

                $sharedData = [
                    'price'              => $p['price'],
                    'weight'             => $p['weight'],
                    'status'             => 1,
                    'new'                => 1,
                    'featured'           => 1,
                    'visible_individually' => 1,
                ];

                // Locale-specific attributes
                foreach ($localeData as $code => $value) {
                    $attr = $attrs->get($code);
                    if (! $attr) {
                        continue;
                    }
                    $channel  = $attr->value_per_channel ? 'default' : null;
                    $attrLocale = $attr->value_per_locale ? $locale : null;
                    $uniqueId = implode('|', array_filter([$channel, $attrLocale, $pid, $attr->id], fn ($v) => $v !== null));

                    if (isset($seen[$uniqueId])) {
                        continue;
                    }
                    $seen[$uniqueId] = true;

                    $col = $typeMap[$attr->type] ?? 'text_value';
                    $rows[] = array_merge($nullCols, [
                        'attribute_id' => $attr->id,
                        'product_id'   => $pid,
                        'channel'      => $channel,
                        'locale'       => $attrLocale,
                        'unique_id'    => $uniqueId,
                        $col           => $value,
                    ]);
                }

                // Shared (non-locale-specific) attributes — insert only once (on 'en' pass)
                if ($locale === 'en') {
                    foreach ($sharedData as $code => $value) {
                        $attr = $attrs->get($code);
                        if (! $attr) {
                            continue;
                        }
                        $channel  = $attr->value_per_channel ? 'default' : null;
                        $attrLocale = null;
                        $uniqueId = implode('|', array_filter([$channel, $attrLocale, $pid, $attr->id], fn ($v) => $v !== null));

                        if (isset($seen[$uniqueId])) {
                            continue;
                        }
                        $seen[$uniqueId] = true;

                        $col = $typeMap[$attr->type] ?? 'text_value';
                        $rows[] = array_merge($nullCols, [
                            'attribute_id' => $attr->id,
                            'product_id'   => $pid,
                            'channel'      => $channel,
                            'locale'       => null,
                            'unique_id'    => $uniqueId,
                            $col           => $value,
                        ]);
                    }
                }
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('product_attribute_values')->insert($chunk);
        }
    }

    protected function seedProductFlat(): void
    {
        $ids = array_column($this->products, 'id');
        DB::table('product_flat')->whereIn('product_id', $ids)->delete();

        $rows = [];
        foreach (['en', 'es'] as $locale) {
            foreach ($this->products as $p) {
                $urlKey = Str::slug($p['sku']) . ($locale === 'es' ? '-es' : '');
                $rows[] = [
                    'sku'                 => $p['sku'],
                    'type'                => 'simple',
                    'product_number'      => null,
                    'name'                => $locale === 'en' ? $p['name_en'] : $p['name_es'],
                    'short_description'   => $locale === 'en' ? $p['short_en'] : $p['short_es'],
                    'description'         => $locale === 'en' ? $p['desc_en'] : $p['desc_es'],
                    'url_key'             => $urlKey,
                    'new'                 => 1,
                    'featured'            => 1,
                    'status'              => 1,
                    'visible_individually' => 1,
                    'meta_title'          => $locale === 'en' ? $p['name_en'] : $p['name_es'],
                    'meta_keywords'       => $locale === 'en' ? $p['name_en'] : $p['name_es'],
                    'meta_description'    => $locale === 'en' ? $p['short_en'] : $p['short_es'],
                    'price'               => $p['price'],
                    'special_price'       => null,
                    'special_price_from'  => null,
                    'special_price_to'    => null,
                    'weight'              => $p['weight'],
                    'locale'              => $locale,
                    'channel'             => 'default',
                    'product_id'          => $p['id'],
                    'parent_id'           => null,
                    'attribute_family_id' => 1,
                    'created_at'          => $this->ts,
                    'updated_at'          => $this->ts,
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('product_flat')->insert($chunk);
        }
    }

    protected function seedProductChannels(): void
    {
        $ids = array_column($this->products, 'id');
        DB::table('product_channels')->whereIn('product_id', $ids)->delete();

        $rows = array_map(fn ($p) => ['product_id' => $p['id'], 'channel_id' => 1], $this->products);
        DB::table('product_channels')->insert($rows);
    }

    protected function seedProductCategories(): void
    {
        $ids = array_column($this->products, 'id');
        DB::table('product_categories')->whereIn('product_id', $ids)->delete();

        $rows = array_map(fn ($p) => ['product_id' => $p['id'], 'category_id' => $p['category_id']], $this->products);
        DB::table('product_categories')->insert($rows);
    }

    protected function seedInventories(): void
    {
        $ids = array_column($this->products, 'id');
        DB::table('product_inventories')->whereIn('product_id', $ids)->delete();

        $rows = array_map(fn ($p) => [
            'product_id'          => $p['id'],
            'inventory_source_id' => 1,
            'qty'                 => 50,
        ], $this->products);

        DB::table('product_inventories')->insert($rows);
    }
}

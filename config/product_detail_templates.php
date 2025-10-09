<?php

/**
 * Product Detail Templates Configuration - 100+ Kategori
 * 
 * Hibrit Yaklaşım: Benzer kategoriler birleştirildi, özel kategoriler ayrı tutuldu
 * Toplam ~35 Template ile 106 kategori kapsanıyor
 * 
 * Template Yapısı:
 * - keywords: Kategori tespiti için anahtar kelimeler (Türkçe + İngilizce)
 * - use_ai: true ise AI kullanılır, false ise statik template
 * - ai_prompt_template: AI için özel prompt (opsiyonel)
 * - ai_description, features, usage_scenarios, specifications, pros_cons, recommendations
 */

return [

    /*
    |--------------------------------------------------------------------------
    | GRUP 1: MODA & AKSESUAR
    |--------------------------------------------------------------------------
    */

    'giyim' => [
        'keywords' => [
            // Kadın
            'kadın', 'bayan', 'women', 'kadın giyim',
            // Erkek
            'erkek', 'men', 'erkek giyim',
            // Genel
            'tişört', 'gömlek', 'bluz', 'shirt', 'tshirt',
            'pantolon', 'kot', 'jean', 'denim', 'pants',
            'elbise', 'dress', 'tulum', 'jumpsuit',
            'etek', 'skirt', 'şort', 'shorts',
            'ceket', 'jacket', 'yelek', 'hırka',
            'eşofman', 'sportswear', 'spor giyim',
            'iç giyim', 'iç çamaşır', 'underwear', 'sütyen', 'boxer',
            'takım elbise', 'suit', 'blazer', 'smokin'
        ],
        'use_ai' => true,
        'ai_prompt_template' => "Giyim Ürünü: {name}
Marka: {brand}
Kategori: Giyim
Fiyat: {price} TL
Açıklama: {description}

Bu giyim ürünü için detaylı analiz yap:
- Kumaş özellikleri (pamuk, polyester, karışım vb.)
- Kesim ve kalıp (slim fit, regular, oversize vb.)
- Hangi mevsim ve ortamlara uygun
- Hangi aksesuarlarla kombine edilebilir
- Yıkama ve bakım talimatları
- Beden seçimi ipuçları

JSON formatında döndür:
{
    \"ai_description\": \"2-3 cümlelik şık ve bilgilendirici açıklama\",
    \"features\": [
        \"Kumaş bilgisi ve özellikleri\",
        \"Kesim ve kalıp detayları\",
        \"Renk ve desen özellikleri\",
        \"Özel detaylar (yaka, cep, fermuar vb.)\"
    ],
    \"usage_scenarios\": [
        \"Günlük kullanım için\",
        \"İş ve formal ortamlar için\",
        \"Spor ve rahat aktiviteler için\",
        \"Özel günler için\"
    ],
    \"specifications\": {
        \"Kumaş\": \"Kumaş kompozisyonu\",
        \"Kesim\": \"Kalıp tipi\",
        \"Beden Aralığı\": \"Mevcut bedenler\",
        \"Bakım\": \"Yıkama talimatı\"
    },
    \"pros_cons\": {
        \"pros\": [\"Konforlu kumaş\", \"Şık kesim\", \"Çok yönlü kullanım\"],
        \"cons\": [\"Beden farklılıkları olabilir\", \"Renk tonu ekrandan farklılık gösterebilir\"]
    },
    \"recommendations\": [
        \"Beden tablosunu kontrol edin\",
        \"İlk yıkamada renk ayrımı yapın\",
        \"Ters çevirerek yıkayın\",
        \"Düşük ısıda ütüleyin\"
    ]
}

Sadece JSON döndür.",
        
        'ai_description' => '{name}, kaliteli kumaşı ve modern tasarımı ile öne çıkan bir giyim ürünüdür. Hem şıklık hem de konfor arayanlar için ideal bir seçimdir.',
        'features' => [
            'Kaliteli kumaş ve işçilik',
            'Rahat ve modern kesim',
            'Çok yönlü kullanım',
            'Dayanıklı dikiş'
        ],
        'usage_scenarios' => [
            'Günlük kullanım için',
            'İş ve sosyal ortamlar için',
            'Rahat ve şık görünüm için'
        ],
        'specifications' => [
            'Kumaş' => 'Kaliteli kumaş',
            'Beden' => 'S-XXL arası',
            'Bakım' => '30°C yıkama'
        ],
        'pros_cons' => [
            'pros' => ['Konforlu', 'Şık', 'Dayanıklı'],
            'cons' => ['Beden farklılıkları olabilir', 'Renk tonu farklılık gösterebilir']
        ],
        'recommendations' => [
            'Beden tablosunu inceleyin',
            'İlk yıkamada renk ayrımı yapın',
            'Uygun sıcaklıkta yıkayın',
            'Kurutma makinesinden kaçının'
        ],
        'additional_info' => 'Giyim ürünleri için beden değişimi ve iade koşullarımızı inceleyebilirsiniz.'
    ],

    'cocuk_giyim' => [
        'keywords' => [
            'çocuk', 'bebek', 'kids', 'baby', 'çocuk giyim',
            'okul', 'school', 'üniforma', 'okul kıyafeti'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, çocuklar için özel olarak tasarlanmış konforlu ve güvenli bir giyim ürünüdür. Dayanıklı kumaşı ve sevimli tasarımı ile çocuğunuzun favorisi olacak.',
        'features' => [
            'Çocuk cildine uygun kumaş',
            'Dayanıklı ve uzun ömürlü',
            'Kolay yıkama ve bakım',
            'Güvenli aksesuarlar'
        ],
        'usage_scenarios' => [
            'Okul için ideal',
            'Günlük aktiviteler için',
            'Oyun ve hareket için rahat'
        ],
        'specifications' => [
            'Yaş Grubu' => 'Belirtilecek',
            'Kumaş' => 'Hassas cilt için uygun',
            'Güvenlik' => 'Sertifikalı ürün'
        ],
        'pros_cons' => [
            'pros' => ['Güvenli', 'Dayanıklı', 'Konforlu'],
            'cons' => ['Hızlı büyüme nedeniyle kısa ömürlü olabilir']
        ],
        'recommendations' => [
            'Yaş ve boy ölçüsüne göre seçin',
            'Kolay çıkarılabilir modeller tercih edin',
            'Yüksek sıcaklıkta yıkamaktan kaçının'
        ],
        'additional_info' => 'Çocuk ürünlerinde güvenlik sertifikalarına dikkat edilmiştir.'
    ],

    'dis_giyim' => [
        'keywords' => [
            'mont', 'kaban', 'parka', 'coat', 'jacket',
            'dış giyim', 'outerwear', 'yağmurluk', 'trençkot',
            'bomber', 'windbreaker', 'softshell'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, soğuk ve sert hava koşullarında sizi koruyacak şık ve fonksiyonel bir dış giyim ürünüdür.',
        'features' => [
            'Su ve rüzgar geçirmez',
            'Sıcak tutan dolgu/yalıtım',
            'Dayanıklı dış kumaş',
            'Pratik cepler'
        ],
        'usage_scenarios' => [
            'Soğuk hava koşulları için',
            'Yağmurlu günler için',
            'Günlük kullanım için',
            'Outdoor aktiviteler için'
        ],
        'specifications' => [
            'Dolgu' => 'Yalıtım tipi',
            'Su Geçirmezlik' => 'Evet/Hayır',
            'Mevsim' => 'Kış/Sonbahar/İlkbahar'
        ],
        'pros_cons' => [
            'pros' => ['Koruyucu', 'Sıcak', 'Dayanıklı'],
            'cons' => ['Hacimli olabilir', 'Özel bakım gerektirebilir']
        ],
        'recommendations' => [
            'Mevsime uygun seçim yapın',
            'Kuru temizleme öneririz',
            'Düzenli havalandırın',
            'Nemli ortamda saklamayın'
        ],
        'additional_info' => 'Dış giyim ürünleri için bakım talimatlarına uyunuz.'
    ],

    'ayakkabi' => [
        'keywords' => [
            'ayakkabı', 'bot', 'terlik', 'sandalet', 'shoes',
            'spor ayakkabı', 'sneaker', 'koşu ayakkabısı', 'running',
            'topuklu', 'babet', 'oxford', 'loafer', 'çizme',
            'çocuk ayakkabı', 'bebek ayakkabı'
        ],
        'use_ai' => true,
        'ai_prompt_template' => "Ayakkabı: {name}
Marka: {brand}
Fiyat: {price} TL

Bu ayakkabı için detaylı analiz yap:
- Ayakkabı tipi (spor, klasik, casual, formal)
- Hangi mevsim ve ortamlar için uygun
- Malzeme ve konfor özellikleri
- Taban teknolojisi
- Bakım önerileri

JSON formatında döndür...",
        
        'ai_description' => '{name}, konforlu ve şık tasarımı ile her ortamda rahatlıkla kullanabileceğiniz bir ayakkabıdır.',
        'features' => [
            'Konforlu iç taban',
            'Dayanıklı dış taban',
            'Kaliteli malzeme',
            'Şık tasarım'
        ],
        'usage_scenarios' => [
            'Günlük kullanım için',
            'Spor aktiviteleri için',
            'İş ve formal ortamlar için'
        ],
        'specifications' => [
            'Malzeme' => 'Deri/Sentetik/Tekstil',
            'Taban' => 'Kauçuk/EVA',
            'Beden Aralığı' => 'Çeşitli bedenler'
        ],
        'pros_cons' => [
            'pros' => ['Konforlu', 'Dayanıklı', 'Şık'],
            'cons' => ['Beden farklılıkları olabilir', 'İlk kullanımda sıkabilir']
        ],
        'recommendations' => [
            'Doğru beden seçimi için ölçü tablonuzu kontrol edin',
            'İlk kullanımda kısa süreler giyin',
            'Düzenli temizlik yapın',
            'Nemli ortamda kurutmayın'
        ],
        'additional_info' => 'Ayakkabı bakımı için özel ürünler kullanmanızı öneririz.'
    ],

    'canta' => [
        'keywords' => [
            'çanta', 'bag', 'sırt çantası', 'el çantası', 'omuz çantası',
            'valiz', 'bavul', 'suitcase', 'backpack', 'tote bag',
            'laptop çantası', 'spor çantası', 'clutch', 'bel çantası'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, fonksiyonel tasarımı ve kaliteli malzemesi ile günlük ihtiyaçlarınızı taşımak için ideal bir çantadır.',
        'features' => [
            'Geniş iç hacim',
            'Kaliteli malzeme',
            'Dayanıklı dikişler',
            'Pratik bölmeler'
        ],
        'usage_scenarios' => [
            'Günlük kullanım için',
            'İş ve okul için',
            'Seyahat için',
            'Spor aktiviteleri için'
        ],
        'specifications' => [
            'Malzeme' => 'Deri/Sentetik/Kumaş',
            'Kapasite' => 'Litre/Boyut bilgisi',
            'Bölmeler' => 'İç/dış cep sayısı'
        ],
        'pros_cons' => [
            'pros' => ['Geniş hacim', 'Dayanıklı', 'Şık tasarım'],
            'cons' => ['Ağır olabilir (doluyken)', 'Bakım gerektirir']
        ],
        'recommendations' => [
            'Kullanım amacınıza uygun model seçin',
            'Düzenli temizlik yapın',
            'Aşırı yüklemekten kaçının',
            'Nem ve güneşten koruyun'
        ],
        'additional_info' => 'Çanta bakımı için uygun temizlik ürünleri kullanın.'
    ],

    'taki' => [
        'keywords' => [
            'kolye', 'küpe', 'yüzük', 'bilezik', 'takı', 'altın', 'gümüş',
            'pırlanta', 'mücevher', 'jewelry', 'necklace', 'ring', 'earring',
            'bracelet', 'zincir', 'şans', 'halhal', 'broş', 'piercing'
        ],
        'use_ai' => true,
        'ai_prompt_template' => "Ürün: {name}
Marka: {brand}
Kategori: Takı/Mücevherat
Fiyat: {price} TL
Açıklama: {description}

Bu takı ürünü için lütfen profesyonel bir analiz yap. Şu konulara dikkat et:
- Takının hangi malzemeden yapıldığı (altın, gümüş, pırlanta, taş vb.)
- Tasarım özellikleri ve estetik değeri
- Hangi kıyafetlerle veya hangi etkinliklerde kullanılabileceği
- Bakım önerileri ve kullanım ipuçları
- Hediye seçeneği olarak değeri

Aşağıdaki JSON formatında döndür:

{
    \"ai_description\": \"2-3 cümlelik zarif ve etkileyici ürün açıklaması\",
    \"features\": [
        \"Malzeme ve ayar bilgisi\",
        \"Tasarım ve işçilik özellikleri\",
        \"Boyut ve ağırlık bilgisi\",
        \"Özel detaylar (taş, süsleme vb.)\"
    ],
    \"usage_scenarios\": [
        \"Günlük kullanım için\",
        \"Özel günler ve davetler için\",
        \"Hediye seçeneği olarak\"
    ],
    \"specifications\": {
        \"Malzeme\": \"Ana malzeme bilgisi\",
        \"Ayar\": \"Altın/gümüş ayarı\",
        \"Taş\": \"Kullanılan taş bilgisi (varsa)\",
        \"Ağırlık\": \"Yaklaşık ağırlık\",
        \"Garanti\": \"Garanti süresi\"
    },
    \"pros_cons\": {
        \"pros\": [
            \"Kaliteli işçilik\",
            \"Zarif tasarım\",
            \"Uzun ömürlü\"
        ],
        \"cons\": [
            \"Özel bakım gerektirir\",
            \"Alerjik reaksiyon riski (materyal bilgisi verilmemişse)\"
        ]
    },
    \"recommendations\": [
        \"Nem ve kimyasallardan uzak tutun\",
        \"Yumuşak bezle silerek temizleyin\",
        \"Özel kutusunda saklayın\",
        \"Parfüm ve kozmetik kullanımından sonra takın\"
    ]
}

Sadece JSON döndür, başka açıklama ekleme.",
        
        'ai_description' => '{name}, zarif tasarımı ve kaliteli işçiliği ile öne çıkan bir takı ürünüdür. Hem günlük kullanım hem de özel günler için ideal bir seçimdir.',
        'features' => [
            'Kaliteli malzeme ve işçilik',
            'Zarif ve şık tasarım',
            'Alerjik reaksiyon yapmayan malzeme',
            'Uzun ömürlü ve dayanıklı'
        ],
        'usage_scenarios' => [
            'Günlük şıklık için',
            'Özel günler ve davetler için',
            'Değerli bir hediye seçeneği olarak'
        ],
        'specifications' => [
            'Malzeme' => 'Belirtilmemiş',
            'Renk' => 'Ürün görseline göre',
            'Garanti' => '2 yıl üretici garantisi'
        ],
        'pros_cons' => [
            'pros' => [
                'Zarif ve şık tasarım',
                'Kaliteli malzeme',
                'Hediye olarak ideal'
            ],
            'cons' => [
                'Özel bakım gerektirir',
                'Su ve kimyasallardan korunmalı'
            ]
        ],
        'recommendations' => [
            'Takıyı suya ve kimyasallara maruz bırakmayın',
            'Yumuşak bir bezle düzenli temizleyin',
            'Özel kutusunda saklayın',
            'Parfüm ve kremlerden sonra takın'
        ],
        'additional_info' => 'Takı ürünleri özel bakım gerektirir. Bakım önerilerine uyarak uzun ömürlü kullanım sağlayabilirsiniz.'
    ],

    'dogaltas' => [
        'keywords' => [
            'doğaltaş', 'doğal taş', 'kristal', 'ametist', 'kuvars', 'akik',
            'yeşim', 'obsidyen', 'türkuaz', 'kehribar', 'natural stone',
            'crystal', 'gemstone', 'jade', 'opal', 'göz nazar', 'şifa taşı',
            'enerji taşı', 'çakra', 'oniks', 'roze kuvars', 'sitrin'
        ],
        'use_ai' => true,
        'ai_prompt_template' => "Ürün: {name}
Marka: {brand}
Kategori: Doğaltaş/Kristal
Fiyat: {price} TL
Açıklama: {description}

Bu doğaltaş ürünü için lütfen detaylı bir analiz yap. Şu konulara dikkat et:
- Taşın özellikleri ve enerji alanı
- Hangi amaçlarla kullanıldığı (şifa, meditasyon, enerji vb.)
- Bakım ve temizleme yöntemleri
- Hangi çakralarla ilişkili olduğu
- Kullanım önerileri

Aşağıdaki JSON formatında döndür:

{
    \"ai_description\": \"2-3 cümlelik mistik ve bilgilendirici açıklama\",
    \"features\": [
        \"Taşın fiziksel özellikleri\",
        \"Enerji ve metafizik özellikleri\",
        \"Boyut ve ağırlık bilgisi\",
        \"Orijin bilgisi\"
    ],
    \"usage_scenarios\": [
        \"Meditasyon ve rahatlama için\",
        \"Enerji dengeleme için\",
        \"Takı olarak günlük kullanım için\",
        \"Hediye seçeneği olarak\"
    ],
    \"specifications\": {
        \"Taş Türü\": \"Ana taş türü\",
        \"Orijin\": \"Çıkarıldığı bölge\",
        \"Boyut\": \"Yaklaşık boyut\",
        \"Ağırlık\": \"Yaklaşık ağırlık\",
        \"Çakra\": \"İlişkili çakra\"
    },
    \"pros_cons\": {
        \"pros\": [
            \"Doğal ve özgün\",
            \"Enerji verici\",
            \"Estetik görünüm\"
        ],
        \"cons\": [
            \"Kırılgan yapı\",
            \"Her taş benzersiz olduğu için görsel farklılıklar olabilir\"
        ]
    },
    \"recommendations\": [
        \"Ay ışığında veya tütsüyle temizleyin\",
        \"Negatif enerjiden arındırmak için düzenli temizlik yapın\",
        \"Meditasyon sırasında elinizde tutun\",
        \"Güneş ışığında uzun süre bırakmayın (solabilir)\"
    ]
}

Sadece JSON döndür, başka açıklama ekleme.",
        
        'ai_description' => '{name}, doğanın eşsiz güzelliğini yansıtan özgün bir doğaltaş ürünüdür. Hem estetik hem de enerji açısından değerli bir seçimdir.',
        'features' => [
            'Doğal ve özgün taş',
            'Yüksek enerji titreşimi',
            'Her biri eşsiz ve benzersiz',
            'Estetik ve doğal görünüm'
        ],
        'usage_scenarios' => [
            'Meditasyon ve rahatlama için',
            'Enerji dengeleme için',
            'Dekoratif aksesuar olarak',
            'Anlamlı bir hediye seçeneği'
        ],
        'specifications' => [
            'Tür' => 'Doğal Taş',
            'Orijin' => 'Doğal kaynaklardan',
            'Özellik' => 'Her taş benzersizdir'
        ],
        'pros_cons' => [
            'pros' => [
                'Doğal ve özgün',
                'Pozitif enerji',
                'Estetik görünüm'
            ],
            'cons' => [
                'Kırılgan olabilir',
                'Renk ve desen farklılıkları olabilir'
            ]
        ],
        'recommendations' => [
            'Düzenli enerji temizliği yapın',
            'Sert yüzeylere çarpmaktan kaçının',
            'Ay ışığında şarj edin',
            'Kimyasal maddelerden uzak tutun'
        ],
        'additional_info' => 'Doğaltaşlar doğadan gelen enerjiyi taşır ve her biri benzersizdir. Renkler ve desenler küçük farklılıklar gösterebilir.'
    ],

    'aksesuar' => [
        'keywords' => [
            'aksesuar', 'gözlük', 'güneş gözlüğü', 'sunglasses',
            'şapka', 'bere', 'hat', 'cap', 'kasket',
            'eşarp', 'atkı', 'fular', 'scarf', 'bandana',
            'eldiven', 'kemer', 'belt', 'kuşak',
            'saat', 'kol saati', 'watch',
            'cüzdan', 'wallet', 'kartlık'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, şık tasarımı ve kaliteli malzemesi ile stilinizi tamamlayan bir aksesuar ürünüdür.',
        'features' => [
            'Kaliteli malzeme',
            'Modern tasarım',
            'Çok yönlü kullanım',
            'Dayanıklı yapı'
        ],
        'usage_scenarios' => [
            'Günlük kullanım için',
            'Özel günler için',
            'Hediye seçeneği olarak'
        ],
        'specifications' => [
            'Malzeme' => 'Kaliteli malzeme',
            'Boyut' => 'Standart',
            'Renk' => 'Çeşitli renkler'
        ],
        'pros_cons' => [
            'pros' => ['Şık', 'Kaliteli', 'Çok yönlü'],
            'cons' => ['Özel bakım gerektirebilir']
        ],
        'recommendations' => [
            'Uygun koşullarda saklayın',
            'Düzenli temizlik yapın',
            'Aşırı güneş ve neme maruz bırakmayın'
        ],
        'additional_info' => 'Aksesuar ürünleri stilinizi tamamlar.'
    ],

    'mayo_bikini' => [
        'keywords' => [
            'mayo', 'bikini', 'deniz', 'plaj', 'swimwear',
            'beach', 'yüzme', 'havuz'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, plaj ve havuz keyfi için tasarlanmış şık ve konforlu bir mayo ürünüdür.',
        'features' => [
            'Elastik ve esnek kumaş',
            'Klora dayanıklı',
            'UV korumalı',
            'Hızlı kuruyan'
        ],
        'usage_scenarios' => [
            'Deniz ve plaj için',
            'Havuz için',
            'Güneşlenme için'
        ],
        'specifications' => [
            'Kumaş' => 'Lycra/Polyester karışımı',
            'UV Koruma' => 'Evet',
            'Beden' => 'S-XL arası'
        ],
        'pros_cons' => [
            'pros' => ['Konforlu', 'Dayanıklı', 'Şık tasarım'],
            'cons' => ['Klor etkisiyle zamanla solabilir']
        ],
        'recommendations' => [
            'Kullanım sonrası bol suyla durulayın',
            'Güneşte kurutmayın',
            'Sıkmadan hafifçe kurutun'
        ],
        'additional_info' => 'Mayo bakımı için özel talimatları takip edin.'
    ],

    'hamile_giyim' => [
        'keywords' => [
            'hamile', 'maternity', 'emzirme', 'lohusa',
            'hamile giyim', 'pregnancy'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, hamilelik döneminde konfor ve şıklığı bir arada sunan özel tasarım bir giyim ürünüdür.',
        'features' => [
            'Esnek ve rahat kumaş',
            'Büyüyen karına uyum sağlar',
            'Emzirmeye uygun tasarım',
            'Nefes alabilen malzeme'
        ],
        'usage_scenarios' => [
            'Hamilelik dönemi için',
            'Emzirme dönemi için',
            'Günlük konfor için'
        ],
        'specifications' => [
            'Dönem' => 'Tüm hamilelik dönemi',
            'Kumaş' => 'Esnek ve nefes alır',
            'Özellik' => 'Emzirme uyumlu'
        ],
        'pros_cons' => [
            'pros' => ['Çok konforlu', 'Esneklik', 'Pratik kullanım'],
            'cons' => ['Sadece belirli dönemde kullanılır']
        ],
        'recommendations' => [
            'Doğru beden seçimi yapın',
            'Düzenli yıkayın',
            'Hassas programda yıkayın'
        ],
        'additional_info' => 'Hamile giyim ürünleri özel dönem ihtiyaçları için tasarlanmıştır.'
    ],

    'gelinlik_abiye' => [
        'keywords' => [
            'gelinlik', 'abiye', 'nişanlık', 'evening dress',
            'gala', 'kokteyl', 'damat', 'damatlık'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, özel günlerinizde göz alıcı görünmeniz için tasarlanmış lüks bir giyim ürünüdür.',
        'features' => [
            'Lüks kumaş ve detaylar',
            'El işçiliği',
            'Özel tasarım',
            'Göz alıcı görünüm'
        ],
        'usage_scenarios' => [
            'Düğün ve nişan için',
            'Gala ve özel davetler için',
            'Mezuniyet ve balo için'
        ],
        'specifications' => [
            'Kumaş' => 'Premium kumaş',
            'İşçilik' => 'El işi detaylar',
            'Stil' => 'Modern/Klasik'
        ],
        'pros_cons' => [
            'pros' => ['Göz alıcı', 'Özel tasarım', 'Lüks kumaş'],
            'cons' => ['Özel bakım gerekir', 'Tek kullanımlık olabilir']
        ],
        'recommendations' => [
            'Prova yaptırın',
            'Kuru temizleme yapın',
            'Uygun koşullarda saklayın'
        ],
        'additional_info' => 'Özel gün kıyafetleri için profesyonel bakım önerilir.'
    ],

    /*
    |--------------------------------------------------------------------------
    | GRUP 2: ELEKTRONİK & TEKNOLOJİ
    |--------------------------------------------------------------------------
    */

    'telefon' => [
        'keywords' => [
            'telefon', 'akıllı telefon', 'smartphone', 'iPhone',
            'Samsung', 'Xiaomi', 'Android', 'iOS', 'mobile'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, son teknoloji özellikleri ve güçlü performansı ile günlük hayatınızı kolaylaştıran bir akıllı telefondur.',
        'features' => [
            'Yüksek çözünürlüklü ekran',
            'Güçlü işlemci',
            'Gelişmiş kamera sistemi',
            'Uzun batarya ömrü'
        ],
        'usage_scenarios' => [
            'Günlük iletişim için',
            'Fotoğraf ve video çekimi için',
            'Oyun ve eğlence için',
            'İş ve prodüktivite için'
        ],
        'specifications' => [
            'İşletim Sistemi' => 'Android/iOS',
            'RAM' => 'GB bilgisi',
            'Depolama' => 'GB bilgisi',
            'Ekran' => 'İnç ve çözünürlük'
        ],
        'pros_cons' => [
            'pros' => ['Güçlü performans', 'Kaliteli kamera', 'Uzun batarya'],
            'cons' => ['Fiyat', 'Büyük boyut (bazı modeller)']
        ],
        'recommendations' => [
            'Ekran koruyucu ve kılıf kullanın',
            'Düzenli yazılım güncellemesi yapın',
            'Orijinal şarj aleti kullanın'
        ],
        'additional_info' => 'Akıllı telefonlar için garanti şartlarını inceleyin.'
    ],

    'bilgisayar' => [
        'keywords' => [
            'laptop', 'notebook', 'bilgisayar', 'computer',
            'MacBook', 'ultrabook', 'gaming laptop',
            'tablet', 'iPad', 'tab'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, güçlü donanımı ve taşınabilir tasarımı ile iş ve eğlence ihtiyaçlarınızı karşılayan bir bilgisayardır.',
        'features' => [
            'Güçlü işlemci',
            'Yeterli RAM',
            'Geniş depolama',
            'Kaliteli ekran'
        ],
        'usage_scenarios' => [
            'İş ve ofis kullanımı için',
            'Grafik ve tasarım için',
            'Oyun için',
            'Eğitim için'
        ],
        'specifications' => [
            'İşlemci' => 'İşlemci modeli',
            'RAM' => 'GB',
            'Ekran' => 'İnç',
            'Batarya' => 'Saat'
        ],
        'pros_cons' => [
            'pros' => ['Güçlü performans', 'Taşınabilir', 'Uzun batarya'],
            'cons' => ['Ağırlık', 'Fiyat']
        ],
        'recommendations' => [
            'Kullanım amacınıza uygun model seçin',
            'Düzenli bakım yapın',
            'Antivirüs kullanın',
            'Yedekleme yapın'
        ],
        'additional_info' => 'Bilgisayarlar için garanti ve teknik destek hizmetlerimiz mevcuttur.'
    ],

    'elektronik_aksesuar' => [
        'keywords' => [
            'kılıf', 'case', 'kablo', 'şarj aleti', 'adaptör',
            'powerbank', 'kulaklık', 'hoparlör', 'speaker',
            'klavye', 'mouse', 'fare', 'monitör', 'ekran',
            'USB', 'flash bellek', 'harici disk'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, elektronik cihazlarınızı korumak ve kullanımını kolaylaştırmak için tasarlanmış bir aksesuardır.',
        'features' => [
            'Yüksek kalite',
            'Uyumlu tasarım',
            'Dayanıklı malzeme',
            'Pratik kullanım'
        ],
        'usage_scenarios' => [
            'Günlük kullanım için',
            'Koruma için',
            'Genişletme için'
        ],
        'specifications' => [
            'Uyumluluk' => 'Cihaz bilgisi',
            'Malzeme' => 'Malzeme tipi',
            'Özellikler' => 'Teknik detaylar'
        ],
        'pros_cons' => [
            'pros' => ['Koruyucu', 'Pratik', 'Uyumlu'],
            'cons' => ['Cihaz özel olabilir']
        ],
        'recommendations' => [
            'Uyumluluk kontrolü yapın',
            'Orijinal ürünleri tercih edin',
            'Düzenli temizlik yapın'
        ],
        'additional_info' => 'Elektronik aksesuarlar için uyumluluk önemlidir.'
    ],

    'akilli_saat' => [
        'keywords' => [
            'akıllı saat', 'smartwatch', 'fitness tracker',
            'bileklik', 'activity tracker', 'Apple Watch', 'Galaxy Watch'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, sağlık takibi ve akıllı özelliklerle donatılmış modern bir giyilebilir cihazdır.',
        'features' => [
            'Sağlık ve fitness takibi',
            'Akıllı bildirimler',
            'Su geçirmez',
            'Uzun batarya ömrü'
        ],
        'usage_scenarios' => [
            'Günlük aktivite takibi için',
            'Spor ve egzersiz için',
            'Sağlık izleme için',
            'Akıllı bildirimler için'
        ],
        'specifications' => [
            'Ekran' => 'AMOLED/LCD',
            'Batarya' => 'Gün',
            'Su Geçirmezlik' => 'ATM/IP sınıfı',
            'Uyumluluk' => 'Android/iOS'
        ],
        'pros_cons' => [
            'pros' => ['Sağlık takibi', 'Akıllı özellikler', 'Şık tasarım'],
            'cons' => ['Batarya süresi sınırlı', 'Telefona bağımlı']
        ],
        'recommendations' => [
            'Telefonunuzla uyumluluğunu kontrol edin',
            'Düzenli şarj edin',
            'Yazılım güncellemelerini yapın',
            'Su geçirmez olsa da sıcak suyun altında tutmayın'
        ],
        'additional_info' => 'Akıllı saatler sağlık takibi için yardımcı araçlardır, tıbbi cihaz değildir.'
    ],

    'tv_monitor' => [
        'keywords' => [
            'televizyon', 'TV', 'smart TV', 'led', 'OLED', 'QLED',
            'monitör', 'ekran', 'monitor', 'display', 'gaming monitor'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, yüksek görüntü kalitesi ve akıllı özellikleriyle ev eğlenceniz için mükemmel bir ekran çözümüdür.',
        'features' => [
            'Yüksek çözünürlük',
            'Akıllı TV özellikleri',
            'Geniş görüş açısı',
            'Zengin renk gamı'
        ],
        'usage_scenarios' => [
            'Film ve dizi izleme için',
            'Oyun oynamak için',
            'Akıllı ev merkezi olarak',
            'Bilgisayar ekranı olarak'
        ],
        'specifications' => [
            'Boyut' => 'İnç',
            'Çözünürlük' => '4K/Full HD',
            'Panel' => 'LED/OLED/QLED',
            'Yenileme Hızı' => 'Hz'
        ],
        'pros_cons' => [
            'pros' => ['Yüksek görüntü kalitesi', 'Akıllı özellikler', 'Geniş ekran'],
            'cons' => ['Yer kaplar', 'Enerji tüketimi']
        ],
        'recommendations' => [
            'Odanızın boyutuna uygun ekran seçin',
            'Düzenli temizlik yapın',
            'Voltaj koruyucu kullanın',
            'Parlak ışıktan koruyun'
        ],
        'additional_info' => 'TV ve monitörler için garanti ve kurulum hizmetlerimiz mevcuttur.'
    ],

    'oyun_konsol' => [
        'keywords' => [
            'oyun konsolu', 'PlayStation', 'Xbox', 'Nintendo',
            'console', 'gaming', 'Switch', 'PS5', 'PS4'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, en yeni oyunları oynamak için güçlü donanım ve geniş oyun kütüphanesi sunan bir oyun konsoludur.',
        'features' => [
            'Güçlü işlemci',
            'Yüksek grafik kalitesi',
            'Geniş oyun kütüphanesi',
            'Online multiplayer'
        ],
        'usage_scenarios' => [
            'Tek oyunculu oyunlar için',
            'Çok oyunculu oyunlar için',
            'Aile eğlencesi için',
            'Yarışmalı oyun için'
        ],
        'specifications' => [
            'İşlemci' => 'CPU detayı',
            'Grafik' => 'GPU detayı',
            'Depolama' => 'GB/TB',
            'Çözünürlük' => '4K/Full HD'
        ],
        'pros_cons' => [
            'pros' => ['Yüksek performans', 'Özel oyunlar', 'Kolay kullanım'],
            'cons' => ['Oyun maliyeti', 'Online abonelik gerekebilir']
        ],
        'recommendations' => [
            'Oyun tercihlerinize göre seçim yapın',
            'İyi bir ekranla kullanın',
            'Düzenli yazılım güncellemesi yapın',
            'Yedek kol alabilirsiniz'
        ],
        'additional_info' => 'Oyun konsolları için garanti ve aksesuar desteği sunuyoruz.'
    ],

    'kamera' => [
        'keywords' => [
            'kamera', 'fotoğraf makinesi', 'DSLR', 'mirrorless',
            'camera', 'drone', 'aksiyon kamerası', 'GoPro',
            'güvenlik kamerası', 'IP kamera'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, profesyonel fotoğraf ve video çekimi için gelişmiş özellikler sunan bir kamera sistemidir.',
        'features' => [
            'Yüksek megapiksel',
            'Gelişmiş otofokus',
            'Video kayıt',
            'Değiştirilebilir lens (DSLR/mirrorless)'
        ],
        'usage_scenarios' => [
            'Profesyonel fotoğrafçılık için',
            'Video prodüksiyonu için',
            'Hobi fotoğrafçılığı için',
            'Güvenlik için (IP kamera)'
        ],
        'specifications' => [
            'Megapiksel' => 'MP',
            'Sensor' => 'Full Frame/APS-C',
            'Video' => '4K/Full HD',
            'ISO' => 'Aralık'
        ],
        'pros_cons' => [
            'pros' => ['Yüksek kalite', 'Profesyonel özellikler', 'Genişletilebilir'],
            'cons' => ['Pahalı', 'Öğrenme eğrisi']
        ],
        'recommendations' => [
            'Kullanım seviyenize uygun seçin',
            'Lens ve aksesuar bütçesi ayırın',
            'Düzenli temizlik yapın',
            'Nemli ortamdan koruyun'
        ],
        'additional_info' => 'Kameralar için profesyonel destek ve eğitim hizmetlerimiz mevcuttur.'
    ],

    'bilgisayar_bileseni' => [
        'keywords' => [
            'RAM', 'SSD', 'hard disk', 'anakart', 'motherboard',
            'işlemci', 'CPU', 'ekran kartı', 'GPU', 'graphics card',
            'kasa', 'case', 'power supply', 'güç kaynağı', 'soğutucu'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, bilgisayar performansını artırmak veya yeni bir sistem kurmak için kullanılan kaliteli bir donanım bileşenidir.',
        'features' => [
            'Yüksek performans',
            'Uyumlu tasarım',
            'Kaliteli yapım',
            'Garanti desteği'
        ],
        'usage_scenarios' => [
            'Bilgisayar yükseltmesi için',
            'Yeni PC kurulumu için',
            'Performans artışı için',
            'Gaming için'
        ],
        'specifications' => [
            'Model' => 'Ürün modeli',
            'Hız' => 'MHz/GHz',
            'Uyumluluk' => 'Socket/Slot tipi',
            'Güç' => 'Watt (varsa)'
        ],
        'pros_cons' => [
            'pros' => ['Performans artışı', 'Kaliteli yapım', 'Garanti'],
            'cons' => ['Uyumluluk kontrolü gerekir', 'Teknik bilgi gerekebilir']
        ],
        'recommendations' => [
            'Sistemle uyumluluğunu kontrol edin',
            'Profesyonel kurulum önerilir',
            'Garanti şartlarını saklayın',
            'Statik elektrikten koruyun'
        ],
        'additional_info' => 'Bilgisayar bileşenleri için teknik destek hizmetimiz mevcuttur.'
    ],

    'akilli_ev' => [
        'keywords' => [
            'akıllı ev', 'smart home', 'akıllı priz', 'akıllı ampul',
            'akıllı anahtar', 'otomasyon', 'Alexa', 'Google Home',
            'Homekit', 'router', 'modem', 'wifi', 'mesh'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, evinizi akıllı hale getirerek konfor ve güvenliği artıran modern bir teknoloji ürünüdür.',
        'features' => [
            'Uzaktan kontrol',
            'Sesli komut desteği',
            'Otomasyon',
            'Enerji tasarrufu'
        ],
        'usage_scenarios' => [
            'Ev otomasyonu için',
            'Güvenlik için',
            'Enerji yönetimi için',
            'Konfor için'
        ],
        'specifications' => [
            'Bağlantı' => 'WiFi/Bluetooth/Zigbee',
            'Uyumluluk' => 'Alexa/Google/Homekit',
            'Güç' => 'Watt/Voltaj',
            'Kontrol' => 'Uygulama/Sesli'
        ],
        'pros_cons' => [
            'pros' => ['Pratik kullanım', 'Enerji tasarrufu', 'Otomasyon'],
            'cons' => ['İnternet bağımlılığı', 'Kurulum gerekebilir']
        ],
        'recommendations' => [
            'Ağ altyapınızı kontrol edin',
            'Uyumlu ekosistem seçin',
            'Güvenli şifre kullanın',
            'Düzenli güncelleme yapın'
        ],
        'additional_info' => 'Akıllı ev cihazları için kurulum desteği sunuyoruz.'
    ],

    /*
    |--------------------------------------------------------------------------
    | GRUP 3: EV & YAŞAM
    |--------------------------------------------------------------------------
    */

    'mobilya' => [
        'keywords' => [
            'mobilya', 'koltuk', 'masa', 'sandalye', 'furniture',
            'yatak', 'dolap', 'gardrop', 'raf', 'kitaplık',
            'kanepe', 'berjer', 'sehpa', 'konsol',
            'ofis mobilya', 'ofis masa', 'ofis sandalye'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, fonksiyonel tasarımı ve kaliteli malzemesiyle yaşam alanınıza değer katan bir mobilya ürünüdür.',
        'features' => [
            'Kaliteli malzeme',
            'Sağlam yapı',
            'Şık tasarım',
            'Kullanışlı boyutlar'
        ],
        'usage_scenarios' => [
            'Ev kullanımı için',
            'Ofis kullanımı için',
            'Dekorasyon için',
            'Fonksiyonel kullanım için'
        ],
        'specifications' => [
            'Malzeme' => 'Ahşap/Metal/Kumaş',
            'Boyut' => 'cm/m',
            'Renk' => 'Renk seçenekleri',
            'Montaj' => 'Gerekli/Hazır'
        ],
        'pros_cons' => [
            'pros' => ['Kaliteli', 'Şık', 'Dayanıklı'],
            'cons' => ['Montaj gerekebilir', 'Ağır olabilir']
        ],
        'recommendations' => [
            'Mekan ölçülerinizi kontrol edin',
            'Montaj talimatlarını takip edin',
            'Düzenli temizlik yapın',
            'Nemden koruyun'
        ],
        'additional_info' => 'Mobilya ürünleri için montaj ve nakliye hizmeti sunuyoruz.'
    ],

    'aydinlatma' => [
        'keywords' => [
            'avize', 'lamba', 'aydınlatma', 'lighting',
            'LED', 'ampul', 'abajur', 'aplik',
            'spot', 'şerit led', 'masa lambası'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, yaşam alanınıza atmosfer katan dekoratif ve fonksiyonel bir aydınlatma ürünüdür.',
        'features' => [
            'Enerji tasarruflu',
            'Uzun ömürlü',
            'Ayarlanabilir parlaklık (bazı modellerde)',
            'Şık tasarım'
        ],
        'usage_scenarios' => [
            'Genel aydınlatma için',
            'Dekoratif amaçlı',
            'Çalışma alanı için',
            'Ortam aydınlatması için'
        ],
        'specifications' => [
            'Güç' => 'Watt',
            'Renk Sıcaklığı' => 'Kelvin',
            'Enerji Sınıfı' => 'A++/A+',
            'Duy' => 'E27/E14/GU10'
        ],
        'pros_cons' => [
            'pros' => ['Enerji tasarrufu', 'Uzun ömür', 'Çeşitli seçenekler'],
            'cons' => ['Montaj gerekebilir', 'Duy uyumu kontrol edilmeli']
        ],
        'recommendations' => [
            'Mekan büyüklüğüne göre seçin',
            'Renk sıcaklığına dikkat edin',
            'Profesyonel montaj öneririz',
            'Düzenli toz temizliği yapın'
        ],
        'additional_info' => 'Aydınlatma ürünleri için montaj desteği sunuyoruz.'
    ],

    'ev_tekstil' => [
        'keywords' => [
            'halı', 'kilim', 'paspas', 'carpet', 'rug',
            'perde', 'stor', 'fon', 'curtain',
            'çarşaf', 'nevresim', 'yastık', 'pike',
            'yatak örtüsü', 'battaniye', 'yorgan',
            'havlu', 'bornoz', 'banyo havlusu'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, evinizin konforunu ve estetiğini artıran kaliteli bir tekstil ürünüdür.',
        'features' => [
            'Kaliteli kumaş',
            'Dayanıklı dikiş',
            'Kolay bakım',
            'Çeşitli desenler'
        ],
        'usage_scenarios' => [
            'Yatak odası için',
            'Salon için',
            'Banyo için',
            'Dekorasyon için'
        ],
        'specifications' => [
            'Malzeme' => 'Pamuk/Polyester',
            'Boyut' => 'cm/m',
            'Renk' => 'Renk seçenekleri',
            'Bakım' => 'Yıkama talimatı'
        ],
        'pros_cons' => [
            'pros' => ['Konforlu', 'Estetik', 'Dayanıklı'],
            'cons' => ['Renk atma riski', 'Ütü gerekebilir']
        ],
        'recommendations' => [
            'İlk yıkamada renk ayrımı yapın',
            '30-40°C\'de yıkayın',
            'Kurutma makinesinden kaçının',
            'Düzenli havalandırın'
        ],
        'additional_info' => 'Ev tekstili ürünleri için bakım talimatlarını takip edin.'
    ],

    'mutfak' => [
        'keywords' => [
            'mutfak', 'tencere', 'tava', 'set', 'cookware',
            'bıçak', 'kesme tahtası', 'kaşık', 'çatal',
            'sofra', 'tabak', 'kase', 'bardak',
            'çay takımı', 'kahve takımı', 'yemek takımı'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, mutfak işlerinizi kolaylaştıran kaliteli ve fonksiyonel bir mutfak ürünüdür.',
        'features' => [
            'Kaliteli malzeme',
            'Dayanıklı yapı',
            'Kolay temizlik',
            'Fonksiyonel tasarım'
        ],
        'usage_scenarios' => [
            'Günlük yemek pişirme için',
            'Misafir ağırlama için',
            'Profesyonel kullanım için'
        ],
        'specifications' => [
            'Malzeme' => 'Paslanmaz çelik/Cam/Porselen',
            'Parça Sayısı' => 'Set içeriği',
            'Bulaşık Makinesi' => 'Uyumlu/Değil',
            'Fırın' => 'Uyumlu/Değil'
        ],
        'pros_cons' => [
            'pros' => ['Dayanıklı', 'Kullanışlı', 'Kolay temizlik'],
            'cons' => ['Özel bakım gerektirebilir', 'Kırılabilir (cam/porselen)']
        ],
        'recommendations' => [
            'İlk kullanımdan önce yıkayın',
            'Yumuşak süngerle temizleyin',
            'Ani sıcaklık değişiminden kaçının',
            'Uygun ocak tipinde kullanın'
        ],
        'additional_info' => 'Mutfak ürünleri için kullanım kılavuzlarını okuyun.'
    ],

    'ev_aleti' => [
        'keywords' => [
            'elektrikli süpürge', 'robot süpürge', 'vacuum',
            'ütü', 'buhar ütüsü', 'iron',
            'su ısıtıcı', 'kettle', 'çay makinesi',
            'kahve makinesi', 'espresso', 'filtre kahve',
            'blender', 'mikser', 'doğrayıcı',
            'fırın', 'mikrodalga', 'oven', 'microwave',
            'buzdolabı', 'çamaşır makinesi', 'kurutma makinesi',
            'dikiş makinesi'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, ev işlerinizi kolaylaştıran modern ve pratik bir elektrikli ev aletidir.',
        'features' => [
            'Güçlü performans',
            'Enerji tasarruflu',
            'Kolay kullanım',
            'Güvenli tasarım'
        ],
        'usage_scenarios' => [
            'Günlük ev işleri için',
            'Zaman tasarrufu için',
            'Pratik kullanım için'
        ],
        'specifications' => [
            'Güç' => 'Watt',
            'Kapasite' => 'Litre/kg',
            'Enerji Sınıfı' => 'A++/A+',
            'Özellikler' => 'Teknik detaylar'
        ],
        'pros_cons' => [
            'pros' => ['Zaman kazandırır', 'Pratik', 'Verimli'],
            'cons' => ['Enerji tüketimi', 'Gürültülü olabilir']
        ],
        'recommendations' => [
            'Kullanım kılavuzunu okuyun',
            'Düzenli bakım yapın',
            'Orijinal yedek parça kullanın',
            'Garanti şartlarını saklayın'
        ],
        'additional_info' => 'Ev aletleri için teknik servis desteği sunuyoruz.'
    ],

    'dekorasyon' => [
        'keywords' => [
            'dekor', 'vazo', 'tablo', 'ayna', 'dekoratif',
            'duvar', 'çerçeve', 'saat', 'mum', 'mumluk',
            'heykel', 'biblo', 'aksesuar', 'süs'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, yaşam alanınıza estetik ve kişilik katan dekoratif bir üründür.',
        'features' => [
            'Şık tasarım',
            'Kaliteli malzeme',
            'Kolay yerleştirme',
            'Çeşitli stil seçenekleri'
        ],
        'usage_scenarios' => [
            'Salon dekorasyonu için',
            'Ofis süslemesi için',
            'Hediye seçeneği olarak',
            'Kişisel dokunuş için'
        ],
        'specifications' => [
            'Malzeme' => 'Cam/Seramik/Ahşap/Metal',
            'Boyut' => 'cm',
            'Renk' => 'Renk seçenekleri',
            'Stil' => 'Modern/Klasik/Vintage'
        ],
        'pros_cons' => [
            'pros' => ['Estetik', 'Kişiselleştirme', 'Çeşitlilik'],
            'cons' => ['Kırılgan olabilir', 'Toz toplar']
        ],
        'recommendations' => [
            'Mekan stiliyle uyumlu seçin',
            'Düzenli toz alın',
            'Sabit yüzeylere yerleştirin',
            'Doğrudan güneş ışığından koruyun'
        ],
        'additional_info' => 'Dekorasyon ürünleri mekanınıza karakter katar.'
    ],

    /*
    |--------------------------------------------------------------------------
    | GRUP 4: KOZMETİK & KİŞİSEL BAKIM
    |--------------------------------------------------------------------------
    */

    'kozmetik' => [
        'keywords' => [
            'makyaj', 'makeup', 'ruj', 'far', 'rimel', 'maskara',
            'fondöten', 'foundation', 'aydınlatıcı', 'highlighter',
            'allık', 'blush', 'eyeliner', 'kaş', 'kontür',
            'oje', 'tırnak', 'nail', 'manikür'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, güzelliğinizi ortaya çıkaran kaliteli ve uzun süreli bir kozmetik ürünüdür.',
        'features' => [
            'Uzun süreli kalıcılık',
            'Cilde uyumlu formül',
            'Zengin renk seçeneği',
            'Kolay uygulanır'
        ],
        'usage_scenarios' => [
            'Günlük makyaj için',
            'Özel günler için',
            'Profesyonel kullanım için'
        ],
        'specifications' => [
            'Ton' => 'Renk/Numara',
            'İçerik' => 'Formül bilgisi',
            'Miktar' => 'ml/gr',
            'Cilt Tipi' => 'Tüm tipler/Özel'
        ],
        'pros_cons' => [
            'pros' => ['Kaliteli', 'Uzun süre kalır', 'Renk çeşitliliği'],
            'cons' => ['Alerjik reaksiyon riski', 'SKT takibi gerekli']
        ],
        'recommendations' => [
            'Cilt tipinize uygunluğunu kontrol edin',
            'Patch test yapın',
            'SKT takibi yapın',
            'Kapalı ve serin yerde saklayın'
        ],
        'additional_info' => 'Kozmetik ürünleri için kullanım talimatlarını okuyun.'
    ],

    'cilt_bakim' => [
        'keywords' => [
            'cilt bakım', 'serum', 'krem', 'tonik', 'skincare',
            'nemlendirici', 'temizleyici', 'maske', 'peeling',
            'güneş kremi', 'SPF', 'anti-aging', 'yaşlanma karşıtı',
            'göz kremi', 'el kremi', 'vücut kremi'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, cildinizin sağlığını ve güzelliğini korumak için özel formülle geliştirilmiş bir bakım ürünüdür.',
        'features' => [
            'Dermatolog onaylı',
            'Cilt tipine uygun',
            'Etkili formül',
            'Doğal içerikler'
        ],
        'usage_scenarios' => [
            'Günlük cilt bakımı için',
            'Gece bakımı için',
            'Özel cilt problemleri için'
        ],
        'specifications' => [
            'Cilt Tipi' => 'Kuru/Yağlı/Karma/Hassas',
            'İçerik' => 'Aktif maddeler',
            'Miktar' => 'ml/gr',
            'SPF' => 'Koruma faktörü (varsa)'
        ],
        'pros_cons' => [
            'pros' => ['Etkili', 'Güvenli', 'Cilde uyumlu'],
            'cons' => ['Alerjik reaksiyon riski', 'Düzenli kullanım gerekir']
        ],
        'recommendations' => [
            'Cilt tipinize uygun seçin',
            'Patch test yapın',
            'Düzenli kullanın',
            'Güneş kremi ile birlikte kullanın'
        ],
        'additional_info' => 'Cilt bakım ürünleri için uzman önerisi alabilirsiniz.'
    ],

    'sac_bakim' => [
        'keywords' => [
            'şampuan', 'saç kremi', 'saç maskesi', 'hair care',
            'saç bakım', 'saç serumu', 'saç yağı',
            'kepeğe karşı', 'anti-dandruff', 'onarıcı',
            'boyalı saç', 'keratin', 'argan'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, saçlarınızın sağlıklı ve güzel görünmesi için özel formülle hazırlanmış bir bakım ürünüdür.',
        'features' => [
            'Saç tipine özel',
            'Besleyici içerik',
            'Güçlendirici',
            'Parlaklık verici'
        ],
        'usage_scenarios' => [
            'Günlük saç yıkama için',
            'Onarım için',
            'Koruma için',
            'Beslenme için'
        ],
        'specifications' => [
            'Saç Tipi' => 'Normal/Kuru/Yağlı/Dökülmeye eğilimli',
            'İçerik' => 'Aktif maddeler',
            'Miktar' => 'ml',
            'Özellik' => 'Sülfatsız/Parabensiz'
        ],
        'pros_cons' => [
            'pros' => ['Besleyici', 'Onarıcı', 'Etkili'],
            'cons' => ['Düzenli kullanım gerekir', 'Saç tipine uygun seçilmeli']
        ],
        'recommendations' => [
            'Saç tipinize uygun seçin',
            'Düzenli kullanın',
            'Ilık suyla durulayın',
            'Aşırı sıcak suyla yıkamayın'
        ],
        'additional_info' => 'Saç bakımı için düzenli kullanım önerilir.'
    ],

    'parfum' => [
        'keywords' => [
            'parfüm', 'koku', 'eau de parfum', 'eau de toilette',
            'fragrance', 'kolonya', 'deodorant', 'roll-on',
            'body spray', 'vücut spreyi'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, kalıcı kokusu ve zarif notalarıyla kendinizi özel hissetmenizi sağlayan bir parfüm ürünüdür.',
        'features' => [
            'Kalıcı koku',
            'Zarif notalar',
            'Uzun süre etkili',
            'Şık şişe tasarımı'
        ],
        'usage_scenarios' => [
            'Günlük kullanım için',
            'Özel günler için',
            'İş ortamı için',
            'Gece için'
        ],
        'specifications' => [
            'Konsantrasyon' => 'EDP/EDT/EDC',
            'Notalar' => 'Üst/Orta/Alt notalar',
            'Miktar' => 'ml',
            'Cinsiyet' => 'Kadın/Erkek/Unisex'
        ],
        'pros_cons' => [
            'pros' => ['Kalıcı', 'Şık', 'Kaliteli'],
            'cons' => ['Alerjik reaksiyon riski', 'Koku tercihi kişisel']
        ],
        'recommendations' => [
            'Test ederek seçin',
            'Nabız noktalarına uygulayın',
            'Doğrudan güneş ışığından uzak tutun',
            'Serin yerde saklayın'
        ],
        'additional_info' => 'Parfümler kişisel tercih ürünleridir, test ederek seçin.'
    ],

    'kisisel_bakim' => [
        'keywords' => [
            'traş makinesi', 'jilet', 'razor', 'shaver',
            'epilasyon', 'epilatör', 'tüy dökücü',
            'saç kurutma', 'fön', 'maşa', 'düzleştirici',
            'diş fırçası', 'diş macunu', 'ağız bakım',
            'erkek bakım', 'aftershave', 'tıraş kremi'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, kişisel bakımınızı kolaylaştıran pratik ve etkili bir bakım ürünüdür.',
        'features' => [
            'Etkili performans',
            'Güvenli kullanım',
            'Pratik tasarım',
            'Uzun ömürlü'
        ],
        'usage_scenarios' => [
            'Günlük bakım için',
            'Hızlı hazırlık için',
            'Profesyonel sonuç için'
        ],
        'specifications' => [
            'Tip' => 'Elektrikli/Manuel',
            'Özellikler' => 'Su geçirmez/Şarjlı',
            'Aksesuar' => 'Yedek başlıklar',
            'Garanti' => 'Süre'
        ],
        'pros_cons' => [
            'pros' => ['Pratik', 'Etkili', 'Hızlı sonuç'],
            'cons' => ['Batarya/şarj bağımlı', 'Yedek parça gerekebilir']
        ],
        'recommendations' => [
            'Düzenli temizlik yapın',
            'Orijinal aksesuarları kullanın',
            'Kullanım kılavuzunu okuyun',
            'Suya dayanıklılığını kontrol edin'
        ],
        'additional_info' => 'Kişisel bakım ürünleri için hijyen önemlidir.'
    ],

    /*
    |--------------------------------------------------------------------------
    | GRUP 5: MARKET & GIDA
    |--------------------------------------------------------------------------
    */

    'gida' => [
        'keywords' => [
            // Kahve & Çay
            'kahve', 'çay', 'coffee', 'tea', 'espresso', 'türk kahvesi',
            // Atıştırmalık
            'cips', 'kraker', 'kuruyemiş', 'snack', 'çerez',
            // Tatlı
            'çikolata', 'şekerleme', 'candy', 'chocolate',
            // Temel gıda
            'makarna', 'pirinç', 'bulgur', 'mercimek', 'pasta', 'bakliyat',
            // Kahvaltılık
            'reçel', 'bal', 'pekmez', 'zeytin', 'kahvaltılık',
            // İçecek
            'meyve suyu', 'kola', 'su', 'içecek', 'beverage',
            // Organik
            'organik', 'organic', 'doğal', 'natural', 'bio',
            // Donmuş
            'dondurulmuş', 'frozen', 'donmuş',
            // Baharat
            'baharat', 'spice', 'kimyon', 'karabiber', 'tarçın',
            // Pet
            'mama', 'kedi maması', 'köpek maması', 'pet food'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, günlük beslenme ihtiyaçlarınızı karşılayan taze ve kaliteli bir gıda ürünüdür.',
        'features' => [
            'Taze ve kaliteli',
            'Hijyenik paketleme',
            'Güvenilir içerik',
            'Lezzetli'
        ],
        'usage_scenarios' => [
            'Günlük tüketim için',
            'Kahvaltı için',
            'Atıştırmalık olarak',
            'Yemek hazırlama için'
        ],
        'specifications' => [
            'İçerik' => 'Bileşenler',
            'Miktar' => 'gr/kg/lt',
            'SKT' => 'Son kullanma tarihi',
            'Saklama' => 'Saklama koşulları'
        ],
        'pros_cons' => [
            'pros' => ['Kaliteli', 'Taze', 'Güvenilir'],
            'cons' => ['SKT takibi gerekli', 'Uygun saklama şartı gerekir']
        ],
        'recommendations' => [
            'SKT kontrolü yapın',
            'Uygun koşullarda saklayın',
            'Hijyenik tüketin',
            'İçeriği kontrol edin'
        ],
        'additional_info' => 'Gıda ürünlerinde SKT ve saklama koşulları önemlidir.'
    ],

    /*
    |--------------------------------------------------------------------------
    | GRUP 6: HOBİ, SPOR & DİĞER
    |--------------------------------------------------------------------------
    */

    'spor' => [
        'keywords' => [
            'spor', 'fitness', 'dambıl', 'ağırlık', 'equipment',
            'yoga', 'matras', 'pilates', 'plates',
            'koşu bandı', 'treadmill', 'bisiklet', 'bike',
            'kamp', 'çadır', 'uyku tulumu', 'camping', 'outdoor',
            'top', 'raket', 'futbol', 'basketbol', 'voleybol'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, spor ve fitness hedeflerinize ulaşmanız için tasarlanmış kaliteli bir spor ekipmanıdır.',
        'features' => [
            'Dayanıklı malzeme',
            'Ergonomik tasarım',
            'Güvenli kullanım',
            'Etkili antrenman'
        ],
        'usage_scenarios' => [
            'Evde antrenman için',
            'Spor salonunda kullanım için',
            'Outdoor aktiviteler için',
            'Grup sporları için'
        ],
        'specifications' => [
            'Malzeme' => 'Malzeme bilgisi',
            'Ağırlık/Kapasite' => 'kg',
            'Boyut' => 'cm/m',
            'Seviye' => 'Başlangıç/Orta/İleri'
        ],
        'pros_cons' => [
            'pros' => ['Kaliteli', 'Dayanıklı', 'Etkili'],
            'cons' => ['Yer kaplıyor olabilir', 'Montaj gerekebilir']
        ],
        'recommendations' => [
            'Seviyenize uygun seçin',
            'Kullanım kılavuzunu okuyun',
            'Düzenli bakım yapın',
            'Güvenlik önlemlerini alın'
        ],
        'additional_info' => 'Spor ekipmanları için doğru kullanım önemlidir.'
    ],

    'hobi' => [
        'keywords' => [
            'oyuncak', 'oyun', 'lego', 'puzzle', 'toy',
            'kitap', 'roman', 'dergi', 'yayın', 'book',
            'gitar', 'piyano', 'bağlama', 'saz', 'instrument',
            'defter', 'kalem', 'silgi', 'kırtasiye', 'stationery',
            'boyama', 'resim', 'sanat', 'art',
            'model', 'maket', 'koleksiyon'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, hobileriniz ve ilgi alanlarınız için özel olarak seçilmiş bir üründür.',
        'features' => [
            'Kaliteli malzeme',
            'Eğlenceli',
            'Gelişim destekleyici',
            'Güvenli'
        ],
        'usage_scenarios' => [
            'Eğlence için',
            'Eğitim için',
            'Yaratıcılık için',
            'Koleksiyon için'
        ],
        'specifications' => [
            'Yaş Grubu' => 'Uygun yaş',
            'Malzeme' => 'Malzeme bilgisi',
            'Özellik' => 'Teknik detaylar',
            'Güvenlik' => 'Sertifikalar'
        ],
        'pros_cons' => [
            'pros' => ['Eğitici', 'Eğlenceli', 'Gelişim destekleyici'],
            'cons' => ['Yaş uygunluğu önemli', 'Küçük parçalar dikkat gerektirir']
        ],
        'recommendations' => [
            'Yaş grubuna uygun seçin',
            'Güvenlik uyarılarını okuyun',
            'Gözetim altında kullanın (çocuklar için)',
            'Temiz tutun'
        ],
        'additional_info' => 'Hobi ürünleri yaratıcılığı destekler.'
    ],

    'oto_aksesuar' => [
        'keywords' => [
            'oto aksesuar', 'araç', 'araba', 'car accessories',
            'oto halı', 'araç kılıfı', 'oto kılıf',
            'araç kamerası', 'dashcam', 'oto teyp',
            'araç şarjı', 'GPS', 'navigasyon',
            'bagaj', 'tavan çantası', 'bisiklet taşıyıcı'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, aracınızın konforunu ve güvenliğini artıran pratik bir oto aksesuarıdır.',
        'features' => [
            'Araç uyumlu',
            'Dayanıklı',
            'Kolay montaj',
            'Fonksiyonel'
        ],
        'usage_scenarios' => [
            'Araç içi konfor için',
            'Güvenlik için',
            'Eğlence için',
            'Koruma için'
        ],
        'specifications' => [
            'Uyumluluk' => 'Araç modelleri',
            'Malzeme' => 'Malzeme bilgisi',
            'Montaj' => 'Kolay/Profesyonel',
            'Özellikler' => 'Teknik detaylar'
        ],
        'pros_cons' => [
            'pros' => ['Pratik', 'Fonksiyonel', 'Araç değerini korur'],
            'cons' => ['Araç uyumluluğu kontrol edilmeli', 'Montaj gerekebilir']
        ],
        'recommendations' => [
            'Aracınıza uygunluğunu kontrol edin',
            'Profesyonel montaj öneririz',
            'Düzenli temizlik yapın',
            'Garanti şartlarını saklayın'
        ],
        'additional_info' => 'Oto aksesuarları için araç uyumluluğu önemlidir.'
    ],

    'bahce' => [
        'keywords' => [
            'bahçe', 'çim biçme', 'çim makinesi', 'garden',
            'saksı', 'vazo', 'bitki', 'plant',
            'tırpan', 'kürek', 'makas', 'garden tools',
            'sulama', 'hortum', 'fiskiye',
            'bahçe mobilya', 'outdoor furniture', 'şezlong'
        ],
        'use_ai' => true,
        'ai_description' => '{name}, bahçe ve dış mekan düzenlemeleriniz için tasarlanmış kaliteli bir üründür.',
        'features' => [
            'Dayanıklı malzeme',
            'Hava koşullarına dayanıklı',
            'Pratik kullanım',
            'Fonksiyonel'
        ],
        'usage_scenarios' => [
            'Bahçe düzenlemesi için',
            'Bitki bakımı için',
            'Dış mekan mobilyası olarak',
            'Hobi amaçlı'
        ],
        'specifications' => [
            'Malzeme' => 'Ahşap/Metal/Plastik',
            'Boyut' => 'cm/m',
            'Dayanıklılık' => 'Hava şartlarına karşı',
            'Bakım' => 'Bakım gereksinimleri'
        ],
        'pros_cons' => [
            'pros' => ['Dayanıklı', 'Fonksiyonel', 'Estetik'],
            'cons' => ['Hava koşullarından etkilenebilir', 'Bakım gerektirir']
        ],
        'recommendations' => [
            'Kış aylarında koruyun',
            'Düzenli bakım yapın',
            'Uygun malzemeyi seçin',
            'Güneşe dayanıklılığı kontrol edin'
        ],
        'additional_info' => 'Bahçe ürünleri için mevsimsel bakım önemlidir.'
    ],

    /*
    |--------------------------------------------------------------------------
    | GENEL (Fallback) - Tüm kategoriler için yedek
    |--------------------------------------------------------------------------
    */

    'genel' => [
        'keywords' => [],
        'use_ai' => true,
        'ai_description' => '{name}, kaliteli ve güvenilir bir üründür. İhtiyaçlarınızı karşılamak için tasarlanmıştır.',
        'features' => [
            'Kaliteli malzeme ve işçilik',
            'Kullanıcı dostu tasarım',
            'Güvenilir performans',
            'Uygun fiyat'
        ],
        'usage_scenarios' => [
            'Günlük kullanım için ideal',
            'Hediye seçeneği olarak uygun',
            'Pratik kullanım'
        ],
        'specifications' => [
            'Fiyat' => '{price} TL',
            'Kategori' => '{category}',
            'Durum' => 'Yeni'
        ],
        'recommendations' => [
            'Kullanım kılavuzunu okuyun',
            'Uygun koşullarda saklayın',
            'Düzenli bakım yapın'
        ],
        'additional_info' => 'Ürün hakkında daha fazla bilgi için müşteri hizmetlerimizle iletişime geçebilirsiniz.'
    ]

];



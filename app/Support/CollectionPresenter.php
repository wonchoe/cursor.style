<?php

namespace App\Support;

use App\Models\Collection;
use Illuminate\Support\Str;

class CollectionPresenter
{

    protected static $charactersTranslations = [
        'en' => 'characters',
        'am' => 'ተዋናይት', // амхарська
        'ar' => 'شخصيات', // арабська
        'bg' => 'герои', // болгарська
        'bn' => 'চরিত্রগুলি', // бенгальська
        'ca' => 'personatges', // каталонська
        'cs' => 'postavy', // чеська
        'da' => 'karakterer', // данська
        'de' => 'Charaktere', // німецька
        'el' => 'χαρακτήρες', // грецька
        'es' => 'personajes', // іспанська
        'et' => 'tegelased', // естонська
        'fa' => 'شخصیت‌ها', // перська
        'fi' => 'hahmot', // фінська
        'fil' => 'mga tauhan', // філіппінська
        'fr' => 'personnages', // французька
        'gu' => 'પાત્રો', // гуджараті
        'he' => 'דמויות', // іврит
        'hi' => 'पात्र', // гінді
        'hr' => 'likovi', // хорватська
        'hu' => 'szereplők', // угорська
        'id' => 'karakter', // індонезійська
        'it' => 'personaggi', // італійська
        'ja' => 'キャラクター', // японська
        'kn' => 'ಪಾತ್ರಗಳು', // каннада
        'ko' => '캐릭터', // корейська
        'lt' => 'personažai', // литовська
        'lv' => 'varoņi', // латиська
        'ml' => 'പ്രതിച്ഛായകൾ', // малаялам
        'mr' => 'चरित्रे', // маратхі
        'ms' => 'watak', // малайська
        'nl' => 'personages', // нідерландська
        'no' => 'karakterer', // норвезька
        'pl' => 'postacie', // польська
        'pt' => 'personagens', // португальська
        'ro' => 'personaje', // румунська
        'ru' => 'персонажи', // російська
        'sk' => 'postavy', // словацька
        'sl' => 'liki', // словенська
        'sr' => 'likovi', // сербська
        'sv' => 'karaktärer', // шведська
        'sw' => 'wahusika', // суахілі
        'ta' => 'பாத்திரங்கள்', // тамільська
        'te' => 'పాత్రలు', // телугу
        'th' => 'ตัวละคร', // тайська
        'tr' => 'karakterler', // турецька
        'uk' => 'персонажі', // українська
        'vi' => 'nhân vật', // вʼєтнамська
        'zh' => '角色', // китайська (спрощена)
    ];

    /**
     * Генерує SEO-дружній слаг для колекції
     *
     * @param Collection $collection
     * @return array{ slug: string, image_path: string }
     */
    public static function seo(Collection $collection): array
    {
        // Назва з перекладу або fallback
        $name = $collection->currentTranslation->name
            ?? $collection->base_name_en
            ?? 'collection-' . $collection->id;

        $slug = slugify($name);

        if (empty($slug)) {
            $slug = 'collection-' . $collection->id;
        }

        $webpImg = str_replace('.png', '.webp', $collection->img);

        // Визначаємо мову
        $locale = app()->getLocale(); // Або $collection->lang, якщо у тебе є поле

        // Масив перекладів “characters”
        $charactersTranslations = self::$charactersTranslations ?? [
            // ... (встав сюди масив із кроку 1, якщо не зробиш static)
        ];

        // Переклад для alt (fallback на англійську)
        $charactersWord = $charactersTranslations[$locale] ?? $charactersTranslations['en'];

        // alt-атрибут
        $alt = $name . ' ' . $charactersWord;

        return [
            'slug' => $slug,
            'url' => "/collections/{$collection->id}-{$slug}",
            'img' => "/{$webpImg}",
            'trans' => $name,
            'alt' => $alt,
        ];
    }
}

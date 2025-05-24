<?php

namespace App\Support;

use App\Models\Collection;
use Illuminate\Support\Str;

class CollectionPresenter
{
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

        $slug = Str::slug($name);

        if (empty($slug)) {
            $slug = 'collection-' . $collection->id;
        }

        return [
            'slug' => $slug,
            'url' => "/collections/{$collection->id}-{$slug}",
            'img' => "/{$collection->img}"
        ];
    }
}

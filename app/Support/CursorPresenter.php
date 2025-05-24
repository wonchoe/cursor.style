<?php

namespace App\Support;

use App\Models\Cursors;
use Illuminate\Support\Str;

class CursorPresenter
{
    /**
     * Генерує SEO-friendly шляхи для курсора
     *
     * @param Cursors $cursor
     * @return array{
     *     slug_url_final: string,
     *     c_file_no_ext: string,
     *     p_file_no_ext: string
     * }
     */
    public static function seo(Cursors $cursor): array
    {
        
        // Беремо переклад категорії → fallback на base_name_en
        $categoryName = $cursor->collection->currentTranslation->name
            ?? $cursor->collection->base_name_en
            ?? '';

        // Переклад курсора → fallback на name_en
        $cursorName = $cursor->currentTranslation->name
            ?? $cursor->name_en
            ?? '';

        // Генеруємо слаги
        $categorySlug = Str::slug($categoryName);
        $cursorSlug = Str::slug($cursorName);

        // Fallback якщо slug порожній (наприклад, китайська або пустий name)
        if (empty($categorySlug)) {
            $categorySlug = 'cat-' . $cursor->collection->id;
        }

        if (empty($cursorSlug)) {
            $cursorSlug = 'cursor-' . $cursor->id;
        }

        // Повний SEO шлях
        $baseSlug = "/collections/{$categorySlug}/{$cursor->id}-{$cursorSlug}";
        $detailsSlug = "/details/{$cursor->id}-{$cursorSlug}";
        $collectionSlug = "/collections/{$cursor->collection->id}-{$categorySlug}";

        return [
            'slug_url_final' => $baseSlug,
            'detailsSlug' => $detailsSlug,
            'collectionSlug' => $collectionSlug,
            'c_file_png' => "{$baseSlug}-cursor.png",
            'p_file_png' => "{$baseSlug}-pointer.png",
            'c_file_no_ext' => "{$baseSlug}-cursor",
            'p_file_no_ext' => "{$baseSlug}-pointer",
            'c_file' => "/{$cursor->c_file}",
            'p_file' => "/{$cursor->p_file}"                        
        ];
    }
}


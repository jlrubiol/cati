<?php

namespace app\components;

use yii\base\Component;

class CatilanguageComponent extends Component
{
    /**
     * Devuelve una locale correspondienten al idioma indicado.
     *
     * Summernote necesita una locale (en-US, es-ES) para configurar el idioma.
     */
    public static function getLocale($language)
    {
        if ('en' === $language) {
            return 'en-US';
        }

        return 'es-ES';
    }
}

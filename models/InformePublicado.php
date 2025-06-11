<?php

namespace app\models;

use app\models\base\InformePublicado as BaseInformePublicado;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "informe_publicado".
 */
class InformePublicado extends BaseInformePublicado
{
    // Si se cambia este valor, hay que cambiar también el array de colores en la vista gestion/lista-informes
    const MAX_VERSION_INFORME = 2;
    const NOMBRES_GM = ['-', 'provisional', 'definitiva'];
    // Si se cambia este valor, hay que cambiar también el array de colores en la vista gestion/lista-informes-doct
    const MAX_VERSION_INFORME_DOCT = 1;
    const NOMBRES_DOCT = ['-', 'definitiva'];
    const MAX_VERSION_INFORME_ICED = 1;

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                // custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                // custom validation rules
            ]
        );
    }

    public static function getNombreVersion($tipo_estudio, $num)
    {
        if ($tipo_estudio == 'grado-master') {
            if ($num >= count(self::NOMBRES_GM)) { $num = count(self::NOMBRES_GM) - 1;}
            return self::NOMBRES_GM[$num];
        } else {
            if ($num >= count(self::NOMBRES_DOCT)) { $num = count(self::NOMBRES_DOCT) - 1; }
            return self::NOMBRES_DOCT[$num];
        }
    }

    /**
     * Devuelve un array estudio_id => InformePublicado.
     */
    public static function getPublicados($anyo, $language)
    {
        $lista = self::findAll(['anyo' => $anyo, 'language' => $language]);
        $publicados = array_combine(array_column($lista, 'estudio_id'), $lista);

        return $publicados;
    }

    public function getVersionMaxima()
    {
        $estudio = Estudio::getEstudio($this->estudio_id);
        if ($estudio->esGradoOMaster()) {
            return self::MAX_VERSION_INFORME;
        } elseif ($estudio->esDoctorado()) {
            return self::MAX_VERSION_INFORME_DOCT;
        } elseif ($estudio->esIced()) {
            return self::MAX_VERSION_INFORME_ICED;
        }

        throw new NotFoundHttpException(sprintf(
            Yii::t('cati', 'Este tipo de estudio no tiene versión máxima del informe.  ☹')
        ));
    }

    /** Devuelve un booleano indicando si el informe no ha llegado aún a su versión final.  */
    public function esEditable()
    {
        return $this->version < $this->getVersionMaxima();
    }
}

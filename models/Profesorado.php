<?php

namespace app\models;

use Yii;
use \app\models\base\Profesorado as BaseProfesorado;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "DATUZ_profesorado".
 */
class Profesorado extends BaseProfesorado
{
    /* Orden en que mostrar la estructura y evolución del profesorado */
    const LISTA = [
        'Cuerpo de Catedráticos de Universidad',  // CU
        'Cuerpo de Profesores Titulares de Universidad',  // TU
        'Cuerpo de Catedráticos de Escuelas Universitarias',  // CEU
        'Cuerpo de Profesores Titulares de Escuelas Universitarias',  // TEU, TEUL
        'Profesor Contratado Doctor',  // COD, CODI
        'Profesor Ayudante Doctor',  // AYD
        'Profesor con contrato indefinido',
        'Profesor con contrato de interinidad',
        'Prof Adjunto de Escuela Univ.',
        'Profesor Asociado',  // AS, ASCL
        'Profesor Asociado en Ciencias de la Salud',  // ASCM
        'Prof Auxiliar o Ayudante',
        'Profesor Ayudante',
        'Profesor Colaborador',  // COL
        'Profesor Emérito',  // EMERPJ, EMER
        'Prof Titular de Escuela Univ.',
        'Profesor Titular de E.U. laboral',
        'Profesor militar con titulación universitaria oficial',
        'Prof Tit de Otras enseñanzas U',
        'Personal Investigador en Formación',  // INV, IJC, IRC, PIF, INVDGA, ¿POS - Invest posdoctorales formación? ¿AY - Ayudante?
        'Colaborador Extraordinario',  // COLEX
        'Personal Docente, Investigador o Técnico',
        'Ayudante',
        'Cuerpo de Profesores de Enseñanza Secundaria',
        'Maestros',
        'Profesor Honorario',
        'Lector (PAS ayuda a la docencia)',
        'Otro personal docente',
        'No Informado',
    ];

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

    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'ano_academico' => Yii::t('models', 'Año académico'),
                'cod_estudio' => Yii::t('models', 'Cód. estudio'),
                'cod_centro' => Yii::t('models', 'Cód. centro'),
                'categoria' => Yii::t('models', 'Categoría'),
                'num_profesores' => Yii::t('models', 'Número de profesores'),
                'num_permanentes' => Yii::t('models', 'Número de permanentes'),
                'num_en_primer_curso' => Yii::t('models', 'En primer curso'),
                'num_sexenios' => Yii::t('models', 'Nº total sexenios'),
                'num_quinquenios' => Yii::t('models', 'Nº total quinquenios'),
                'fecha_carga' => Yii::t('models', 'Fecha de carga'),
                'porcentaje_profesor' => Yii::t('models', 'Porcentaje profesores'),
                'horas_impartidas' => Yii::t('models', 'Horas impartidas'),
                'porcentaje_horas' => Yii::t('models', 'Porcentaje horas'),
                'horas_personal_permanente' => Yii::t('models', 'Horas del personal permanente'),
            ]
        );
    }

    /**
     * Función de comparación para ordenar el array de profesorado por su categoría, según la lista superior.
     */
    private static function cmp($p1, $p2)
    {
        return array_search($p1['categoria'], self::LISTA) - array_search($p2['categoria'], self::LISTA);
    }

    /**
     * Devuelve los centros para los que hay información de un estudio y año.
     */
    private static function getCentros($anyo, $estudio_id_nk)
    {
        $centro_ids = self::find()
            ->select('cod_centro')
            ->where(['cod_estudio' => $estudio_id_nk, 'ano_academico' => $anyo])
            ->distinct()
            ->orderBy('cod_centro')
            ->column();

        $centros = Centro::find()->where(['id' => $centro_ids])->orderBy('id')->all();

        return $centros;
    }

    /**
     * Devuelve las estructuras del profesorado de un estudio y año, clasificadas por centro.
     */
    public static function getEstructuraProfesorado($anyo, $estudio_id_nk)
    {
        $centros = self::getCentros($anyo, $estudio_id_nk);
        $estructuras = [];
        foreach ($centros as $centro) {
            $profesorado_del_centro = self::find()
                ->where(['cod_estudio' => $estudio_id_nk, 'ano_academico' => $anyo, 'cod_centro' => $centro->id])
                ->all();
            usort($profesorado_del_centro, 'self::cmp');

            $estructuras[$centro->nombre] = $profesorado_del_centro;
        }

        return $estructuras;
    }

    /**
     * Devuelve el año de estructura del profesorado de un estudio y año.
     */
    public static function getAnyoUltimaEstructura($anyo, $estudio_id_nk)
    {
        // La evolución y estructura del profesorado se publican a fecha 15 de julio.
        // Vg: Para el profesorado del curso 16/17, se saca el 15/07/2017.
        if ($anyo < date('Y') or date('m') > 7 or (date('m') == 7 and date('d') >= 15)) {
            $anyo_profesorado = $anyo - 1 ;
        } else {
            $anyo_profesorado = $anyo - 2;
        }
        // Dos centros adscritos (el 175-EUPLA y el 179-CUD) proporcionan sus datos después del 30 de junio,
        // y se pasa entonces manualmente a un fichero del que se alimentan las tablas origen.
        // Como tardan unos días, entre tanto no están disponibles los datos, y se produce un error.
        // Para evitarlo usamos en su lugar el último año disponible.
        // Los códigos de estudios afectados son: 141, 142, 143, 150, 162, 707
        $ultimo_anyo_disponible = self::find()->select('ano_academico')->where(['cod_estudio' => $estudio_id_nk])->max('ano_academico');
        if ($ultimo_anyo_disponible < $anyo_profesorado) {
            $anyo_profesorado = $ultimo_anyo_disponible;
        }
        return $anyo_profesorado;
    }

    /**
     * Devuelve las evoluciones del profesorado de un estudio, clasificadas por centro.
     */
    public static function getEvolucionProfesorado($estudio_id_nk)
    {
        // Se publican a fecha 1 de julio, todos los años.
        // Vg: Para el profesorado del curso 16/17, se saca el 01/07/2017.
        $anyo_academico = date('m') < 7 ? date('Y') - 2 : date('Y') - 1;
        $centros = self::getCentros($anyo_academico, $estudio_id_nk);

        $evoluciones = [];
        foreach ($centros as $centro) {
            foreach (self::LISTA as $categoria) {
                $datos_categoria = [];
                for ($anyo = $anyo_academico - 6; $anyo <= $anyo_academico; ++$anyo) {
                    $dato = (new yii\db\Query)
                        ->select(['num_profesores'])
                        ->from('DATUZ_profesorado')
                        ->where(
                            [
                                'cod_estudio' => $estudio_id_nk,
                                'cod_centro' => $centro->id,
                                'categoria' => $categoria,
                                'ano_academico' => $anyo,
                            ]
                        )->scalar();
                    $datos_categoria[$anyo] = $dato ?: 0;
                }
                if (empty(array_filter($datos_categoria))) {
                    continue;
                }
                $datos_categoria['categoria'] = $categoria;
                $evoluciones[$centro->nombre][] = $datos_categoria;
            }
            // Tras recorrer todas las categorías, añadimos los porcentajes de
            // horas impartidad por PDI permanente y por PDI no permanente.
            $datos_hpp = [];
            $datos_hpnp = [];
            for ($anyo = $anyo_academico - 6; $anyo <= $anyo_academico; ++$anyo) {
                $total_horas_impartidas = (new yii\db\Query)
                    ->select('SUM(horas_impartidas)')
                    ->from('DATUZ_profesorado')
                    ->where(['cod_estudio' => $estudio_id_nk, 'cod_centro' => $centro->id, 'ano_academico' => $anyo])
                    ->scalar();

                if ($total_horas_impartidas) {
                    $total_horas_permanentes = (new yii\db\Query)
                        ->select('SUM(horas_personal_permanente)')
                        ->from('DATUZ_profesorado')
                        ->where(['cod_estudio' => $estudio_id_nk, 'cod_centro' => $centro->id, 'ano_academico' => $anyo])
                        ->scalar();

                    $fraccion_horas_permanentes = $total_horas_permanentes / $total_horas_impartidas;
                    $datos_hpp[$anyo] = Yii::$app->formatter->asPercent($fraccion_horas_permanentes, 2);
                    $datos_hpnp[$anyo] = Yii::$app->formatter->asPercent(1 - $fraccion_horas_permanentes, 2);
                } else {
                    $datos_hpp[$anyo] = '—';
                    $datos_hpnp[$anyo] = '—';
                }
            }
            $datos_hpp['categoria'] = 'Horas profesorado permanente';
            $evoluciones[$centro->nombre][] = $datos_hpp;
            $datos_hpnp['categoria'] = 'Horas profesorado no permanente';
            $evoluciones[$centro->nombre][] = $datos_hpnp;
        }

        return $evoluciones;
    }

    /**
     * Devuelve un array de los estudios de los que hay información en el año indicado.
     */
    public static function getEstudiosDelAnyo($anyo)
    {
        $id_nks = self::find()
            ->select('cod_estudio')
            ->where(['ano_academico' => $anyo])
            ->distinct()
            ->column();

        $estudios = Estudio::find()->where(['id_nk' => $id_nks, 'anyo_academico' => $anyo])->all();

        /*
         * La tabla DATUZ_profesorado incluye códigos que no tenemos en la tabla Estudio.
         * Vg: 155 -> Actividades académicas complementarias.
         * En estos casos el estudio es NULL. Lo quitamos.
         */
        $estudios = array_filter($estudios);

        return $estudios;
    }

    /**
     * Devuelve la suma del atributo indicado de un array de objetos.
     */
    public static function getTotal($datos, $campo)
    {
        return array_sum(array_column($datos, $campo));
    }
}

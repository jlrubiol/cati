<?php

namespace app\models;

use yii\base\Model;
use yii\db\Query;
use yii\helpers\Url;
use Yii;

/*
 * Las asignaturas de los itinerarios proceden de la tabla `ODS_ASIG_EST_CENT_PLAN_ITINER`
 * El nombre de los itinerarios procede de la tabla `ODS_ITINERARIO_DESCRIPCION`
 */
class Aecp extends Model
{
    /**
     * Devuelve las asignaturas de un plan.
     */
    public function getAsignaturas($estudio_id, $centro_id, $plan_id_nk, $anyo_academico)
    {
        $language = Yii::$app->language;

        /* XXX GROUP_CONCAT no es SQL estándar */
        $query = (new Query())
            ->select([
                'a.anyo_academico',
                'a.centro_id',
                'ac.curso',
                'periodo' => "GROUP_CONCAT(DISTINCT aper.periodo ORDER BY aper.periodo SEPARATOR ', ')",
                'a.asignatura_id',
                'al.descripcion',
                'ac.clase',
                'asig.creditos',
                'a.situacion',
                'a.numero_plazas',
                'a.hay_limite_plazas',
                'idioma' => "GROUP_CONCAT(DISTINCT ai.idioma ORDER BY ai.idioma SEPARATOR ', ')",
            ])
            ->from(['a' => 'aecp'])
            ->join(
                'LEFT JOIN',
                ['aper' => 'aecp_periodo'],
                'a.anyo_academico = aper.anyo_academico AND
                a.estudio_id = aper.estudio_id AND
                a.centro_id = aper.centro_id AND
                a.plan_id_nk = aper.plan_id_nk AND
                a.asignatura_id = aper.asignatura_id'
            )->join(
                'INNER JOIN',
                ['ac' => 'aecp_curso'],
                'a.anyo_academico = ac.anyo_academico AND
                a.estudio_id = ac.estudio_id AND
                a.centro_id = ac.centro_id AND
                a.plan_id_nk = ac.plan_id_nk AND
                a.asignatura_id = ac.asignatura_id'
            )->join(
                'LEFT JOIN',
                ['ai' => 'aecp_idioma'],
                'a.anyo_academico = ai.anyo_academico AND
                a.estudio_id = ai.estudio_id AND
                a.centro_id = ai.centro_id AND
                a.plan_id_nk = ai.plan_id_nk AND
                a.asignatura_id = ai.asignatura_id'
            )->join(
                'INNER JOIN',
                ['asig' => 'asignatura'],
                'a.asignatura_id = asig.id'
            )->join(
                'INNER JOIN',
                ['al' => 'asignatura_lang'],
                'a.asignatura_id = al.asignatura_id'
            )->where([
                'a.anyo_academico' => $anyo_academico,
                'a.estudio_id' => $estudio_id,
                'a.centro_id' => $centro_id,
                'a.plan_id_nk' => $plan_id_nk,
                'al.language' => $language,
            ])->groupBy(
                'a.anyo_academico, a.centro_id, ac.curso, a.asignatura_id, al.descripcion, clase, asig.creditos,' .
                    ' a.situacion, a.numero_plazas, a.hay_limite_plazas'
            )->orderBy('ac.curso, periodo, a.asignatura_id');

        $command = $query->createCommand();
        // die(var_dump($command->rawSql));  // Returns the raw SQL by inserting parameter values into the corresponding placeholders
        $asignaturas = $command->queryAll();

        return $asignaturas;
    }

    /**
     * Devuelve los detalles de una asignatura.
     */
    public function getAsignatura($asignatura_id, $estudio_id, $centro_id, $plan_id_nk, $anyo_academico)
    {
        $language = Yii::$app->language;

        $query = (new Query())
            ->select([
                'curso' => "GROUP_CONCAT(DISTINCT ac.curso ORDER BY ac.curso SEPARATOR ', ')", // 'ac.curso',
                'a.asignatura_id',
                'al.descripcion',
                'ac.clase',
                'tipo' => 'tal.descripcion',
                'asig.creditos',
                'periodo' => "GROUP_CONCAT(DISTINCT aper.periodo ORDER BY aper.periodo SEPARATOR ', ')",
                'a.situacion',
                'a.numero_plazas',
                'a.hay_limite_plazas',
                'idioma' => "GROUP_CONCAT(DISTINCT ai.idioma ORDER BY ai.idioma SEPARATOR ', ')",
            ])
            ->from(['a' => 'aecp'])
            ->join(
                'LEFT JOIN',
                ['aper' => 'aecp_periodo'],
                'a.anyo_academico = aper.anyo_academico AND
                a.estudio_id = aper.estudio_id AND
                a.centro_id = aper.centro_id AND
                a.plan_id_nk = aper.plan_id_nk AND
                a.asignatura_id = aper.asignatura_id'
            )->join(
                'INNER JOIN',
                ['ac' => 'aecp_curso'],
                'a.anyo_academico = ac.anyo_academico AND
                a.estudio_id = ac.estudio_id AND
                a.centro_id = ac.centro_id AND
                a.plan_id_nk = ac.plan_id_nk AND
                a.asignatura_id = ac.asignatura_id'
            )->join(
                'LEFT JOIN',
                ['ai' => 'aecp_idioma'],
                'a.anyo_academico = ai.anyo_academico AND
                a.estudio_id = ai.estudio_id AND
                a.centro_id = ai.centro_id AND
                a.plan_id_nk = ai.plan_id_nk AND
                a.asignatura_id = ai.asignatura_id'
            )->join(
                'INNER JOIN',
                ['asig' => 'asignatura'],
                'a.asignatura_id = asig.id'
            )->join(
                'INNER JOIN',
                ['al' => 'asignatura_lang'],
                'a.asignatura_id = al.asignatura_id'
            )->join(
                'INNER JOIN',
                ['tal' => 'tipoasignatura_lang'],
                'asig.tipoasignatura_id = tal.tipoasignatura_id'
            )->where([
                'a.asignatura_id' => $asignatura_id,
                'a.anyo_academico' => $anyo_academico,
                'a.estudio_id' => $estudio_id,
                'a.centro_id' => $centro_id,
                'a.plan_id_nk' => $plan_id_nk,
                'al.language' => $language,
                'tal.language' => $language,
            ])->groupBy(
                'a.asignatura_id, al.descripcion, tipo, clase,' .
                    ' asig.creditos, a.situacion, a.numero_plazas, a.hay_limite_plazas'
            )->orderBy('curso, a.asignatura_id');

        $command = $query->createCommand();
        // die(var_dump($command->rawSql));  // returns the actual SQL
        $asignatura = $command->queryOne();
        if (!$asignatura) {
            return null;
        }

        $query2 = (new Query())
            ->select([
                'ap.nip',
                'ap.nombre_completo',
                'c.URL',
            ])
            ->from(['ap' => 'aecp_profesor'])
            ->where([
                'ap.anyo_academico' => $anyo_academico,
                'ap.estudio_id' => $estudio_id,
                'ap.centro_id' => $centro_id,
                'ap.plan_id_nk' => $plan_id_nk,
                'ap.asignatura_id' => $asignatura_id,
            ])->join(
                'LEFT JOIN',
                ['c' => 'curriculum'],
                'ap.nip = c.NIP'
            )->distinct()->orderBy('ap.apellido1, ap.apellido2');
        $command2 = $query2->createCommand();
        $profesores = $command2->queryAll();

        return ['asignatura' => $asignatura, 'profesores' => $profesores];
    }

    /**
     * Devuelve los itinerarios de un plan y año.
     */
    public function getItinerarios($centro_id, $plan_id_nk, $anyo)
    {
        $language = Yii::$app->language;

        $query = (new Query())
            ->select([
                'i.id_nk',
                'il.descripcion',
            ])
            ->from(['aitin' => 'aecp_itinerario'])
            ->join(
                'INNER JOIN',
                ['i' => 'itinerario'],
                'aitin.itinerario_id_nk = i.id_nk AND
                aitin.anyo_academico = i.anyo_academico'
            )->join(
                'INNER JOIN',
                ['il' => 'itinerario_lang'],
                'i.id = il.itinerario_id'
            )->where([
                'aitin.plan_id_nk' => $plan_id_nk,
                'aitin.centro_id' => $centro_id,
                'aitin.anyo_academico' => $anyo,
                'il.language' => $language,
            ])->orderBy('il.descripcion')
            ->distinct();
        $command = $query->createCommand();
        // die(var_dump($command->rawSql));  // returns the actual SQL
        $itinerarios = $command->queryAll();

        return $itinerarios;
    }

    /**
     * Devuelve las asignaturas de un itinerario y año.
     */
    public function getAsignaturasItinerario($estudio_id, $centro_id, $plan_id_nk, $itinerario_id_nk, $anyo)
    {
        $language = Yii::$app->language;

        /* XXX GROUP_CONCAT no es SQL estándar */
        $query = (new Query())
            ->select([
                'a.anyo_academico',
                'ac.curso',
                'periodo' => "GROUP_CONCAT(DISTINCT aper.periodo ORDER BY aper.periodo SEPARATOR ', ')",
                'a.asignatura_id',
                'al.descripcion',
                'ac.clase',
                'asig.creditos',
                'a.situacion',
                'a.numero_plazas',
                'a.hay_limite_plazas',
                'idioma' => "GROUP_CONCAT(DISTINCT ai.idioma ORDER BY ai.idioma SEPARATOR ', ')",
            ])
            ->from(['a' => 'aecp'])
            ->join(
                'LEFT JOIN',
                ['aper' => 'aecp_periodo'],
                'a.anyo_academico = aper.anyo_academico AND
                a.estudio_id = aper.estudio_id AND
                a.centro_id = aper.centro_id AND
                a.plan_id_nk = aper.plan_id_nk AND
                a.asignatura_id = aper.asignatura_id'
            )->join(
                'INNER JOIN',
                ['ac' => 'aecp_curso'],
                'a.anyo_academico = ac.anyo_academico AND
                a.estudio_id = ac.estudio_id AND
                a.centro_id = ac.centro_id AND
                a.plan_id_nk = ac.plan_id_nk AND
                a.asignatura_id = ac.asignatura_id'
            )->join(
                'LEFT JOIN',
                ['ai' => 'aecp_idioma'],
                'a.anyo_academico = ai.anyo_academico AND
                a.estudio_id = ai.estudio_id AND
                a.centro_id = ai.centro_id AND
                a.plan_id_nk = ai.plan_id_nk AND
                a.asignatura_id = ai.asignatura_id'
            )->join(
                'INNER JOIN',
                ['asig' => 'asignatura'],
                'a.asignatura_id = asig.id'
            )->join(
                'INNER JOIN',
                ['al' => 'asignatura_lang'],
                'a.asignatura_id = al.asignatura_id'
            )->join(
                'INNER JOIN',
                ['aitin' => 'aecp_itinerario'],
                'a.anyo_academico = aitin.anyo_academico AND
                a.estudio_id = aitin.estudio_id AND
                a.centro_id = aitin.centro_id AND
                a.plan_id_nk = aitin.plan_id_nk AND
                a.asignatura_id = aitin.asignatura_id'
            )->where([
                'a.anyo_academico' => $anyo,
                'a.estudio_id' => $estudio_id,
                'a.centro_id' => $centro_id,
                'a.plan_id_nk' => $plan_id_nk,
                'al.language' => $language,
                'aitin.itinerario_id_nk' => $itinerario_id_nk,
            ])->groupBy(
                'a.anyo_academico, ac.curso, a.asignatura_id, al.descripcion, clase, asig.creditos,' .
                    ' a.situacion, a.numero_plazas, a.hay_limite_plazas'
            )->orderBy('ac.curso, periodo, a.asignatura_id');

        $command = $query->createCommand();
        // die(var_dump($command->sql)); // returns the actual SQL
        $asignaturas = $command->queryAll();

        return $asignaturas;
    }

    /**
     * Devuelve el nombre de un itinerario y año.
     */
    public function getNombreItinerario($itinerario_id_nk, $anyo)
    {
        $language = Yii::$app->language;

        $query = (new Query())
            ->select('il.descripcion')
            ->from(['il' => 'itinerario_lang'])
            ->join(
                'INNER JOIN',
                ['i' => 'itinerario'],
                'i.id = il.itinerario_id'
            )->where([
                'i.id_nk' => $itinerario_id_nk,
                'i.anyo_academico' => $anyo,
                'il.language' => $language,
            ]);
        $command = $query->createCommand();
        // die(var_dump($command->sql)); // returns the actual SQL
        $nombre = $command->queryScalar();

        return $nombre;
    }

    /**
     * Devuelve las asignaturas que tienen guía docente publicada.
     */
    public function getGuiasPublicadas($anyo_academico)
    {
        $language = Yii::$app->language;

        $query = (new Query())
            ->select([
                'a.asignatura_id',
                'a.estudio_id',
                'a.centro_id',
                'a.plan_id_nk',
                'al.descripcion',
            ])->from(['a' => 'aecp'])
            ->join(
                'INNER JOIN',
                ['al' => 'asignatura_lang'],
                'a.asignatura_id = al.asignatura_id'
            )->where([
                'a.anyo_academico' => $anyo_academico,
                'al.language' => $language,
            ])->orderBy('a.plan_id_nk, al.descripcion');
        $command = $query->createCommand();
        // die(var_dump($command->sql)); // returns the actual SQL
        $asignaturas = $command->queryAll();

        $guias = [];
        $pdfdir = Yii::getAlias('@webroot') . '/pdf/guias/' . $anyo_academico;
        $pdfdirurl = Url::base() . '/pdf/guias/' . $anyo_academico;
        foreach ($asignaturas as $asignatura) {
            $file = "{$asignatura['asignatura_id']}_{$language}.pdf";
            $stat = @stat("{$pdfdir}/{$file}");
            if ($stat) {
                $asignatura['url_guia'] = "{$pdfdirurl}/{$file}";
                $asignatura['fecha'] = $stat['mtime'];
                $guias[] = $asignatura;
            }
        }

        return $guias;
    }

    /**
     * Devuelve los nombres (en inglés) de las asignaturas que se imparten
     * en otros idiomas distintos al castellano.
     */
    public static function getEnOtrosIdiomas($anyo_academico)
    {
        $query = (new Query())
            ->select([
                'a.anyo_academico',
                'a.asignatura_id',
                'a.estudio_id',
                'a.centro_id',
                'a.plan_id_nk',
                'al.descripcion',
                'el.nombre',
            ])->from(['a' => 'aecp_idioma'])
            ->join(
                'INNER JOIN',
                ['al' => 'asignatura_lang'],
                'a.asignatura_id = al.asignatura_id'
            )->join(
                'INNER JOIN',
                ['el' => 'estudio_lang'],
                'a.estudio_id = el.estudio_id'
            )->where(
                ['<>', 'a.idioma', 'Castellano']
            )->andWhere([
                'a.anyo_academico' => $anyo_academico,
                'al.language' => 'en',
                'el.language' => 'en',
            ])->orderBy('a.estudio_id, a.plan_id_nk, al.descripcion');
        $command = $query->createCommand();
        // die(var_dump($command->sql));  // returns the actual SQL
        $asignaturas = $command->queryAll();

        $resultados = [];
        foreach ($asignaturas as $a) {
            $resultados[$a['estudio_id']][] = $a;
        }

        $home = Url::base(true);
        $resultados2 = [];
        foreach ($resultados as $estudio_id => $asigs_del_estudio) {
            $enlaces = array_map(
                function ($a) use ($home) {
                    return sprintf(
                        "<a href='$home/estudio/asignatura?anyo_academico=%d&asignatura_id=%s&estudio_id=%d&centro_id=%d&plan_id_nk=%d'>%s</a>",
                        $a['anyo_academico'],
                        $a['asignatura_id'],
                        $a['estudio_id'],
                        $a['centro_id'],
                        $a['plan_id_nk'],
                        $a['descripcion']
                    );
                },
                $asigs_del_estudio
            );
            $resultados2[] = [
                'estudio_id' => $estudio_id,
                'link_estudio' => sprintf(
                    "<a href='$home/estudio/ver?id=%d'>%s</a>",
                    $estudio_id,
                    $asigs_del_estudio[0]['nombre']
                ),
                'links_asignaturas' => $enlaces,
            ];
        }

        return $resultados2;
    }

    /**
     * Devuelve los nombres (en inglés) de las asignaturas que se imparten
     * en inglés.
     */
    public static function getInEnglish($anyo_academico)
    {
        $query = (new Query())
            ->select([
                'a.anyo_academico',
                'a.asignatura_id',
                'a.estudio_id',
                'a.estudio_id_nk',
                'a.centro_id',
                'a.plan_id_nk',
                'al.descripcion',
                'el.nombre',
            ])->from(['a' => 'aecp_idioma'])
            ->join(
                'INNER JOIN',
                ['al' => 'asignatura_lang'],
                'a.asignatura_id = al.asignatura_id'
            )->join(
                'INNER JOIN',
                ['el' => 'estudio_lang'],
                'a.estudio_id = el.estudio_id'
            )->where([
                'a.idioma' => 'English',
                'a.anyo_academico' => $anyo_academico,
                'al.language' => 'en',
                'el.language' => 'en',
            ])->andWhere(['<>', 'a.plan_id_nk', 415])
            ->orderBy('a.estudio_id, a.plan_id_nk, al.descripcion');
        $command = $query->createCommand();
        // die(var_dump($command->sql));  // returns the actual SQL
        $asignaturas = $command->queryAll();

        $resultados = [];
        foreach ($asignaturas as $a) {
            $resultados[$a['estudio_id']][] = $a;
        }

        $home = Url::base(true);
        $resultados2 = [];
        foreach ($resultados as $estudio_id => $asigs_del_estudio) {
            $enlaces = array_map(
                function ($a) use ($home) {
                    return sprintf(
                        "<a href='$home/estudio/asignatura?anyo_academico=%d&asignatura_id=%s&estudio_id=%d&centro_id=%d&plan_id_nk=%d'>%s</a>",
                        $a['anyo_academico'],
                        $a['asignatura_id'],
                        $a['estudio_id'],
                        $a['centro_id'],
                        $a['plan_id_nk'],
                        $a['descripcion']
                    );
                },
                $asigs_del_estudio
            );
            $resultados2[] = [
                'estudio_id' => $estudio_id,
                'link_estudio' => sprintf(
                    "<a href='$home/estudio/ver?id=%d'>%s</a>",
                    $asigs_del_estudio[0]['estudio_id_nk'],
                    $asigs_del_estudio[0]['nombre']
                ),
                'links_asignaturas' => $enlaces,
            ];
        }

        return $resultados2;
    }

    /**
     * Devuelve los nombres (en castellano) de las asignaturas que se imparten
     * en inglés.
     */
    public static function getEnIngles($anyo_academico)
    {
        $query = (new Query())
            ->select([
                'a.anyo_academico',
                'a.asignatura_id',
                'a.estudio_id',
                'a.estudio_id_nk',
                'a.centro_id',
                'a.plan_id_nk',
                'al.descripcion',
                'el.nombre',
            ])->from(['a' => 'aecp_idioma'])
            ->join(
                'INNER JOIN',
                ['al' => 'asignatura_lang'],
                'a.asignatura_id = al.asignatura_id'
            )->join(
                'INNER JOIN',
                ['el' => 'estudio_lang'],
                'a.estudio_id = el.estudio_id'
            )->where([
                'a.idioma' => 'English',
                'a.anyo_academico' => $anyo_academico,
                'al.language' => 'es',
                'el.language' => 'es',
            ])->andWhere(['<>', 'a.plan_id_nk', 415])
            ->orderBy('a.estudio_id, a.plan_id_nk, al.descripcion');
        $command = $query->createCommand();
        // die(var_dump($command->sql));  // returns the actual SQL
        $asignaturas = $command->queryAll();

        $resultados = [];
        foreach ($asignaturas as $a) {
            $resultados[$a['estudio_id']][] = $a;
        }

        $home = Url::base(true);
        $resultados2 = [];
        foreach ($resultados as $estudio_id => $asigs_del_estudio) {
            $enlaces = array_map(
                function ($a) use ($home) {
                    return sprintf(
                        "<a href='$home/estudio/asignatura?anyo_academico=%d&asignatura_id=%s&estudio_id=%d&centro_id=%d&plan_id_nk=%d'>%s</a>",
                        $a['anyo_academico'],
                        $a['asignatura_id'],
                        $a['estudio_id'],
                        $a['centro_id'],
                        $a['plan_id_nk'],
                        $a['descripcion']
                    );
                },
                $asigs_del_estudio
            );
            $resultados2[] = [
                'estudio_id' => $estudio_id,
                'link_estudio' => sprintf(
                    "<a href='$home/estudio/ver?id=%d'>%s</a>",
                    $asigs_del_estudio[0]['estudio_id_nk'],
                    $asigs_del_estudio[0]['nombre']
                ),
                'links_asignaturas' => $enlaces,
            ];
        }

        return $resultados2;
    }
}

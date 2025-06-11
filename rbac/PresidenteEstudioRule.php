<?php

namespace app\rbac;

use Yii;
use yii\rbac\Rule;
use app\models\Agente;

/**
 * Esta regla sirve para limitar quién puede editar un informe de evaluación
 * o un plan de mejora.
 *
 * El informe de evaluación pueden editarlo los coordinadores de los planes
 * de _esa_ titulación.
 * Además tendrán permisos:
 * - los delegados que los coordinadores puedan nombrar
 * - la unidad de calidad y racionalización
 * - el administrador de la aplicación
 *
 * El plan de mejora pueden editarlo los anteriores, y además:
 * - el presidente de la Comisión de garantía de la calidad de esa titulación.
 * - los delegados del presidente de la CGC
 *
 * @author Quique <quique@unizar.es>
 */
class PresidenteEstudioRule extends Rule
{
    /**
     * @var string name of the rule
     */
    public $name = 'esPresidenteDelEstudio';

    /**
     * Executes the rule.
     *
     * @param string|int $user   the user ID. This should be either an integer
     *                           or a string representing the unique identifier
     *                           of a user. See [[\yii\web\User::id]].
     * @param Item       $item   the role or permission that this rule is
     *                           associated with
     * @param array      $params parameters passed to
     *                           [[CheckAccessInterface::checkAccess()]]
     *
     * @return bool a value indicating whether the rule permits the auth item
     *              it is associated with
     */
    public function execute($user, $item, $params)
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        if (!isset($params['estudio'])) {
            return false;
        }

        $estudio = $params['estudio'];
        $usuario = Yii::$app->user->identity;

        $presidentes = Agente::find()
            ->where([
                'estudio_id_nk' => $estudio->id_nk,
                'comision_id' => ['G', 'dele_cgc'],
            ])
            ->andWhere(['like', 'rol', 'resident'])  // Genera `rol LIKE '%resident%'`
            ->all();

        foreach ($presidentes as $presidente) {
            if ((string)$presidente->nip === $usuario->username) {
                return true;
            }
        }

        return false;
    }
}

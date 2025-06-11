<?php
/**
 * Regla para determinar si el usuario es coordinador de un estudio.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */

namespace app\rbac;

use Yii;
use yii\rbac\Rule;
use app\models\Agente;

/**
 * Esta regla sirve para limitar quién puede ver los extractos del PAIM de un centro.
 *
 * @author Quique <quique@unizar.es>
 */
class DecanoCentroRule extends Rule
{
    /**
     * @var string name of the rule
     */
    public $name = 'esDecanoDelCentro';

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
	print("============= EsDecanoDelCentro =============\n");
        if (Yii::$app->user->isGuest) {
            return false;
        }

        if (!isset($params['centro'])) {
            return false;
        }

        $centro = $params['centro'];
        $webuser = Yii::$app->user;
        $usuario = $webuser->identity;

	print("ID CENTRO: " . $centro->id);
	print("USERNAME: " . $usuario->username);
        return $centro->nip_decano == $usuario->username;
    }
}

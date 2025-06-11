<?php
/**
 * Modelo de la tabla User.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 */

namespace app\models;

use Yii;
use yii\db\Query;
use \Da\User\Model\User as BaseUser;

class User extends BaseUser
{
    /**
     * Permite ordenar un array de usuarios por su nombre del perfil, sin distinguir mayúsculas y minúsculas.
     */
    public function cmpProfileName($a, $b)
    {
        return strcasecmp($a->profile->name, $b->profile->name);
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Busca en Gestión de Identidades la identidad correspondiente a un NIP.
     */
    public static function findIdentidadByNip($nip)
    {
        $query = (new Query())
            ->select('*')
            ->from(['i' => 'GESTIDEN.GI_V_IDENTIDAD'])
            ->where(['i.NIP' => $nip]);

        $command = $query->createCommand(Yii::$app->dbident);
        // die(var_dump($command->rawSql));  // Returns the raw SQL by inserting parameter values into the corresponding placeholders
        $identidad = $command->queryOne();
        if ($identidad) {
            $identidad = array_map('utf8_encode', $identidad);
        }

        return $identidad;
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @throws HttpException if the model cannot be found
     *
     * @param int $id
     *
     * @return User the loaded model
     */
    public static function getModel($id)
    {
        if (null !== ($model = self::findOne(['id' => $id]))) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('models', 'No se ha encontrado ese usuario.  ☹'));
    }

    /**
     * Devuelve si el usuario es presidente o secretario de la Comisión de Doctorado.
     *
     * La Comisión de Doctorado está integrada por diez coordinadores de programas de doctorado de la UZ.
     * Entre ellos elegirán a su presidente, quien designará entre los miembros de la Comisión al Secretario.
     * El presidente y el secretario de la Comisión de Doctorado se configuran en `config/params.php`.
     */
    public function esComisionDoctorado()
    {
        $presi_cd = Yii::$app->params['presiDoctNip'];
        $secre_cd = Yii::$app->params['secreDoctNip'];
        return in_array($this->username, [$presi_cd, $secre_cd]);
    }
}

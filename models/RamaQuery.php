<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Rama]].
 *
 * @see Rama
 */
class RamaQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return Rama[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Rama|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

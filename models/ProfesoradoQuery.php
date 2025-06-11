<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Profesorado]].
 *
 * @see Profesorado
 */
class ProfesoradoQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return Profesorado[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Profesorado|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

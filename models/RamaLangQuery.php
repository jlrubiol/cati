<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[RamaLang]].
 *
 * @see RamaLang
 */
class RamaLangQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return RamaLang[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return RamaLang|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

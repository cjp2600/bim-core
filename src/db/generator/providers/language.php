<?php
namespace Bim\Db\Generator\Providers;

use Bim\Db\Generator\Code;
use Bim\Exception\BimException;

/**
 * Class Language
 * @package Bim\Db\Generator\Providers
 */
class Language extends Code
{
    /**
     * метод для генерации кода добавления нового языка системы
     * @param array $langId
     * @return string
     * @throws BimException
     */
    public function generateAddCode($langId)
    {
        $this->checkParams($langId);

        $langData = $this->ownerItemDbData;
        unset($langData['ID']);
        $addFields = $langData;

        return  $this->getMethodContent('Bim\Db\Main\LanguageIntegrate', 'Add', array($addFields));
    }

    /**
     * метод для генерации кода обновления  языка системы
     * @param $params array
     * @return string
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);
        $langData = $this->ownerItemDbData;
        $updateFields = $langData;
        return $this->getMethodContent('Bim\Db\Main\LanguageIntegrate', 'Update', array($updateFields['LID'], $updateFields));

    }

    /**
     * метод для генерации кода удаления  языка системы
     * @param array $langId
     * @return mixed
     * @throws BimException
     */
    public function generateDeleteCode($langId)
    {
        $this->checkParams($langId);
        $langData = $this->ownerItemDbData;
        return $this->getMethodContent('Bim\Db\Main\LanguageIntegrate', 'Delete', array($langData['LID']));
    }

    /**
     * @param array $langId
     * @return mixed|void
     * @throws BimException
     * @internal param array $params
     */
    public function checkParams($langId)
    {
        $lang  = new \CLanguage();
        if (!isset($langId) || empty($langId)) {
            throw new BimException('В параметрах не найден langId');
        }

        $this->ownerItemDbData = array();
        $langDbRes = $lang->GetList($by = "lid", $order = "desc", array('LID' => $langId));
        if ($langDbRes === false || !$langDbRes->SelectedRowsCount()) {
            throw new BimException('Не найден язык системы с id = ' . $langId);
        }

        $langData = $langDbRes->Fetch();
        $this->ownerItemDbData = $langData;
    }


}
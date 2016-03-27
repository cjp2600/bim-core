<?php

/**
 * Class LanguageGen
 * класс для генерацияя кода изменений в языках системы
 *
 * @package Bitrix\Adv_Preset\Main_Generator
 */
class LanguageGen extends CodeGenerator
{


    public function __construct()
    {
    }

    /**
     * метод для генерации кода добавления нового языка системы
     * @param $params array
     * @return string
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $langData = $this->ownerItemDbData;
        $code = '<?php' . PHP_EOL . '/*  Добавляем новый язык системы */' . PHP_EOL . PHP_EOL;
        unset($langData['ID']);
        $addFields = $langData;

        $code = $code . $this->buildCode('LanguageIntegrate', 'Add', array($addFields)) . PHP_EOL . PHP_EOL;

        return $code;

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
        $code = '<?php' . PHP_EOL . '/*  Обновляем язык системы */' . PHP_EOL . PHP_EOL;
        $updateFields = $langData;

        $code = $code . $this->buildCode('LanguageIntegrate', 'Update',
                array($updateFields['LID'], $updateFields)) . PHP_EOL . PHP_EOL;

        return $code;

    }

    /**
     * метод для генерации кода удаления  языка системы
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $langData = $this->ownerItemDbData;
        $code = '<?php' . PHP_EOL . '/*  Удаляем язык системы  */' . PHP_EOL . PHP_EOL;

        $code = $code . $this->buildCode('LanguageIntegrate', 'Delete', array($langData['LID'])) . PHP_EOL . PHP_EOL;

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     *          langId => (string) id языка
     *   )
     * @return mixed
     */
    public function checkParams($params)
    {


        if (!isset($params['langId']) || empty($params['langId'])) {
            throw new \Exception('В параметрах не найден langId');
        }


        $this->ownerItemDbData = array();
        $langId = $params['langId'];

        $langDbRes = \CLanguage::GetList($by = "lid", $order = "desc", array('LID' => $langId));
        if ($langDbRes === false || !$langDbRes->SelectedRowsCount()) {
            throw new \Exception('Не найден язык системы с id = ' . $langId);
        }

        $langData = $langDbRes->Fetch();

        $this->ownerItemDbData = $langData;


    }


}

?>

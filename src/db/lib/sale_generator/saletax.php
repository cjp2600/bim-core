<?php


/**
 * Class SaleTaxGen
 * класс для генерации кода изменений налога
 *
 * @package Bitrix\Adv_Preset\Saletax
 */
class SaleTaxGen extends CodeGenerator
{


    public function __construct()
    {
        \CModule::IncludeModule('sale');
    }

    /**
     * метод для генерации кода добавления налога
     * @param $params array
     * @return mixed
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Добавляем налог */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $taxData) {
            $addFields = $taxData;
            unset($addFields['ID']);

            $code = $code . $this->buildCode('SaleTaxIntegrate', 'Add', array($addFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода обновления налога
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Обновляем налог */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $taxData) {
            $updateFields = $taxData;


            $code = $code . $this->buildCode('SaleTaxIntegrate', 'Update', array($updateFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления  налога
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Удаляем  налог   */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $taxData) {
            $code = $code . $this->buildCode('SaleTaxIntegrate', 'Delete', array($taxData['CODE']));
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     * taxId => id налога
     * )
     * @return mixed
     */
    public function checkParams($params)
    {

        if (!isset($params['taxId']) || empty($params['taxId'])) {
            throw new \Exception('В параметрах не найден taxId');
        }

        foreach ($params['taxId'] as $taxId) {
            $taxData = \CSaleTax::GetByID($taxId);

            if (!strlen($taxData['CODE'])) {
                throw new \Exception('У налога с id = "' . $taxData['ID'] . '" не указан CODE');
            }
            unset($taxData['ID']);

            $this->ownerItemDbData[] = $taxData;
        }


    }


}

?>

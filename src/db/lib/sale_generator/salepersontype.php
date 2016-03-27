<?php


/**
 * Class SalePersonTypeGen
 * класс для генерации кода изменений типа плательщика
 *
 * @package Bitrix\Adv_Preset\SalePersonTypeGen
 */
class SalePersonTypeGen extends CodeGenerator
{


    public function __construct()
    {
        \CModule::IncludeModule('sale');
    }

    /**
     * метод для генерации кода добавления типа плательщика
     * @param $params array
     * @return mixed
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Добавляем типа плательщика */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $personData) {
            $addFields = $personData;

            $code = $code . $this->buildCode('SalePersonTypeIntegrate', 'Add', array($addFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода обновления типа плательщика
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Обновляем тип плательщика */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $personData) {
            $updateFields = $personData;
            $updateFields['OLD_NAME'] = $updateFields['NAME'];

            $code = $code . $this->buildCode('SalePersonTypeIntegrate', 'Update',
                    array($updateFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления типа плательщика
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Удаляем тип плательщика   */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $personData) {
            $code = $code . $this->buildCode('SalePersonTypeIntegrate', 'Delete',
                    array($personData['LID'], $personData['NAME']));
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     * personId => id типа плательщика
     * )
     * @return mixed
     */
    public function checkParams($params)
    {

        if (!isset($params['personId']) || empty($params['personId'])) {
            throw new \Exception('В параметрах не найден personId');
        }


        foreach ($params['personId'] as $personId) {

            $personData = \CSalePersonType::GetByID($personId);
            if (!$personData) {
                throw new \Exception('В системе не найдена платежная система с id = "' . $personId . '"');
            }
            unset($personData['ID']);


            $this->ownerItemDbData[] = $personData;
        }


    }


}

?>

<?php


/**
 * Class CurrencyRatesGen
 * класс для генерации кода изменений курсов валют
 *
 * @package Bitrix\Adv_Preset\CurrencyRatesGen
 */
class CurrencyRatesGen extends CodeGenerator
{


    public function __construct()
    {
        \CModule::IncludeModule('currency');
    }

    /**
     * метод для генерации кода добавления курсов валют
     * @param $params array
     * @return mixed
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Добавляем курсы валют */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $rateData) {
            $addFields = $rateData;

            $code = $code . $this->buildCode('CurrencyRatesIntegrate', 'Add', array($addFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода обновления валюты
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Обновляем курсы валют */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $rateData) {
            $updateFields = $rateData;

            $code = $code . $this->buildCode('CurrencyRatesIntegrate', 'Update',
                    array($updateFields['CURRENCY'], $updateFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода курсов валют
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Удаляем курсы валют  */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $rateData) {
            $code = $code . $this->buildCode('CurrencyRatesIntegrate', 'Delete',
                    array($rateData['CURRENCY'], $rateData['DATE_RATE']));
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     * rateId => курс валюты
     * )
     * @return mixed
     */
    public function checkParams($params)
    {

        if (!isset($params['rateId']) || empty($params['rateId'])) {
            throw new \Exception('В параметрах не найден rateId');
        }


        foreach ($params['rateId'] as $rateId) {

            $rateData = \CCurrencyRates::GetByID($rateId);
            unset($rateData['ID']);


            $this->ownerItemDbData[] = $rateData;
        }


    }


}

?>

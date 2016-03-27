<?php


/**
 * Class CurrencyGen
 * класс для генерации кода изменений валют
 *
 * @package Bitrix\Adv_Preset\CurrencyGen
 */
class CurrencyGen extends CodeGenerator
{


    public function __construct()
    {
        \CModule::IncludeModule('currency');
    }

    /**
     * метод для генерации кода добавления новой валюты
     * @param $params array
     * @return mixed
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Добавляем новую валюту */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $currencyData) {
            $addFields = $currencyData;

            $code = $code . $this->buildCode('CurrencyIntegrate', 'Add', array($addFields)) . PHP_EOL . PHP_EOL;
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

        $code = '<?php' . PHP_EOL . '/*  Обновляем валюту */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $currencyData) {
            $updateFields = $currencyData;

            $code = $code . $this->buildCode('CurrencyIntegrate', 'Update',
                    array($updateFields['CURRENCY'], $updateFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления валюты
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Удаляем валюту  */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $currencyData) {
            $code = $code . $this->buildCode('CurrencyIntegrate', 'Delete', array($currencyData['CURRENCY']));
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     * currencyId => код валюты
     * )
     * @return mixed
     */
    public function checkParams($params)
    {

        if (!isset($params['currencyId']) || empty($params['currencyId'])) {
            throw new \Exception('В параметрах не найден currencyId');
        }


        foreach ($params['currencyId'] as $currencyId) {

            $currencyData = \CCurrency::GetByID($currencyId);
            $dbLang = \CLangAdmin::GetList($by = "sort", $order = "asc");
            while ($arLang = $dbLang->Fetch()) {
                $currencyLang = \CCurrencyLang::GetByID($currencyId, $arLang['LID']);

                $currencyData['CURRENCY_LANG'][$arLang['LID']] = $currencyLang;

            }


            $this->ownerItemDbData[] = $currencyData;
        }


    }


}

?>

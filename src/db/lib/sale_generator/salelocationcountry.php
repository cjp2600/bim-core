<?php


/**
 * Class SaleLocationCountryGen
 * класс для генерации кода изменений страны местоположения
 *
 * @package Bitrix\Adv_Preset\SaleLocationCountryGen
 */
class SaleLocationCountryGen extends CodeGenerator
{


    public function __construct()
    {
        \CModule::IncludeModule('sale');
    }

    /**
     * метод для генерации кода добавления новой страны местоположения
     * @param $params array
     * @return mixed
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Добавляем новую страну местоположения */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $countryData) {
            $addFields = $countryData;
            unset($addFields['ID']);

            $code = $code . $this->buildCode('SaleLocationCountryIntegrate', 'Add',
                    array($addFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода обновления страны местоположения
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Обновляем страну местоположения */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $countryData) {
            $updateFields = $countryData;
            unset($updateFields['ID']);
            $updateFields['OLD_NAME'] = $countryData['NAME'];

            $code = $code . $this->buildCode('SaleLocationCountryIntegrate', 'Update',
                    array($updateFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления  страны местоположения
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Удаляем  страну местоположения   */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $countryData) {
            $code = $code . $this->buildCode('SaleLocationCountryIntegrate', 'Delete', array($countryData['NAME']));
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     * countryId => id городов
     * )
     * @return mixed
     */
    public function checkParams($params)
    {

        if (!isset($params['countryId']) || empty($params['countryId'])) {
            throw new \Exception('В параметрах не найден countryId');
        }


        foreach ($params['countryId'] as $countryId) {

            $countryData = \CSaleLocation::GetCountryById($countryId);
            $dbLang = \CLangAdmin::GetList($by = "sort", $order = "asc");
            while ($arLang = $dbLang->Fetch()) {
                $countryLang = \CSaleLocation::GetCountryLangByID($countryId, $arLang['LID']);
                unset($countryLang['ID']);
                unset($countryLang['COUNTRY_ID']);
                $countryData[$arLang['LID']] = $countryLang;

            }


            $this->ownerItemDbData[] = $countryData;
        }


    }


}

?>

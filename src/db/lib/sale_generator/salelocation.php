<?php


/**
 * Class SaleLocationGen
 * класс для генерации кода изменений местоположения
 *
 * @package Bitrix\Adv_Preset\SaleLocationlocationGen
 */
class SaleLocationGen extends CodeGenerator
{


    public function __construct()
    {
        \CModule::IncludeModule('sale');
    }

    /**
     * метод для генерации кода добавления нового  местоположения
     * @param $params array
     * @return mixed
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Добавляем новое местоположение */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $locationData) {
            $addFields = $locationData;

            $code = $code . $this->buildCode('SaleLocationIntegrate', 'Add', array($addFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода обновления  местоположения
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Обновляем  местоположение */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $locationData) {
            $updateFields = $locationData;

            $code = $code . $this->buildCode('SaleLocationIntegrate', 'Update',
                    array($updateFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления  местоположения
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Удаляем  местоположение   */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $locationData) {
            $code = $code . $this->buildCode('SaleLocationIntegrate', 'Delete',
                    array(LANGUAGE_ID, $locationData['COUNTRY_NAME'], $locationData['CITY_NAME']));
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     * locationId => id местоположения
     * )
     * @return mixed
     */
    public function checkParams($params)
    {

        if (!isset($params['locationId']) || empty($params['locationId'])) {
            throw new \Exception('В параметрах не найден locationId');
        }


        foreach ($params['locationId'] as $locationId) {

            $locationData = \CSaleLocation::GetById($locationId);

            if ((int)$locationData['COUNTRY_ID']) {
                $locationData['COUNTRY'] = \CSaleLocation::GetCountryById($locationData['COUNTRY_ID']);

            }

            if ((int)$locationData['REGION_ID']) {
                $locationData['REGION'] = \CSaleLocation::GetRegionByID($locationData['REGION_ID']);

            }

            if ((int)$locationData['CITY_ID']) {
                $locationData['CITY'] = \CSaleLocation::GetCityByID($locationData['CITY_ID']);

            }
            $dbLang = \CLangAdmin::GetList($by = "sort", $order = "asc");
            while ($arLang = $dbLang->Fetch()) {
                if ((int)$locationData['COUNTRY_ID']) {
                    $countryLang = \CSaleLocation::GetCountryLangByID($locationData['COUNTRY_ID'], $arLang['LID']);
                    unset($countryLang['ID']);
                    unset($countryLang['COUNTRY_ID']);
                    $locationData['COUNTRY'][$arLang['LID']] = $countryLang;
                }

                if ((int)$locationData['REGION_ID']) {
                    $regionLang = \CSaleLocation::GetRegionLangByID($locationData['REGION_ID'], $arLang['LID']);
                    unset($regionLang['ID']);
                    unset($regionLang['REGION_ID']);
                    $locationData['REGION'][$arLang['LID']] = $regionLang;
                }

                if ((int)$locationData['CITY_ID']) {
                    $cityLang = \CSaleLocation::GetCityLangByID($cityId, $arLang['LID']);
                    unset($cityLang['ID']);
                    unset($cityLang['CITY_ID']);
                    $locationData['CITY'][$arLang['LID']] = $cityLang;
                }

            }
            $locationData['COUNTRY_ID'] = 0;
            $locationData['REGION_ID'] = 0;
            $locationData['CITY_ID'] = 0;

            $this->ownerItemDbData[] = $locationData;
        }


    }


}

?>

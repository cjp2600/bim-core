<?php


/**
 * Class SaleLocationRegionGen
 * класс для генерации кода изменений региона местоположения
 *
 * @package Bitrix\Adv_Preset\SaleLocationRegionGen
 */
class SaleLocationRegionGen extends CodeGenerator
{


    public function __construct()
    {
        \CModule::IncludeModule('sale');
    }

    /**
     * метод для генерации кода добавления нового региона местоположения
     * @param $params array
     * @return mixed
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Добавляем новый регион местоположения */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $regionData) {
            $addFields = $regionData;
            unset($addFields['ID']);
            unset($addFields['REGION_ID']);

            $code = $code . $this->buildCode('SaleLocationRegionIntegrate', 'Add',
                    array($addFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода обновления региона местоположения
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Обновляем регион местоположения */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $regionData) {
            $updateFields = $regionData;
            unset($updateFields['ID']);
            unset($updateFields['REGION_ID']);
            $updateFields['OLD_NAME'] = $regionData['NAME'];

            $code = $code . $this->buildCode('SaleLocationRegionIntegrate', 'Update',
                    array($updateFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления  региона местоположения
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Удаляем  регион местоположения   */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $regionData) {
            $code = $code . $this->buildCode('SaleLocationRegionIntegrate', 'Delete', array($regionData['NAME']));
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     * regionId => id региона
     * )
     * @return mixed
     */
    public function checkParams($params)
    {

        if (!isset($params['regionId']) || empty($params['regionId'])) {
            throw new \Exception('В параметрах не найден regionId');
        }


        foreach ($params['regionId'] as $regionId) {

            $regionData = \CSaleLocation::GetRegionByID($regionId);
            $dbLang = \CLangAdmin::GetList($by = "sort", $order = "asc");
            while ($arLang = $dbLang->Fetch()) {
                $regionLang = \CSaleLocation::GetRegionLangByID($regionId, $arLang['LID']);
                unset($regionLang['ID']);
                unset($regionLang['REGION_ID']);
                $regionData[$arLang['LID']] = $regionLang;

            }


            $this->ownerItemDbData[] = $regionData;
        }


    }


}

?>

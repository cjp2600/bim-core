<?php

/**
 * Class CatalogGen
 * класс для генерации кода изменений в торговом каталоге
 *
 * @package Bitrix\Adv_Preset\Catalog_Generator
 */
class CatalogGen extends CodeGenerator
{


    public function __construct()
    {
        \CModule::IncludeModule('catalog');
        \CModule::IncludeModule('iblock');
    }

    /**
     * метод для генерации кода добавления инфоблока к торговому каталогу
     * @param $params array
     * @return mixed
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $addFields = $this->ownerItemDbData;

        $code = '<?php' . PHP_EOL . '/*  Добавляем новый инфоблок к торговому каталогу */' . PHP_EOL . PHP_EOL;

        $code = $code . $this->buildCode('CatalogIntegrate', 'Add', array($addFields));

        return $code;

    }

    /**
     * метод для генерации кода обновления инфоблока к торговому каталогу
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $updateFields = $this->ownerItemDbData;

        $code = '<?php' . PHP_EOL . '/*  Обновляем инфоблок из торгового каталога  */' . PHP_EOL . PHP_EOL;

        $code = $code . $this->buildCode('CatalogIntegrate', 'Update',
                array($updateFields['IBLOCK_CODE'], $updateFields));

        return $code;

    }

    /**
     * метод для генерации кода удаления  инфоблока из торгового каталога
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Удаляем инфоблок из торгового каталога  */' . PHP_EOL . PHP_EOL;

        $code = $code . $this->buildCode('CatalogIntegrate', 'Delete', array($this->ownerItemDbData['IBLOCK_CODE']));

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     * iblockId => id инфоблока
     * )
     * @return mixed
     */
    public function checkParams($params)
    {

        if (!isset($params['iblockId']) || !strlen($params['iblockId'])) {
            throw new \Exception('В параметрах не найден iblockTypeId');
        }


        $iblockDbRes = \CIBlock::GetByID($params['iblockId']);
        if ($iblockDbRes === false || !$iblockDbRes->SelectedRowsCount()) {
            throw new \Exception('В системе не найден  инфоблок с id = ' . $params['iblockId']);
        }

        $iblockData = $iblockDbRes->Fetch();
        if (!strlen($iblockData['CODE'])) {
            throw new \Exception('У инфоблока с id = ' . $params['iblockId'] . ' не найден символьный код');
        }

        $this->ownerItemDbData = array(
            'YANDEX_EXPORT' => 'N',
            'SUBSCRIPTION' => 'N',
            'IBLOCK_CODE' => $iblockData['CODE']
        );


    }


}

?>

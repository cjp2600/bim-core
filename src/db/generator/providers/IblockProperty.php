<?php

namespace Bim\Db\Generator\Providers;
use Bim\Db\Generator\Code;
use Bim\Exception\BimException;

/**
 * Class IblockPropertyGen
 * @package Bim\Db\Generator\Providers
 */
class IblockProperty extends Code
{

    /**
     * IblockPropertyGen constructor.
     */
    public function __construct()
    {
        # Требует обязательного подключения модуля
        # Iblock

        \CModule::IncludeModule('iblock');
    }

    /**
     * generateAddCode
     * @param array $params
     * @return bool|string
     */
    public function generateAddCode($params)
    {
        $iBlock = new \CIBlock();
        $IblockCode = $params['iblockCode'];
        $PropertyCode = $params['propertyCode'];
        $IblockProperty = new \CIBlockProperty();
        $dbIblockProperty = $IblockProperty->GetList(array(),
            array('IBLOCK_CODE' => $IblockCode, 'CODE' => $PropertyCode));
        if ($arIblockProperty = $dbIblockProperty->Fetch()) {
            if ($arIblockProperty['PROPERTY_TYPE'] == 'L') {
                $arIblockProperty['VALUES'] = $this->getEnumItemList($arIblockProperty['IBLOCK_ID'],
                    $arIblockProperty['ID']);
            }
            if (isset($arIblockProperty['LINK_IBLOCK_ID'])) {
                $res = $iBlock->GetByID($arIblockProperty['LINK_IBLOCK_ID']);
                if ($ar_res = $res->GetNext()) {
                    unset($arIblockProperty['LINK_IBLOCK_ID']);
                    $arIblockProperty['LINK_IBLOCK_CODE'] = $ar_res['CODE'];
                }
            }
            unset($arIblockProperty['ID']);
            unset($arIblockProperty['IBLOCK_ID']);
            $arIblockProperty['IBLOCK_CODE'] = $IblockCode;

            return $this->getMethodContent('Bim\Db\Iblock\IblockPropertyIntegrate', 'Add', array($arIblockProperty));
        } else {
            return false;
        }
    }


    public function generateUpdateCode($params)
    {
        //UPDATE
    }

    /**
     * метод для генерации кода удаления  свойства инфоблока
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        return $this->getMethodContent('Bim\Db\Iblock\IblockPropertyIntegrate', 'Delete',
            array($params['iblockCode'], $params['propertyCode']));
    }

    /**
     * getEnumItemList
     * @param $iblockId
     * @param $iblockPropId
     * @return array
     */
    private function getEnumItemList($iblockId, $iblockPropId)
    {
        $result = array();
        $propEnumDbRes = \CIBlockPropertyEnum::GetList(array('SORT' => 'ASC'),
            array('IBLOCK_ID' => $iblockId, 'PROPERTY_ID' => $iblockPropId));
        if ($propEnumDbRes !== false && $propEnumDbRes->SelectedRowsCount()) {
            $index = 0;
            while ($propEnum = $propEnumDbRes->Fetch()) {
                $result[$index] = array(
                    'ID' => $index,
                    'VALUE' => $propEnum['VALUE'],
                    'XML_ID' => $propEnum['XML_ID'],
                    'SORT' => $propEnum['SORT'],
                    'DEF' => $propEnum['DEF']
                );
                $index++;
            }
        }
        return $result;
    }

    /**
     * getIblockCode
     * @param $iblockId
     * @return bool
     */
    private function getIblockCode($iblockId)
    {
        $iBlock = new \CIBlock();
        $iblockDbRes = $iBlock->GetByID($iblockId);

        if ($iblockDbRes !== false && $iblockDbRes->SelectedRowsCount()) {
            $iblockData = $iblockDbRes->Fetch();
            if (strlen($iblockData['CODE'])) {
                return $iblockData['CODE'];
            }
        }
        return false;
    }

    /**
     * checkParams
     * @param array $params
     * @return mixed|void
     * @throws \Exception
     */
    public function checkParams($params)
    {
        $iBlock = new \CIBlock();

        if (!isset($params['iblockId']) || !strlen($params['iblockId'])) {
            throw new BimException('В параметрах не найден iblockId');
        }

        if (!isset($params['propertyId']) || empty($params['propertyId'])) {
            throw new BimException('В параметрах не найден propertyId');
        }

        $iblockDbRes = $iBlock->GetByID($params['iblockId']);
        if ($iblockDbRes === false || !$iblockDbRes->SelectedRowsCount()) {
            throw new BimException('Не найден инфоблок с iblockId = ' . $params['iblockId']);
        }
        $iblockData = $iblockDbRes->Fetch();
        if (!strlen($iblockData['CODE'])) {
            throw new BimException('В инфоблоке не указан символьный код');
        }
        $this->ownerItemDbData['iblockData'] = $iblockData;
        $this->ownerItemDbData['propertyData'] = array();
        foreach ($params['propertyId'] as $propertyId) {
            $propertyDbRes = \CIBlockProperty::GetByID($propertyId);
            if ($propertyDbRes === false || !$propertyDbRes->SelectedRowsCount()) {
                throw new BimException('Не найдено св-во с id = ' . $propertyId);
            }
            $propertyData = $propertyDbRes->Fetch();
            if (!strlen($propertyData['CODE'])) {
                throw new BimException('В свойстве c id =' . $propertyData['ID'] . ' не указан символьный код.');
            }
            $this->ownerItemDbData['propertyData'][] = $propertyData;
        }
    }

}

?>

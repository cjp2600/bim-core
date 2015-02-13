<?php

namespace Bim\Db\Lib;

/**
 * Class IblockPropertyGen
 * @package Bim\Db\Lib
 */
class IblockPropertyGen extends \Bim\Db\Lib\CodeGenerator
{

    public function __construct(){
        \CModule::IncludeModule('iblock');
    }

    /**
     * метод для генерации кода добавления нового свойства инфоблока
     * @param array $params
     * @return mixed
     * @internal param array $ch
     * @internal param array $params
     */
    public function generateAddCode( $params )
    {
        $IblockCode = $params['iblockCode'];
        $PropertyCode = $params['propertyCode'];
        $IblockProperty = new \CIBlockProperty();
        $dbIblockProperty = $IblockProperty->GetList(array(), array('IBLOCK_CODE' => $IblockCode, 'CODE' => $PropertyCode));
        if ($arIblockProperty = $dbIblockProperty->Fetch()) {
            if ($arIblockProperty['PROPERTY_TYPE'] == 'L') {
                $arIblockProperty['VALUES'] = $this->getEnumItemList($arIblockProperty['IBLOCK_ID'], $arIblockProperty['ID']);
            }
            if (isset($arIblockProperty['LINK_IBLOCK_ID'])) {
                $res = \CIBlock::GetByID($arIblockProperty['LINK_IBLOCK_ID']);
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


    public function generateUpdateCode( $params )
    {
        //UPDATE
    }

    /**
     * метод для генерации кода удаления  свойства инфоблока
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params )
    {
        return $this->getMethodContent('Bim\Db\Iblock\IblockPropertyIntegrate', 'Delete', array( $params['iblockCode'], $params['propertyCode'] ));
    }

    /**
     * getEnumItemList
     * @param $iblockId
     * @param $iblockPropId
     * @return array
     */
    private function getEnumItemList( $iblockId, $iblockPropId )
    {
        $result = array();
        $propEnumDbRes = \CIBlockPropertyEnum::GetList(array('SORT' => 'ASC'), array('IBLOCK_ID' => $iblockId, 'PROPERTY_ID' => $iblockPropId ));
        if ( $propEnumDbRes !== false && $propEnumDbRes->SelectedRowsCount() ) {
            $index = 0;
            while( $propEnum =  $propEnumDbRes->Fetch() ) {
                $result[ $index ] = array(
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
    private function getIblockCode( $iblockId ) {
        $iblockDbRes = \CIBlock::GetByID( $iblockId );

        if ( $iblockDbRes !== false && $iblockDbRes->SelectedRowsCount() ) {
            $iblockData = $iblockDbRes->Fetch();
            if ( strlen( $iblockData['CODE'] ) ) {
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
    public function checkParams( $params  )
    {
        if (!isset($params['iblockId']) || !strlen($params['iblockId'])) {
            throw new \Exception('В параметрах не найден iblockId');
        }

        if (!isset($params['propertyId']) || empty($params['propertyId'])) {
            throw new \Exception('В параметрах не найден propertyId');
        }

        $iblockDbRes = \CIBlock::GetByID($params['iblockId']);
        if ($iblockDbRes === false || !$iblockDbRes->SelectedRowsCount()) {
            throw new \Exception('Не найден инфоблок с iblockId = ' . $params['iblockId']);
        }
        $iblockData = $iblockDbRes->Fetch();
        if (!strlen($iblockData['CODE'])) {
            throw new \Exception('В инфоблоке не указан символьный код');
        }
        $this->ownerItemDbData['iblockData'] = $iblockData;
        $this->ownerItemDbData['propertyData'] = array();
        foreach ($params['propertyId'] as $propertyId) {
            $propertyDbRes = \CIBlockProperty::GetByID($propertyId);
            if ($propertyDbRes === false || !$propertyDbRes->SelectedRowsCount()) {
                throw new \Exception('Не найдено св-во с id = ' . $propertyId);
            }
            $propertyData = $propertyDbRes->Fetch();
            if (!strlen($propertyData['CODE'])) {
                throw new \Exception('В свойстве c id =' . $propertyData['ID'] . ' не указан символьный код.');
            }
            $this->ownerItemDbData['propertyData'][] = $propertyData;
        }
    }

}

?>

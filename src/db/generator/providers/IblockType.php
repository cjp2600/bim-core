<?php

namespace Bim\Db\Generator\Providers;

use Bim\Db\Generator\Code;
use Bim\Exception\BimException;
use Bim\Util\Helper;


/**
 * Class IblockTypeGen
 * @package Bim\Db\Generator\Providers
 */
class IblockType extends Code
{
    /**
     * IblockType constructor.
     */
    public function __construct()
    {
        # Требует обязательного подключения модуля
        # Iblock

        \CModule::IncludeModule('iblock');
    }


    /**
     * Генерация создания
     *
     * generateAddCode
     * @param array $IblockTypeId
     * @return bool|string
     */
    public function generateAddCode($IblockTypeId)
    {
        $iBlock = new \CIBlock();
        $CIblockType = new \CIBlockType();
        $lang = new \CLanguage();

        $return = array();
        $dbIblockType = $CIblockType->GetByID($IblockTypeId);
        if ($arIblockType = $dbIblockType->GetNext()) {
            $Iblock = new \CIBlock();
            $dbIblock = $Iblock->GetList(array(), array('TYPE' => $IblockTypeId, 'CHECK_PERMISSIONS' => 'N'));
            while ($arIblock = $dbIblock->GetNext()) {
                $IblockProperty = new \CIBlockProperty();
                $dbIblockProperty = $IblockProperty->GetList(array(),
                    array('IBLOCK_CODE' => $arIblock['CODE'], 'CHECK_PERMISSIONS' => 'N'));
                while ($arIblockProperty = $dbIblockProperty->GetNext()) {
                    $dbPropertyValues = \CIBlockPropertyEnum::GetList(array(),
                        array("IBLOCK_ID" => $arIblockProperty['IBLOCK_ID'], "CODE" => $arIblockProperty['CODE']));
                    while ($arPropertyValues = $dbPropertyValues->Fetch()) {
                        $arIblockProperty['VALUES'][$arPropertyValues['ID']] = $arPropertyValues;
                    }

                    Helper::unsetFields(array(
                        'ID',
                        '~ID',
                        'IBLOCK_ID',
                        '~IBLOCK_ID'
                    ), $arIblockProperty);

                    $arIblockProperty['IBLOCK_CODE'] = $arIblock['CODE'];

                    foreach ($arIblockProperty as $k => $v) {
                        if (strstr($k, "~") || is_null($v)) {
                            unset($arIblockProperty[$k]);
                        }
                    }
                    if (isset($arIblockProperty['LINK_IBLOCK_ID'])) {
                        $res = $iBlock->GetList(array(),
                            array("ID" => $arIblockProperty['LINK_IBLOCK_ID'], 'CHECK_PERMISSIONS' => 'N'));
                        if ($ar_res = $res->GetNext()) {
                            unset($arIblockProperty['LINK_IBLOCK_ID']);
                            $arIblockProperty['LINK_IBLOCK_CODE'] = $ar_res['CODE'];
                        }
                    }
                    $return[] = $this->getMethodContent('Bim\Db\Iblock\IblockPropertyIntegrate', 'Add',
                        array($arIblockProperty));
                }
                foreach ($arIblock as $k => $v) {
                    if ((strstr($k, "~")) || ($k == 'ID')) {
                        unset($arIblock[$k]);
                    }
                }
                $return[] = $this->getMethodContent('Bim\Db\Iblock\IblockIntegrate', 'Add', array($arIblock));
            }
            foreach ($arIblockType as $k => $v) {
                if (strstr($k, "~") || is_null($v)) {
                    unset($arIblockType[$k]);
                }
            }
            $rsLang = $lang->GetList($by = "lid", $order = "desc");
            while ($arLang = $rsLang->Fetch()) {
                $arTypeLang = $CIblockType->GetByIDLang($IblockTypeId, $arLang['LID']);
                $arIblockType["LANG"][$arLang['LID']] = array(
                    'NAME' => $arTypeLang['NAME'],
                    'SECTION_NAME' => $arTypeLang['SECTION_NAME'],
                    'ELEMENT_NAME' => $arTypeLang['ELEMENT_NAME'],
                );
            }
            $return[] = $this->getMethodContent('Bim\Db\Iblock\IblockTypeIntegrate', 'Add', array($arIblockType));
            $return = array_reverse($return);

            return implode(PHP_EOL, $return);
        } else {
            return false;
        }
    }

    /**
     *  Генерация кода обновления
     *
     * generateUpdateCode
     * @param array $params
     * @return mixed|void
     */
    public function generateUpdateCode($params)
    {
        // UPDATE ..
    }

    /**
     * метод для генерации кода удаления
     *
     * generateDeleteCode
     * @param array $IblockTypeId
     * @return string
     */
    public function generateDeleteCode($IblockTypeId)
    {
        return $this->getMethodContent('Bim\Db\Iblock\IblockTypeIntegrate', 'Delete', array($IblockTypeId));
    }

    /**
     * getLangData
     * @param $iblockTypeId
     * @return array
     */
    private function getLangData($iblockTypeId)
    {
        $CIblockType = new \CIBlockType();
        $lang = new \CLanguage();

        $result = array();
        $langDbRes = $lang->GetList($by = "lid", $order = "desc", Array());
        while ($langData = $langDbRes->Fetch()) {
            $typeLangItemTmp = $CIblockType->GetByIDLang($iblockTypeId, $langData['LID']);
            $typeLangItem = array();
            foreach ($typeLangItemTmp as $key => $value) {
                if (strstr($key, '~')) {
                    continue;
                }
                $typeLangItem[$key] = $value;
            }

            $result[$langData['LID']] = $typeLangItem;
        }
        return $result;
    }

    /**
     * Абстрактный метод проверки передаваемых параметров
     *
     * checkParams
     * @param array $params
     * @return mixed|void
     * @throws \Exception
     */
    public function checkParams($params)
    {
        $CIblockType = new \CIBlockType();
        if (!isset($params['iblockTypeId']) || !strlen($params['iblockTypeId'])) {
            throw new BimException('В параметрах не найден iblockTypeId');
        }
        $iblockTypeDbRes = $CIblockType->GetByID($params['iblockTypeId']);
        if ($iblockTypeDbRes === false || !$iblockTypeDbRes->SelectedRowsCount()) {
            throw new BimException('В системе не найден тип инфоблока с id = ' . $params['iblockTypeId']);
        }
        $this->ownerItemDbData = $iblockTypeDbRes->Fetch();
    }
    
}
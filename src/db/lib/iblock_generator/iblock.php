<?php

namespace Bim\Db\Lib;

/**
 * Class IblockGen
 * класс для генерации кода изменений в инфоблоке:
 */
class IblockGen extends \Bim\Db\Lib\CodeGenerator
{

    public function __construct(){
        \CModule::IncludeModule('iblock');
    }

    /**
     * метод для генерации кода добавления нового  инфоблока
     * @param array $IblockCode
     * @return mixed
     * @internal param array $params
     */
    public function generateAddCode($IblockCode)
    {
        $iblock = new \CIBlock();
        $return = array();
        $iblock_object = $iblock->GetList(array(), array('CODE' => $IblockCode));
        if ($item = $iblock_object->Fetch()) {
            $item['GROUP_ID'] = \CIBlock::GetGroupPermissions($item['ID']);
            $item['FIELDS'] = \CIBlock::GetFields($item['ID']);
            unset($item['ID']);
            if ($return[] = $this->getMethodContent('Bim\Db\Iblock\IblockIntegrate', 'Add', array($item))) {
                $IblockProperty = new \CIBlockProperty();
                $dbIblockProperty = $IblockProperty->GetList(array(), array('IBLOCK_CODE' => $item['CODE']));
                while ($arIblockProperty = $dbIblockProperty->Fetch()) {
                    unset($arIblockProperty['ID']);
                    $dbPropertyValues = \CIBlockPropertyEnum::GetList(array(), array("IBLOCK_ID" => $arIblockProperty['IBLOCK_ID'], "CODE" => $arIblockProperty['CODE']));
                    while ($arPropertyValues = $dbPropertyValues->Fetch())
                        unset($arPropertyValues['PROPERTY_ID']);
                    $arIblockProperty['VALUES'][$arPropertyValues['ID']] = $arPropertyValues;
                    $return[] = $this->getMethodContent('Bim\Db\Iblock\IblockPropertyIntegrate', 'Add', array($arIblockProperty));
                }
                return implode(PHP_EOL, $return);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    /**
     * метод для генерации кода обновления инфоблока
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode( $params )
    {
        $this->checkParams( $params );
        $code = false;
        foreach( $this->ownerItemDbData as $iblockData  ){
            $updateFields = $iblockData;
            unset( $updateFields['ID'] );
            $updateFields['FIELDS'] = \CIBlock::GetFields( $iblockData['ID'] );
            $updateFields['GROUP_ID'] = \CIBlock::GetGroupPermissions( $iblockData['ID'] );
            $code = $code . $this->buildCode('Bim\Db\Iblock\IblockIntegrate', 'Update', array( $updateFields['CODE'], $updateFields ) ) .PHP_EOL.PHP_EOL;
        }
        return $code;
    }

    /**
     * метод для генерации кода удаления  инфоблока
     * @param array $iblockCode
     * @return mixed
     * @internal param array $params
     */
    public function generateDeleteCode( $iblockCode )
    {
        return $this->getMethodContent('Bim\Db\Iblock\IblockIntegrate', 'Delete', array( $iblockCode ) );
    }


    /**
     * абстрактный метод проверки передаваемых параметров
     * @param $params array
     * @return mixed
     */
    public function checkParams($params)
    {
        // TODO: Implement checkParams() method.
    }
}
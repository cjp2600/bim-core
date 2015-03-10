<?php

namespace Bim\Db\Lib;

use Bim\Db\Lib\CodeGenerator;
use \Bitrix\Highloadblock as HL;

/**
 * Class HighloadblockGen
 * @package Bim\Db\Lib
 */
class HighloadblockGen extends CodeGenerator
{

    public function __construct(){
        \CModule::IncludeModule("highloadblock");
    }


    /**
     * generateAddCode
     * @param array $hlblockId
     * @return string
     * @throws \Exception
     */
    public function generateAddCode($hlblockId)
    {
        $return = array();
        $hlblock = HL\HighloadBlockTable::getById($hlblockId)->fetch();
        if (!$hlblock) {
            throw new \Exception('Not found highload block with id = ' . $hlblockId);
        }
        $return[] = $this->getMethodContent('Bim\Db\Iblock\HighloadblockIntegrate', 'Add', array($hlblock['NAME'], $hlblock['TABLE_NAME']));
        $obHl = \CUserTypeEntity::GetList(array(), array("ENTITY_ID" => "HLBLOCK_" . $hlblockId));
        while ($arHl = $obHl->Fetch()) {
            $arFullData = \CUserTypeEntity::GetByID($arHl['ID']);
            unset($arFullData['ID']);
            unset($arFullData['ENTITY_ID']);

            if ( ($arFullData['USER_TYPE_ID'] == "iblock_element" || $arFullData['USER_TYPE_ID'] == "iblock_section" ) && (isset($arFullData['SETTINGS']['IBLOCK_ID']))) {
                if (!empty($arFullData['SETTINGS']['IBLOCK_ID'])) {
                    $iblockId = $arFullData['SETTINGS']['IBLOCK_ID'];
                    unset($arFullData['SETTINGS']['IBLOCK_ID']);
                    $rsIBlock = \CIBlock::GetList(array(), array('ID' => $iblockId,'CHECK_PERMISSIONS'=>'N'));
                    if ($arIBlock = $rsIBlock->Fetch()) {
                        $arFullData['SETTINGS']['IBLOCK_CODE'] = $arIBlock['CODE'];
                    } else {
                        throw new \Exception(' Not found iblock with id ' . $iblockId);
                    }
                }
            }

            $return[] = $this->getMethodContent('Bim\Db\Iblock\HighloadblockFieldIntegrate', 'Add', array($hlblock['NAME'], $arFullData));
        }
        return implode(PHP_EOL, $return);
    }


    /**
     * generateUpdateCode
     * @param array $params
     * @return mixed|void
     */
    public function generateUpdateCode($params)
    {
        // update
    }


    /**
     * generateDeleteCode
     * @param array $hlblockId
     * @return string
     * @throws \Exception
     */
    public function generateDeleteCode($hlblockId)
    {
        $hlblock = HL\HighloadBlockTable::getById($hlblockId)->fetch();
        if (!$hlblock) {
            throw new \Exception('В системе не найден highload инфоблок с id = ' . $hlblockId);
        }

        return  $this->getMethodContent('Bim\Db\Iblock\HighloadblockIntegrate', 'Delete', array($hlblock['NAME']));
    }


    /**
     * checkParams
     * @param array $params
     * @return mixed|void
     * @throws \Exception
     */
    public function checkParams($params)
    {
        if (!isset($params['hlblockId']) || empty($params['hlblockId'])) {
            throw new \Exception('В параметрах не найден hlblockId');
        }
        foreach ($params['hlblockId'] as $hlblockId) {
            $hlblock = HL\HighloadBlockTable::getById($hlblockId)->fetch();
            if (!$hlblock) {
                throw new \Exception('В системе не найден highload инфоблок с id = ' . $hlblockId);
            }
            $this->ownerItemDbData[] = $hlblock;
        }
    }


}

?>

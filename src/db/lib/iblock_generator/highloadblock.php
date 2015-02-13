<?php

namespace Bim\Db\Lib;

use \Bitrix\Highloadblock as HL;

/**
 * Class HighloadblockGen
 * @package Bim\Db\Lib
 */
class HighloadblockGen extends \Bim\Db\Lib\CodeGenerator
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
    public function generateDeleteCode( $hlblockId )
    {
        $hlblock = HL\HighloadBlockTable::getById($hlblockId)->fetch();
        if (!$hlblock) {
            throw new \Exception('В системе не найден highload инфоблок с id = ' . $hlblockId);
        }

        $obHl = \CUserTypeEntity::GetList(array(), array("ENTITY_ID" => "HLBLOCK_" . $hlblockId));
        while ($arHl = $obHl->Fetch()) {
            $arFullData = \CUserTypeEntity::GetByID($arHl['ID']);
            unset($arFullData['ID']);
            unset($arFullData['ENTITY_ID']);
            $return[] = $this->getMethodContent('Bim\Db\Iblock\HighloadblockFieldIntegrate', 'Delete', array($hlblock['NAME'], $arFullData['FIELD_NAME']));
        }
        $return[] = $this->getMethodContent('Bim\Db\Iblock\HighloadblockIntegrate', 'Delete', array($hlblock['NAME']));

        return implode(PHP_EOL, $return);
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

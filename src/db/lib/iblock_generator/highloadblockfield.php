<?php

namespace Bim\Db\Lib;

use \Bitrix\Highloadblock as HL;

/**
 * Class HighloadblockFieldGen
 * @package Bim\Db\Lib
 */
class HighloadblockFieldGen extends \Bim\Db\Lib\CodeGenerator
{

    public function __construct()
    {
        \CModule::IncludeModule("highloadblock");
    }


    /**
     * generateAddCode
     * @param array $params
     * @return string
     * @throws \Exception
     */
    public function generateAddCode($params)
    {
        $hlblockId = $params['hlblockId'];
        $hlFieldId = $params['hlFieldId'];

        $this->checkParams($params);
        $return = "";
        $hlblockData = $this->ownerItemDbData['hlblockData'];
        if ($hlFieldData = $this->ownerItemDbData['hlFieldData']) {
            unset($hlFieldData['ID']);
            unset($hlFieldData['ENTITY_ID']);

            # add iblock code to
            if ($hlFieldData['USER_TYPE_ID'] == "iblock_element" && (isset($hlFieldData['SETTINGS']['IBLOCK_ID']))) {
                $iblockId = $hlFieldData['SETTINGS']['IBLOCK_ID'];
                unset($hlFieldData['SETTINGS']['IBLOCK_ID']);
                $rsIBlock = \CIBlock::GetList(array(), array('ID' => $iblockId));
                if ($arIBlock = $rsIBlock->Fetch()) {
                    $hlFieldData['SETTINGS']['IBLOCK_CODE'] = $arIBlock['CODE'];
                } else {
                    throw new \Exception(' Not found iblock with id ' . $iblockId);
                }
            }

            $return = $this->getMethodContent('Bim\Db\Iblock\HighloadblockFieldIntegrate', 'Add', array($hlblockData['NAME'], $hlFieldData));
        }
        return $return;
    }


    /**
     * generateUpdateCode
     * @param array $params
     * @return string
     * @throws \Exception
     */
    public function generateUpdateCode( $params )
    {
        // UPDATE
    }


    /**
     * generateDeleteCode
     * @param array $params
     * @return string
     * @throws \Exception
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);
        $return = "";
        $hlblockData = $this->ownerItemDbData['hlblockData'];
        if ($hlFieldData = $this->ownerItemDbData['hlFieldData']) {
            $return = $this->getMethodContent('Bim\Db\Iblock\HighloadblockFieldIntegrate', 'Delete', array($hlblockData['NAME'], $hlFieldData['FIELD_NAME']));
        }
        return $return;
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
        if (!isset($params['hlFieldId']) || empty($params['hlFieldId'])) {
            throw new \Exception('В параметрах не найден hlFieldId');
        }
        $hlblock = HL\HighloadBlockTable::getById($params['hlblockId'])->fetch();
        if (!$hlblock) {
            throw new \Exception('В системе не найден highload инфоблок с id = ' . $params['hlblockId']);
        }
        $this->ownerItemDbData['hlblockData'] = $hlblock;
        if ($params['hlFieldId']) {
            $userFieldData = \CUserTypeEntity::GetByID($params['hlFieldId']);
            if ($userFieldData === false || empty($userFieldData)) {
                throw new \Exception('Не найдено пользовательское поле с id = ' . $params['hlFieldId']);
            }
            $this->ownerItemDbData['hlFieldData'] = $userFieldData;
        }
    }


}
?>
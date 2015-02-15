<?php

namespace Bim\Db\Iblock;

\CModule::IncludeModule("highloadblock");
use Bitrix\Highloadblock as HL;

/**
 * Class HighloadblockFieldIntegrate
 *
 * Documentation: http://cjp2600.github.io/bim-core/
 * @package Bim\Db\Iblock
 */
class HighloadblockFieldIntegrate {

    /**
     * Add
     * @param $entityName
     * @param $fields
     * @return mixed
     * @throws \Exception
     */
    function Add($entityName, $fields)
    {
        if (empty($entityName) || empty($fields)) {
            throw new \Exception('entityName or fields is empty');
        }
        if (empty($fields['FIELD_NAME'])) {
            throw new \Exception('Field FIELD_NAME is required.');
        }
        if (empty($fields['USER_TYPE_ID'])) {
            throw new \Exception('Field USER_TYPE_ID is required.');
        }
        if (isset($fields['ID'])) {
            unset($fields['ID']);
        }
        $userFieldEntity = self::_getEntityId($entityName);
        $fields['ENTITY_ID'] = $userFieldEntity;

        $typeEntityDbRes = \CUserTypeEntity::GetList(array(), array(
            "ENTITY_ID" => $fields["ENTITY_ID"],
            "FIELD_NAME" => $fields["FIELD_NAME"],
        ));
        if ($typeEntityDbRes !== false && $typeEntityDbRes->SelectedRowsCount()) {
            throw new \Exception('Hlblock field with name = "' . $fields["FIELD_NAME"] . '" already exist.');
        }

        #if
        if ( ($fields['USER_TYPE_ID'] == "iblock_element" || $fields['USER_TYPE_ID'] == "iblock_section") && (isset($fields['SETTINGS']['IBLOCK_CODE']))) {
            unset($fields['SETTINGS']['IBLOCK_CODE']);
            $rsIBlock = \CIBlock::GetList(array(), array('CODE' => $fields['SETTINGS']['IBLOCK_CODE']));
            if ($arIBlock = $rsIBlock->Fetch()) {
                $fields['SETTINGS']['IBLOCK_ID'] = $arIBlock['ID'];
            } else {
                throw new \Exception(__METHOD__ . ' Not found iblock with code ' . $fields['SETTINGS']['IBLOCK_CODE']);
            }
        }

        $UserType = new \CUserTypeEntity;
        $ID = $UserType->Add($fields);
        if (!(int)$ID) {
            throw new \Exception('Not added Hlblock field');
        }
        return $ID;
    }

    /**
     * Delete
     * @param $entityName
     * @param $fieldName
     * @return mixed
     * @throws \Exception
     */
    function Delete($entityName, $fieldName)
    {
        if (empty($entityName)) {
            throw new \Exception('entityName is required');
        }

        if (empty($fieldName)) {
            throw new \Exception('fieldName is required.');
        }

        $filter = array('NAME' => $entityName);
        $hlBlockDbRes = HL\HighloadBlockTable::getList(array(
            "filter" => $filter
        ));
        if (!$hlBlockDbRes->getSelectedRowsCount()) {
            throw new \Exception('Not found highloadBlock with entityName = ' . $entityName);
        }

        $hlBlockRow = $hlBlockDbRes->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlBlockRow);
        $entityDataClass = $entity->getDataClass();

        $obList = $entityDataClass::getList(array("select"=>array("ID")));
        if ($obList->getSelectedRowsCount() > 0) {
            throw new \Exception('Unable to remove a highloadBlock[ ' . $entityName . ' ], because it has elements');
        }

        $userFieldEntity = self::_getEntityId($entityName);
        $typeEntityDbRes = \CUserTypeEntity::GetList(array(), array(
            "ENTITY_ID" => $userFieldEntity,
            "FIELD_NAME" => $fieldName,
        ));
        if ($typeEntityDbRes->SelectedRowsCount() > 0) {
            $hlBlockFieldData = $typeEntityDbRes->Fetch();
            $userType = new \CUserTypeEntity;
            if (!$userType->Delete($hlBlockFieldData['ID'])) {
                throw new \Exception('Not delete Hlblock field');
            }
            return $hlBlockFieldData['ID'];
        }
    }

    /**
     * _getEntityId
     * @param $entityName
     * @return bool|string
     * @throws \Exception
     */
    static public function _getEntityId($entityName)
    {
        if (!strlen($entityName)) {
            return false;
        }
        $filter = array('NAME' => $entityName);
        $hlBlockDbRes = HL\HighloadBlockTable::getList(array(
            "filter" => $filter
        ));
        if (!$hlBlockDbRes->getSelectedRowsCount()) {
            throw new \Exception('Not found highloadBlock with entityName = " ' . $entityName . ' "');
        }
        $hlBlockRow = $hlBlockDbRes->fetch();
        $userFieldEntity = sprintf('HLBLOCK_%s', $hlBlockRow['ID']);
        return $userFieldEntity;
    }

}
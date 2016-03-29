<?php
namespace Bim\Db\Iblock;

\CModule::IncludeModule("highloadblock");
\CModule::IncludeModule("iblock");
use Bim\Exception\BimException;
use Bitrix\Highloadblock as HL;

/**
 * Class HighloadblockIntegrate
 *
 * Documentation: http://cjp2600.github.io/bim-core/
 * @package Bim\Db\Iblock
 */
class HighloadblockIntegrate
{
    /**
     * Add
     * @param $entityName
     * @param $tableName
     * @return bool
     * @throws \Exception
     */
    public static function Add($entityName, $tableName)
    {
        if (empty($entityName)) {
            throw new BimException('entityName is empty');
        }
        if (empty($tableName)) {
            throw new BimException('tableName is empty');
        }
        $addFields = array(
            'NAME' => trim($entityName),
            'TABLE_NAME' => trim($tableName)
        );
        $addResult = HL\HighloadBlockTable::add($addFields);
        if (!$addResult->isSuccess()) {
            throw new \Exception(implode(", ", $addResult->getErrorMessages()));
        }
        return true;
    }


    /**
     * Delete
     * @param $entityName
     * @return bool
     * @throws \Exception
     */
    public static function Delete($entityName)
    {
        $userType = new \CUserTypeEntity();
        if (!strlen($entityName)) {
            throw new BimException('Incorrect entityName param value');
        }
        $filter = array('NAME' => $entityName);
        $hlBlockDbRes = HL\HighloadBlockTable::getList(array(
            "filter" => $filter
        ));
        if (!$hlBlockDbRes->getSelectedRowsCount()) {
            throw new BimException('Not found highloadBlock with entityName = ' . $entityName);
        }
        $hlBlockRow = $hlBlockDbRes->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlBlockRow);
        $entityDataClass = $entity->getDataClass();

        $obList = $entityDataClass::getList();
        if ($obList->getSelectedRowsCount() > 0) {
            throw new BimException('Unable to remove a highloadBlock[' . $entityName . '], because it has elements');
        }

        # delete all Fields
        $obHl = $userType->GetList(array(), array("ENTITY_ID" => "HLBLOCK_" . $hlBlockRow['ID']));
        while ($arHl = $obHl->Fetch()) {

            $obUF = new \CUserTypeEntity();
            $obUF->Delete($arHl['ID']);

        }

        $delResult = HL\HighloadBlockTable::delete($hlBlockRow['ID']);
        if (!$delResult->isSuccess()) {
            throw new BimException(implode(", ", $delResult->getErrorMessages()));
        }
        return true;
    }

}
<?php
namespace Bim\Db\Entity;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

/**
 * Class MigrationsTable
 * @package Bim\Db\Entity
 */
class MigrationsTable
{
    /**
     * getTableName
     * @return string
     */
    public static function getTableName()
    {
        return 'bim_migrations';
    }

    /**
     * isExistsInTable
     * @param $id
     * @return bool
     * @throws Exception
     */
    public static function isExistsInTable($id)
    {
        global $DB;
        if ($result = $DB->Query("SELECT 'id' FROM " . self::getTableName() . " WHERE id = '" . $id . "'", true)) {
            if ($result->AffectedRowsCount()) {
                return true;
            }
        } else {
            throw new Exception($DB->GetErrorMessage());
        }
        return false;
    }

    /**
     * add
     * @param $id
     * @return bool
     * @throws Exception
     */
    public static function add($id)
    {
        global $DB;
        if (!self::isExistsInTable($id)) {
            $DB->Add(self::getTableName(),array(
                "id" => $id
            ));
            if (self::isExistsInTable($id)){
                return true;
            }
        }
        return false;
    }

    /**
     * delete
     * @param $id
     * @return bool
     * @throws Exception
     */
    public static function delete($id)
    {
        global $DB;
        if ($result = $DB->Query("DELETE FROM " . self::getTableName() . " WHERE id = '" . $id . "'", true)) {
            return true;
        } else {
            throw new Exception($DB->GetErrorMessage());
        }
        return false;
    }


}
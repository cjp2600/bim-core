<?php
namespace Bim\Db\Entity;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

/**
 * Class MigrationsTable
 *
 * Fields:
 * <ul>
 * <li> id string(255) mandatory
 * <li> migration string(255) mandatory
 * <li> content string mandatory
 * </ul>
 *
 * @package Bitrix\Migrations
 **/

class MigrationsTable extends Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'bim_migrations';
    }

    public static function getMap()
    {
        return array(
            'id' => array(
                'primary' => true,
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateId'),
            ),
            'migration' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateMigration'),
            ),
            'content' => array(
                'data_type' => 'text',
                'required' => false,
            ),
        );
    }
    public static function validateId()
    {
        return array(
            new Entity\Validator\Length(null, 255),
        );
    }
    public static function validateMigration()
    {
        return array(
            new Entity\Validator\Length(null, 255),
        );
    }
}
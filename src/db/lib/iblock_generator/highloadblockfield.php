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
    public function generateAddCode( $params )
    {
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Добавляем поля сущностей highload инфоблока */'.PHP_EOL.PHP_EOL;
        $hlblockData = $this->ownerItemDbData['hlblockData'];
        foreach( $this->ownerItemDbData['hlFieldData'] as $hlFieldData  ){

            $code = $code . $this->buildCode('HighloadblockFieldIntegrate', 'Add', array( $hlblockData['NAME'], $hlFieldData ) ) .PHP_EOL.PHP_EOL;
        }
        return $code;
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
    public function generateDeleteCode( $params )
    {
        $this->checkParams( $params );
        $code = '<?php'.PHP_EOL.'/*  Удаляем  поля сущностей highload инфоблока   */'.PHP_EOL.PHP_EOL;
        $hlblockData = $this->ownerItemDbData['hlblockData'];
        foreach( $this->ownerItemDbData['hlFieldData'] as $hlFieldData  ){
            $code = $code . $this->buildCode('HighloadblockFieldIntegrate', 'Delete', array( $hlblockData['NAME'], $hlFieldData['FIELD_NAME'] ) ) .PHP_EOL.PHP_EOL;
        }
        return $code;
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
        foreach ($params['hlFieldId'] as $hlFieldId) {
            $userFieldData = \CUserTypeEntity::GetByID($hlFieldId);
            if ($userFieldData === false || empty($userFieldData)) {
                throw new \Exception('Не найдено пользовательское поле с id = ' . $hlFieldId);
            }
            $this->ownerItemDbData['hlFieldData'][] = $userFieldData;
        }
    }



}

?>

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
     * @param $params array
     * @return mixed
     */
    public function generateAddCode( $params )
    {
        $this->checkParams( $params );
        $code = false;
        foreach( $this->ownerItemDbData as $iblockData  ) {
            $addFields = $iblockData;
            unset( $addFields['ID'] );
            $addFields['FIELDS'] = \CIBlock::GetFields( $iblockData['ID'] );
            $addFields['GROUP_ID'] = \CIBlock::GetGroupPermissions( $iblockData['ID'] );
            $code = $this->buildCode('Bim\Db\Iblock\IblockIntegrate', 'Add', array( $addFields ) ) .PHP_EOL.PHP_EOL;
        }
        return $code;
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
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params )
    {
        $this->checkParams( $params );
        $code = false;
        foreach( $this->ownerItemDbData as $iblockData  ){
            $code =  $this->buildCode('Bim\Db\Iblock\IblockIntegrate', 'Delete', array( $iblockData['CODE'] ) );
        }
        return $code;
    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     * iblockId => id инфоблоков
     * )
     * @return mixed
     * @throws \Exception
     */
    public function checkParams( $params  )
    {
        if ( !isset( $params['iblockId'] ) || empty( $params['iblockId'] ) ) {
            throw new \Exception( 'В параметрах не найден iblockId' );
        }
        foreach( $params['iblockId'] as $iblockId ) {
            $iblockDbRes = \CIBlock::GetByID( $iblockId );
            if ( $iblockDbRes === false || !$iblockDbRes->SelectedRowsCount() ) {
                throw new \Exception( 'В системе не найден  инфоблок с id = ' . $iblockId );
            }
            $iblockData = $iblockDbRes->Fetch();
            if ( !strlen($iblockData['CODE']) ) {
                throw new \Exception('У инфоблока "' . $iblockData['NAME'] . '" не указан символьный код' );
            }
            $this->ownerItemDbData[] = $iblockData;
        }
    }
}
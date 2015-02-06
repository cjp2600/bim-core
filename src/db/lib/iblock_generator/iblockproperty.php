<?php

/**
 * Class IblockPropertyGen
 * класс для генерацияя кода изменений в типах инфоблоков:
 *
 * @package Bitrix\Adv_Preset\IblockPropertyGen
 */
class IblockPropertyGen extends CodeGenerator
{


    public function __construct(){
        \CModule::IncludeModule('iblock');
    }
    /**
     * метод для генерации кода добавления нового свойства инфоблока
     * @param $params array
     * @return mixed
     */
    public function generateAddCode( $params ){
        $this->checkParams( $params );

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php'.PHP_EOL.'/*  Добавляем новое свойство инфоблока */'.PHP_EOL.PHP_EOL;
        foreach( $ownerItemDbData['propertyData'] as $propertyData ) {
            $addFields = $propertyData;
            $addFields['IBLOCK_CODE'] = $ownerItemDbData['iblockData']['CODE'];
            unset( $addFields['ID'] );
            if ( $propertyData['PROPERTY_TYPE'] == 'L' ) {
                $addFields['VALUES'] = $this->getEnumItemList( $params['iblockId'], $propertyData['ID'] );
            }
            if ( isset( $propertyData['LINK_IBLOCK_ID'] ) ) {
                $addFields['LINK_IBLOCK_CODE'] = $this->getIblockCode( $propertyData['LINK_IBLOCK_ID'] );

            }

            $code = $code . $this->buildCode('IblockPropertyIntegrate', 'Add', array(  $addFields ) ) .PHP_EOL.PHP_EOL;    
        }

        return $code;

    }
    /**
     * метод для генерации кода обновления  свойства инфоблока
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode( $params ){
        $this->checkParams( $params );

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php'.PHP_EOL.'/*  Обновляем свойства инфоблока */'.PHP_EOL.PHP_EOL;
        foreach( $ownerItemDbData['propertyData'] as $propertyData ) {
            $updateFields = $propertyData;
            unset( $updateFields['ID'] );
            if ( $propertyData['PROPERTY_TYPE'] == 'L' ) {
                $updateFields['VALUES'] = $this->getEnumItemList( $params['iblockId'], $propertyData['ID'] );
            }
            if ( isset( $propertyData['LINK_IBLOCK_ID'] ) ) {
                $updateFields['LINK_IBLOCK_CODE'] = $this->getIblockCode( $propertyData['LINK_IBLOCK_ID'] );

            }

            $code = $code . $this->buildCode('IblockPropertyIntegrate', 'Update', array( $ownerItemDbData['iblockData']['CODE'], $updateFields['CODE'], $updateFields ) ) .PHP_EOL.PHP_EOL;
        }

        return $code;

    }

    /**
     * метод для генерации кода удаления  свойства инфоблока
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params ){
        $this->checkParams( $params );

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php'.PHP_EOL.'/*  Удаляем свойства инфоблока  */'.PHP_EOL.PHP_EOL;
        foreach( $ownerItemDbData['propertyData'] as $propertyData ) {

            $code = $code . $this->buildCode('IblockPropertyIntegrate', 'Delete', array( $ownerItemDbData['iblockData']['CODE'], $propertyData['CODE'] ) ) .PHP_EOL.PHP_EOL;
        }

        return $code;

    }


    /**
     * получаем список значений св-ва с типом "список"
     * @param $iblockPropId
     * @return array
     */
    private function getEnumItemList( $iblockId, $iblockPropId ) {
        $result = array();

        $propEnumDbRes = \CIBlockPropertyEnum::GetList(array('SORT' => 'ASC'), array('IBLOCK_ID' => $iblockId, 'PROPERTY_ID' => $iblockPropId ));
        if ( $propEnumDbRes !== false && $propEnumDbRes->SelectedRowsCount() ) {
            $index = 0;
            while( $propEnum =  $propEnumDbRes->Fetch() ) {
                $result[ $index ] = array(
                    'ID' => $index,
                    'VALUE' => $propEnum['VALUE'],
                    'XML_ID' => $propEnum['XML_ID'],
                    'SORT' => $propEnum['SORT'],
                    'DEF' => $propEnum['DEF']
                );
                $index++;
            }
        }


        return $result;


    }

    /**
     * получаем код инфоблока
     * @param $iblockId
     * @return bool
     */
    private function getIblockCode( $iblockId ) {
        $iblockDbRes = \CIBlock::GetByID( $iblockId );

        if ( $iblockDbRes !== false && $iblockDbRes->SelectedRowsCount() ) {
            $iblockData = $iblockDbRes->Fetch();
            if ( strlen( $iblockData['CODE'] ) ) {
                return $iblockData['CODE'];
            }
        }

        return false;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
                iblockId => (int) id инфоблока,
     *          propertyId => (array) массив с id свойств
     *   )
     * @return mixed
     */
    public function checkParams( $params  ) {

        if ( !isset( $params['iblockId'] ) || !strlen( $params['iblockId'] ) ) {
            throw new \Exception( 'В параметрах не найден iblockId' );
        }

        if ( !isset( $params['propertyId'] ) || empty( $params['propertyId'] ) ) {
            throw new \Exception( 'В параметрах не найден propertyId' );
        }

        $iblockDbRes = \CIBlock::GetByID( $params['iblockId'] );
        if ( $iblockDbRes === false || !$iblockDbRes->SelectedRowsCount() ) {
            throw new \Exception( 'Не найден инфоблок с iblockId = ' . $params['iblockId'] );
        }
        $iblockData = $iblockDbRes->Fetch();
        if ( !strlen( $iblockData['CODE'] ) ) {
            throw new \Exception( 'В инфоблоке не указан символьный код' );
        }
        $this->ownerItemDbData[ 'iblockData' ] = $iblockData;
        $this->ownerItemDbData[ 'propertyData' ] = array();
        foreach( $params['propertyId'] as $propertyId ) {
            $propertyDbRes = \CIBlockProperty::GetByID( $propertyId );
            if ( $propertyDbRes === false || !$propertyDbRes->SelectedRowsCount() ) {
                throw new \Exception( 'Не найдено св-во с id = ' . $propertyId );
            }

            $propertyData = $propertyDbRes->Fetch();
            if ( !strlen( $propertyData['CODE'] ) ) {
                throw new \Exception( 'В свойстве c id =' . $propertyData['ID'] . ' не указан символьный код.' );
            }
            $this->ownerItemDbData[ 'propertyData' ][] = $propertyData;

        }

    }



}

?>

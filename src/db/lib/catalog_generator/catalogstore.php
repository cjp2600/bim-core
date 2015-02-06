<?php

/**
 * Class CatalogStoreGen
 * класс для генерации кода изменений в данных склада
 *
 * @package Bitrix\Adv_Preset\CatalogStore
 */
class CatalogStoreGen extends CodeGenerator
{


    public function __construct(){
        \CModule::IncludeModule('catalog');
    }
    /**
     * метод для генерации кода добавления нового  склада
     * @param $params array
     * @return mixed
     */
    public function generateAddCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Добавляем новый склад */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $storeData  ){
            $addFields = $storeData;
            unset( $addFields['ID'] );

            $code = $code . $this->buildCode('CatalogStoreIntegrate', 'Add', array( $addFields ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }
    /**
     * метод для генерации кода обновления склада
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Обновляем склад */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $storeData  ){
            $updateFields = $storeData;
            unset( $updateFields['ID'] );

            $code = $code . $this->buildCode('CatalogStoreIntegrate', 'Update', array( $updateFields['XML_ID'], $updateFields ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления  склада
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Удаляем  склад   */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $storeData  ){
            $code = $code . $this->buildCode('CatalogStoreIntegrate', 'Delete', array( $storeData['XML_ID'] ) );
        }

        return $code;

    }




    /**
     * метод проверки передаваемых параметров
     * @param $params array(
                storeId => id склада
     * )
     * @return mixed
     */
    public function checkParams( $params  ) {

        if ( !isset( $params['storeId'] ) || empty( $params['storeId'] ) ) {
            throw new \Exception( 'В параметрах не найден storeId' );
        }

        foreach( $params['storeId'] as $storeId ) {
            $storeDbRes = \CCatalogStore::GetList( array(), array('ID' => $storeId) );
            if ( $storeDbRes === false || !$storeDbRes->SelectedRowsCount() ) {
                throw new \Exception( 'В системе не найден  склад с id = ' . $storeId );
            }

            $storeData = $storeDbRes->Fetch();
            if ( !strlen($storeData['XML_ID']) ) {
                throw new \Exception('У склада "' . $storeData['TITLE'] . '" не указан XML_ID' );
            }
            $this->ownerItemDbData[] = $storeData;
        }





    }



}

?>

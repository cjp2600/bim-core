<?php

/**
 * Class CatalogVatGen
 * класс для генерации кода изменений в данных ставок НДС
 *
 * @package Bitrix\Adv_Preset\CatalogVatGen
 */
class CatalogVatGen extends CodeGenerator
{


    public function __construct(){
        \CModule::IncludeModule('catalog');
    }
    /**
     * метод для генерации кода добавления новой ставки НДС
     * @param $params array
     * @return mixed
     */
    public function generateAddCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Добавляем ставку НДС */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $catalogVatData  ){
            $addFields = $catalogVatData;
            unset( $addFields['ID'] );

            $code = $code . $this->buildCode('CatalogVatIntegrate', 'Add', array( $addFields ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }
    /**
     * метод для генерации кода обновления ставки НДС
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Обновляем ставку НДС */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $catalogVatData  ){
            $updateFields = $catalogVatData;
            unset( $updateFields['ID'] );

            $code = $code . $this->buildCode('CatalogVatIntegrate', 'Update', array( $updateFields['NAME'], $updateFields ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления  ставки НДС
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Удаляем  ставку НДС   */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $catalogVatData  ){
            $code = $code . $this->buildCode('CatalogVatIntegrate', 'Delete', array( $catalogVatData['NAME'] ) );
        }

        return $code;

    }




    /**
     * метод проверки передаваемых параметров
     * @param $params array(
                catalogVatId => id ставки НДС
     * )
     * @return mixed
     */
    public function checkParams( $params  ) {

        if ( !isset( $params['catalogVatId'] ) || empty( $params['catalogVatId'] ) ) {
            throw new \Exception( 'В параметрах не найден catalogVatId' );
        }

        foreach( $params['catalogVatId'] as $catalogVatId ) {
            $catalogVatDbRes = \CCatalogVat::GetList( array(), array('ID' => $catalogVatId) );
            if ( $catalogVatDbRes === false || !$catalogVatDbRes->SelectedRowsCount() ) {
                throw new \Exception( 'В системе не найдена наценка с id = ' . $catalogVatId );
            }

            $catalogVatData = $catalogVatDbRes->Fetch();
            $this->ownerItemDbData[] = $catalogVatData;
        }





    }



}

?>

<?php

/**
 * Class CatalogDiscountGen
 * класс для генерации кода изменений в данных склада
 *
 * @package Bitrix\Adv_Preset\CatalogDiscount
 */
class CatalogDiscountGen extends CodeGenerator
{


    public function __construct(){
        \CModule::IncludeModule('catalog');
    }
    /**
     * метод для генерации кода добавления новой скидки
     * @param $params array
     * @return mixed
     */
    public function generateAddCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Добавляем новую скидку */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $discountData  ){
            $addFields = $discountData;
            unset( $addFields['ID'] );

            $code = $code . $this->buildCode('CatalogDiscountIntegrate', 'Add', array( $addFields ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }
    /**
     * метод для генерации кода обновления скидки
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Обновляем скидку */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $discountData  ){
            $updateFields = $discountData;
            unset( $updateFields['ID'] );

            $code = $code . $this->buildCode('CatalogDiscountIntegrate', 'Update', array( $updateFields['XML_ID'], $updateFields ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления  скидки
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Удаляем  скидку   */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $discountData  ){
            $code = $code . $this->buildCode('CatalogDiscountIntegrate', 'Delete', array( $discountData['XML_ID'] ) );
        }

        return $code;

    }




    /**
     * метод проверки передаваемых параметров
     * @param $params array(
                discountId => id инфоблоков
     * )
     * @return mixed
     */
    public function checkParams( $params  ) {

        if ( !isset( $params['discountId'] ) || empty( $params['discountId'] ) ) {
            throw new \Exception( 'В параметрах не найден discountId' );
        }

        foreach( $params['discountId'] as $discountId ) {
            $discountDbRes = \CCatalogDiscount::GetList( array(), array('ID' => $discountId) );
            if ( $discountDbRes === false || !$discountDbRes->SelectedRowsCount() ) {
                throw new \Exception( 'В системе не найден  склад с id = ' . $discountId );
            }

            $discountData = $discountDbRes->Fetch();
            if ( !strlen($discountData['XML_ID']) ) {
                throw new \Exception('У скидки "' . $discountData['NAME'] . '" не указан XML_ID' );
            }
            $this->ownerItemDbData[] = $discountData;
        }





    }



}

?>

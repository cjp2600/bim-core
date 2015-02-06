<?php


/**
 * Class SaleDiscountGen
 * класс для генерации кода изменений скидок на сумму заказа
 *
 * @package Bitrix\Adv_Preset\SaleDiscount
 */
class SaleDiscountGen extends CodeGenerator
{


    public function __construct(){
        \CModule::IncludeModule('sale');
    }
    /**
     * метод для генерации кода добавления новую скидку
     * @param $params array
     * @return mixed
     */
    public function generateAddCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Добавляем новую скидку */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $discountData  ){
            $addFields = $discountData;
            unset( $addFields['ID'] );

            $code = $code . $this->buildCode('SaleDiscountIntegrate', 'Add', array( $addFields ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }
    /**
     * метод для генерации кода обновления службы доставки
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Обновляем скидку */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $discountData  ){
            $updateFields = $discountData;
            unset( $updateFields['ID'] );

            $code = $code . $this->buildCode('SaleDiscountIntegrate', 'Update', array( $updateFields['XML_ID'], $updateFields ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления  службы доставки
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Удаляем  скидку   */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $discountData  ){
            $code = $code . $this->buildCode('SaleDiscountIntegrate', 'Delete', array( $discountData['XML_ID'] ) );
        }

        return $code;

    }




    /**
     * метод проверки передаваемых параметров
     * @param $params array(
                discountId => id скидки
     * )
     * @return mixed
     */
    public function checkParams( $params  ) {

        if ( !isset( $params['discountId'] ) || empty( $params['discountId'] ) ) {
            throw new \Exception( 'В параметрах не найден discountId' );
        }

        foreach( $params['discountId'] as $discountId ) {
            $discountData = \CSaleDiscount::GetByID( $discountId );

            if ( !strlen($discountData['XML_ID']) ) {
                throw new \Exception('У скидки с id = "' . $discountData['ID'] . '" не указан XML_ID' );
            }

            $discountGroupDbRes = CSaleDiscount::GetDiscountGroupList( array('DISCOUNT_ID' => $discountId ) );
            while( $discountGroupData = $discountGroupDbRes->Fetch() ) {
                $discountData['USER_GROUPS'][] = $discountGroupData['GROUP_ID'];
            }

            $this->ownerItemDbData[] = $discountData;
        }





    }



}

?>

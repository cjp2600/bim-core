<?php


/**
 * Class CatalogDiscountCouponGen
 * класс для генерации кода изменений  купонов скидок
 *
 * @package Bitrix\Adv_Preset\CatalogDiscountCouponGen
 */
class CatalogDiscountCouponGen extends CodeGenerator
{


    public function __construct(){
        \CModule::IncludeModule('catalog');
    }
    /**
     * метод для генерации кода добавления нового купона скидки
     * @param $params array
     * @return mixed
     */
    public function generateAddCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Добавляем новый купон скидки */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $accData  ){
            $addFields = $accData;

            $code = $code . $this->buildCode('CatalogDiscountCouponIntegrate', 'Add', array( $addFields ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }
    /**
     * метод для генерации кода обновления купона скидки
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Обновляем купон скидки */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $accData  ){
            $updateFields = $accData;

            $code = $code . $this->buildCode('CatalogDiscountCouponIntegrate', 'Update', array( $updateFields['COUPON'], $updateFields ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления купона скидки
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Удаляем купон скидки   */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $accData  ){
            $code = $code . $this->buildCode('CatalogDiscountCouponIntegrate', 'Delete', array( $accData['COUPON'] ) );
        }

        return $code;

    }




    /**
     * метод проверки передаваемых параметров
     * @param $params array(
                couponId => id купона
     * )
     * @return mixed
     */
    public function checkParams( $params  ) {

        if ( !isset( $params['couponId'] ) || empty( $params['couponId'] ) ) {
            throw new \Exception( 'В параметрах не найден couponId' );
        }


        foreach( $params['couponId'] as $couponId ) {

            $couponData = \CCatalogDiscountCoupon::GetByID( $couponId  );
            if ( !$couponData ) {
                throw new \Exception( 'В системе не найден купон с id = "' . $couponId .'"' );
            }

            unset( $couponData['ID'] );
            $catalogDiscountData = CCatalogDiscount::GetByID( $couponData['DISCOUNT_ID'] );
            $couponData['DISCOUNT_XML_ID'] = $catalogDiscountData['XML'];
            unset( $couponData['DISCOUNT_ID'] );


            $this->ownerItemDbData[] = $couponData;
        }





    }



}

?>

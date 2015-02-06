<?php


/**
 * Class SaleUserAccountGen
 * класс для генерации кода изменений  счетов пользователей
 *
 * @package Bitrix\Adv_Preset\SaleUserAccountGen
 */
class SaleUserAccountGen extends CodeGenerator
{


    public function __construct(){
        \CModule::IncludeModule('sale');
    }
    /**
     * метод для генерации кода добавления нового счета пользователя
     * @param $params array
     * @return mixed
     */
    public function generateAddCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Добавляем новый счет пользователя */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $accData  ){
            $addFields = $accData;

            $code = $code . $this->buildCode('SaleUserAccountIntegrate', 'Add', array( $addFields ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }
    /**
     * метод для генерации кода обновления счета пользователя
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Обновляем счет пользователя */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $accData  ){
            $updateFields = $accData;

            $code = $code . $this->buildCode('SaleUserAccountIntegrate', 'Update', array( $updateFields['NOTES'], $updateFields ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления счета пользователя
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Удаляем счет пользователя   */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $accData  ){
            $code = $code . $this->buildCode('SaleUserAccountIntegrate', 'Delete', array( $accData['NOTES'] ) );
        }

        return $code;

    }




    /**
     * метод проверки передаваемых параметров
     * @param $params array(
                accId => id счета
     * )
     * @return mixed
     */
    public function checkParams( $params  ) {

        if ( !isset( $params['accId'] ) || empty( $params['accId'] ) ) {
            throw new \Exception( 'В параметрах не найден accId' );
        }


        foreach( $params['accId'] as $accId ) {

            $accData = \CSaleUserAccount::GetByID( $accId  );
            if ( !$accData ) {
                throw new \Exception( 'В системе не найден счет с id = "' . $accId .'"' );
            }
            if ( !strlen( $accData['NOTES'] ) ) {
                throw new \Exception( 'У счета с id = "' . $accId .'" не указан NOTES' );
            }
            unset( $accData['ID'] );


            $this->ownerItemDbData[] = $accData;
        }





    }



}

?>

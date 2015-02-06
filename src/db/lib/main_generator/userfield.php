<?php

/**
 * Class UserFieldGen
 * класс для генерацияя кода изменений в пользовательских полях
 *
 * @package Bitrix\Adv_Preset\Main_Generator
 */
class UserFieldGen extends CodeGenerator
{


    public function __construct(){
    }
    /**
     * метод для генерации кода добавления нового пользовательского поля
     * @param $params array
     * @return string
     */
    public function generateAddCode( $params ){
        $this->checkParams( $params );

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php'.PHP_EOL.'/*  Добавляем новое пользовательское поле */'.PHP_EOL.PHP_EOL;
        foreach( $ownerItemDbData as $userFieldData ) {
            $addFields = $userFieldData;
            unset( $addFields['ID'] );


            $code = $code . $this->buildCode('UserFieldIntegrate', 'Add', array(  $addFields ) ) .PHP_EOL.PHP_EOL;

        }

        return $code;

    }
    /**
     * метод для генерации кода обновления пользовательского поля
     * @param $params array
     * @return string
     */
    public function generateUpdateCode( $params ){
        $this->checkParams( $params );

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php'.PHP_EOL.'/*  Обновляем пользовательское поле */'.PHP_EOL.PHP_EOL;
        foreach( $ownerItemDbData as $userFieldData ) {
            unset( $userFieldData['ID'] );
            $updateFields = $userFieldData;

            $code = $code . $this->buildCode('UserFieldIntegrate', 'Update', array( $updateFields['ENTITY_ID'], $updateFields['FIELD_NAME'],  $updateFields ) ) .PHP_EOL.PHP_EOL;
        }

        return $code;

    }

    /**
     * метод для генерации кода удаления  пользовательского поля
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params ){
        $this->checkParams( $params );

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php'.PHP_EOL.'/*  Удаляем пользовательское поле  */'.PHP_EOL.PHP_EOL;
        foreach( $ownerItemDbData as $userFieldData ) {
            unset( $userFieldData['ID'] );
            $code = $code . $this->buildCode('UserFieldIntegrate', 'Delete', array( $userFieldData['ENTITY_ID'], $userFieldData['FIELD_NAME'] ) ) .PHP_EOL.PHP_EOL;
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     *          userFieldId => (array) массив с id пользовательских полей
     *
     *   )
     * @return mixed
     */
    public function checkParams( $params  ) {


        if ( !isset( $params['userFieldId'] ) || empty( $params['userFieldId'] ) ) {
            throw new \Exception( 'В параметрах не найден userFieldId' );
        }


        $this->ownerItemDbData = array();

        foreach( $params['userFieldId'] as $userFieldId ) {
            $userFieldData = \CUserTypeEntity::GetByID( $userFieldId  );
            if ( $userFieldData === false || empty( $userFieldData ) ) {
                throw new \Exception( 'Не найдено пользовательское поле с id = ' . $userFieldId );
            }

            
            $this->ownerItemDbData[] = $userFieldData;

        }

    }



}

?>

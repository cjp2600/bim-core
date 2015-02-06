<?php

/**
 * Class GroupGen
 * класс для генерацияя кода изменений в группах пользователей
 *
 * @package Bitrix\Adv_Preset\Main_Generator
 */
class GroupGen extends CodeGenerator
{


    public function __construct(){
    }
    /**
     * метод для генерации кода добавления новой группы пользователей
     * @param $params array
     * @return string
     */
    public function generateAddCode( $params ){
        $this->checkParams( $params );

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php'.PHP_EOL.'/*  Добавляем новую группы пользователей */'.PHP_EOL.PHP_EOL;
        foreach( $ownerItemDbData as $groupData ) {
            unset( $groupData['ID'] );
            $addFields = $groupData;

            $code = $code . $this->buildCode('GroupIntegrate', 'Add', array(  $addFields ) ) .PHP_EOL.PHP_EOL;
        }

        return $code;

    }
    /**
     * метод для генерации кода обновления группы пользователей
     * @param $params array
     * @return string
     */
    public function generateUpdateCode( $params ){
        $this->checkParams( $params );

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php'.PHP_EOL.'/*  Обновляем группы пользователей */'.PHP_EOL.PHP_EOL;
        foreach( $ownerItemDbData as $groupData ) {
            unset( $groupData['ID'] );
            $updateFields = $groupData;

            $code = $code . $this->buildCode('GroupIntegrate', 'Update', array( $updateFields['STRING_ID'],  $updateFields ) ) .PHP_EOL.PHP_EOL;
        }

        return $code;

    }

    /**
     * метод для генерации кода удаления  группы пользователей
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params ){
        $this->checkParams( $params );

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php'.PHP_EOL.'/*  Удаляем группы пользователей  */'.PHP_EOL.PHP_EOL;
        foreach( $ownerItemDbData as $groupData ) {
            unset( $groupData['ID'] );
            $code = $code . $this->buildCode('GroupIntegrate', 'Delete', array(  $groupData['STRING_ID'] ) ) .PHP_EOL.PHP_EOL;
        }

        return $code;

    }




    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     *          groupId => (array) массив с id групп пользователей
     *   )
     * @return mixed
     */
    public function checkParams( $params  ) {


        if ( !isset( $params['groupId'] ) || empty( $params['groupId'] ) ) {
            throw new \Exception( 'В параметрах не найден groupId' );
        }


        $this->ownerItemDbData = array();

        foreach( $params['groupId'] as $groupId ) {
            $groupDbRes = \CGroup::GetList( $by = 'id', $order = 'desc', array('ID' => $groupId )  );
            if ( $groupDbRes === false || !$groupDbRes->SelectedRowsCount() ) {
                throw new \Exception( 'Не найдена группа с id = ' . $groupId );
            }

            $groupData = $groupDbRes->Fetch();
            if ( !strlen( $groupData['STRING_ID'] ) ) {
                throw new \Exception( 'У группы с id = ' . $groupId . ' не найден символьный код' );
            }
            
            $this->ownerItemDbData[] = $groupData;

        }

    }



}

?>

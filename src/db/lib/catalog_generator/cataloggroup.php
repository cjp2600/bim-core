<?php

/**
 * Class CatalogGroupGen
 * класс для генерации кода изменений в типе цен
 *
 * @package Bitrix\Adv_Preset\CatalogGroup
 */
class CatalogGroupGen extends CodeGenerator
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

        $code = '<?php'.PHP_EOL.'/*  Добавляем новый тип цен */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $groupData  ){
            $addFields = $groupData;
            unset( $addFields['ID'] );

            $code = $code . $this->buildCode('CatalogGroupIntegrate', 'Add', array( $addFields ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }
    /**
     * метод для генерации кода обновления типа цен
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Обновляем тип цен */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $groupData  ){
            $updateFields = $groupData;
            unset( $updateFields['ID'] );

            $code = $code . $this->buildCode('CatalogGroupIntegrate', 'Update', array( $updateFields['XML_ID'], $updateFields ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления типа цен
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Удаляем  тип цен   */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $groupData  ){
            $code = $code . $this->buildCode('CatalogGroupIntegrate', 'Delete', array( $groupData['XML_ID'] ) );
        }

        return $code;

    }




    /**
     * метод проверки передаваемых параметров
     * @param $params array(
                groupId => id типа цен
     * )
     * @return mixed
     */
    public function checkParams( $params  ) {

        if ( !isset( $params['groupId'] ) || empty( $params['groupId'] ) ) {
            throw new \Exception( 'В параметрах не найден groupId' );
        }

        foreach( $params['groupId'] as $groupId ) {
            $groupData = \CCatalogGroup::GetByID( $groupId );
            if ( $groupData === false  ) {
                throw new \Exception( 'В системе не найден  склад с id = ' . $groupId );
            }

            if ( !strlen($groupData['XML_ID']) ) {
                throw new \Exception('У типа цен "' . $groupData['NAME'] . '" не указан XML_ID' );
            }
            $groupData['USER_GROUP'] = array();
            $groupData['USER_GROUP_BUY'] = array();
            $groupUserDbRes = \CCatalogGroup::GetGroupsList(array( 'CATALOG_GROUP_ID' => $groupId ));
            while( $groupUserData = $groupUserDbRes->Fetch() ) {

                $groupData['USER_GROUP'][] = $groupUserData['GROUP_ID'];
                if ( $groupUserData['BUY'] == 'Y' ) {
                    $groupData['USER_GROUP_BUY'][] = $groupUserData['GROUP_ID'];
                }

            };

            $groupLangDbRes = \CCatalogGroup::GetLangList(array( 'CATALOG_GROUP_ID' => $groupId ));
            while( $groupLangData = $groupLangDbRes->Fetch() ) {
                $groupData['USER_LANG'][ $groupLangData['LID'] ] = $groupLangData['NAME'];

            }


            $this->ownerItemDbData[] = $groupData;
        }





    }



}

?>

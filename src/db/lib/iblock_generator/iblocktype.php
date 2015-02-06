<?php

/**
 * Class IblockType
 * класс для генерацияя кода изменений в типах инфоблоков:
 *
 * @package Bitrix\Adv_Preset\IblockTypeGen
 */
class IblockTypeGen extends CodeGenerator
{


    public function __construct(){
        \CModule::IncludeModule('iblock');
    }
    /**
     * метод для генерации кода добавления нового типа инфоблоков
     * @param $params array
     * @return mixed
     */
    public function generateAddCode( $params ){
        $this->checkParams( $params );

        $addFields = $this->ownerItemDbData;
        $addFields['LANG'] = $this->getLangData( $params['iblockTypeId'] );

        $code = '<?php'.PHP_EOL.'/*  Добавляем новый тип ИБ */'.PHP_EOL.PHP_EOL;

        $code = $code . $this->buildCode('IblockTypeIntegrate', 'Add', array( $addFields ) );

        return $code;

    }
    /**
     * метод для генерации кода обновления  типа инфоблоков
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode( $params ){
        $this->checkParams( $params );

        $updateFields = $this->ownerItemDbData;
        unset( $updateFields['ID'] );
        $updateFields['LANG'] = $this->getLangData( $params['iblockTypeId'] );

        $code = '<?php'.PHP_EOL.'/*  Обновляем тип ИБ с id = "' . $params['iblockTypeId'] .'"  */'.PHP_EOL.PHP_EOL;

        $code = $code . $this->buildCode('IblockTypeIntegrate', 'Update', array( $params['iblockTypeId'], $updateFields ) );

        return $code;

    }

    /**
     * метод для генерации кода удаления типа инфоблоков
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Удаляем тип ИБ с id = "' . $params['iblockTypeId'] .'"  */'.PHP_EOL.PHP_EOL;

        $code = $code . $this->buildCode('IblockTypeIntegrate', 'Delete', array( $params['iblockTypeId'] ) );

        return $code;

    }


    /**
     * получаем языкозависимые названия и заголовки
     * @param $iblockTypeId
     * @return array
     */
    private function getLangData( $iblockTypeId ) {
        $result = array();
        $langDbRes = CLanguage::GetList($by="lid", $order="desc", Array());
        while( $langData = $langDbRes->Fetch() ) {
            $typeLangItemTmp = CIBlockType::GetByIDLang( $iblockTypeId, $langData['LID'] );
            $typeLangItem = array();
            foreach( $typeLangItemTmp as $key => $value ) {
                if ( strstr( $key, '~') ) {
                    continue;
                }
                $typeLangItem[ $key ] = $value;
            }

            $result[ $langData['LID'] ] = $typeLangItem;
        }


        return $result;


    }

    /**
     * метод проверки передаваемых параметров
     * @param $params array
     * @return mixed
     */
    public function checkParams( $params  ) {

        if ( !isset( $params['iblockTypeId'] ) || !strlen( $params['iblockTypeId'] ) ) {
            throw new \Exception( 'В параметрах не найден iblockTypeId' );
        }


        $iblockTypeDbRes = CIBlockType::GetByID( $params['iblockTypeId'] );
        if ( $iblockTypeDbRes === false || !$iblockTypeDbRes->SelectedRowsCount() ) {
            throw new \Exception( 'В системе не найден тип инфоблока с id = ' . $params['iblockTypeId'] );
        }

        $this->ownerItemDbData = $iblockTypeDbRes->Fetch();




    }



}

?>

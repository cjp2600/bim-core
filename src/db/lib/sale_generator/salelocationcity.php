<?php


/**
 * Class SaleLocationCityGen
 * класс для генерации кода изменений города местоположения
 *
 * @package Bitrix\Adv_Preset\SaleLocationCity
 */
class SaleLocationCityGen extends CodeGenerator
{


    public function __construct(){
        \CModule::IncludeModule('sale');
    }
    /**
     * метод для генерации кода добавления нового города местоположения
     * @param $params array
     * @return mixed
     */
    public function generateAddCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Добавляем новый город местоположения */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $cityData  ){
            $addFields = $cityData;
            unset( $addFields['ID'] );
            unset( $addFields['REGION_ID'] );

            $code = $code . $this->buildCode('SaleLocationCityIntegrate', 'Add', array( $addFields ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }
    /**
     * метод для генерации кода обновления города местоположения
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Обновляем город местоположения */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $cityData  ){
            $updateFields = $cityData;
            unset( $updateFields['ID'] );
            unset( $updateFields['REGION_ID'] );
            $updateFields['OLD_NAME'] = $cityData['NAME'];

            $code = $code . $this->buildCode('SaleLocationCityIntegrate', 'Update', array(  $updateFields ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления  города местоположения
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Удаляем  город местоположения   */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $cityData  ){
            $code = $code . $this->buildCode('SaleLocationCityIntegrate', 'Delete', array( $cityData['NAME'] ) );
        }

        return $code;

    }




    /**
     * метод проверки передаваемых параметров
     * @param $params array(
                cityId => id городов
     * )
     * @return mixed
     */
    public function checkParams( $params  ) {

        if ( !isset( $params['cityId'] ) || empty( $params['cityId'] ) ) {
            throw new \Exception( 'В параметрах не найден cityId' );
        }


        foreach( $params['cityId'] as $cityId ) {

            $cityData = \CSaleLocation::GetCityByID( $cityId  );
            $dbLang = \CLangAdmin::GetList($by = "sort", $order = "asc");
            while ($arLang = $dbLang->Fetch())
            {
                $cityLang = \CSaleLocation::GetCityLangByID( $cityId, $arLang['LID']  );
                unset( $cityLang['ID'] );
                unset( $cityLang['CITY_ID'] );
                $cityData[ $arLang['LID'] ] =$cityLang;

            }


            $this->ownerItemDbData[] = $cityData;
        }





    }



}

?>

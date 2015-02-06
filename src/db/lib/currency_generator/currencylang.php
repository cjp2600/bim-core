<?php


/**
 * Class CurrencyLangGen
 * класс для генерации кода изменений языкозависимых параметров валют
 *
 * @package Bitrix\Adv_Preset\CurrencyGen
 */
class CurrencyLangGen extends CodeGenerator
{


    public function __construct(){
        \CModule::IncludeModule('currency');
    }
    /**
     * метод для генерации кода добавления языкозависимых параметров валют
     * @param $params array
     * @return mixed
     */
    public function generateAddCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Добавляем языкозависимые параметры валют */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $currencyData  ){
            $addFields = $currencyData;

            $code = $code . $this->buildCode('CurrencyLangIntegrate', 'Add', array( $addFields ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }
    /**
     * метод для генерации кода обновления языкозависимых параметров валют
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Обновляем языкозависимые параметры валют */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $currencyData  ){
            $updateFields = $currencyData;

            $code = $code . $this->buildCode('CurrencyLangIntegrate', 'Update', array( $updateFields['CURRENCY'], $updateFields['LID'],  $updateFields ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления языкозависимых параметров валют
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Удаляем языкозависимые параметры валют  */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $currencyData  ){
            $code = $code . $this->buildCode('CurrencyLangIntegrate', 'Delete', array( $currencyData['CURRENCY'], $currencyData['LID'] ) );
        }

        return $code;

    }




    /**
     * метод проверки передаваемых параметров
     * @param $params array(
                currencyId => код валюты
     * )
     * @return mixed
     */
    public function checkParams( $params  ) {

        if ( !isset( $params['currencyId'] ) || empty( $params['currencyId'] ) ) {
            throw new \Exception( 'В параметрах не найден currencyId' );
        }


        foreach( $params['currencyId'] as $currencyId ) {


            $dbLang = \CLangAdmin::GetList($by = "sort", $order = "asc");
            while ($arLang = $dbLang->Fetch())
            {
                $currencyLang = \CCurrencyLang::GetByID( $currencyId, $arLang['LID']  );
                $this->ownerItemDbData[] = $currencyLang;

            }



        }





    }



}

?>

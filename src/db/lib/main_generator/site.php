<?php

/**
 * Class SiteGen
 * класс для генерации кода изменений в данных сайта
 *
 * @package Bitrix\Adv_Preset\Main_Generator
 */
class SiteGen extends CodeGenerator
{


    public function __construct(){
    }
    /**
     * метод для генерации кода добавления нового сайта
     * @param $params array
     * @return string
     */
    public function generateAddCode( $params ){
        $this->checkParams( $params );

        $siteData = $this->ownerItemDbData;
        $code = '<?php'.PHP_EOL.'/*  Добавляем новый сайт */'.PHP_EOL.PHP_EOL;
        unset( $siteData['ID'] );
        $addFields = $siteData;

        $code = $code . $this->buildCode('SiteIntegrate', 'Add', array(  $addFields ) ) .PHP_EOL.PHP_EOL;

        return $code;

    }
    /**
     * метод для генерации кода обновления  сайта
     * @param $params array
     * @return string
     */
    public function generateUpdateCode( $params ){
        $this->checkParams( $params );

        $siteData = $this->ownerItemDbData;
        $code = '<?php'.PHP_EOL.'/*  Обновляем сайт */'.PHP_EOL.PHP_EOL;
        $updateFields = $siteData;

        $code = $code . $this->buildCode('SiteIntegrate', 'Update', array( $updateFields['LID'], $updateFields ) ) .PHP_EOL.PHP_EOL;

        return $code;

    }

    /**
     * метод для генерации кода удаления  сайта
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params ){
        $this->checkParams( $params );

        $siteData = $this->ownerItemDbData;
        $code = '<?php'.PHP_EOL.'/*  Удаляем сайт  */'.PHP_EOL.PHP_EOL;

        $code = $code . $this->buildCode('SiteIntegrate', 'Delete', array( $siteData['LID'] ) ) .PHP_EOL.PHP_EOL;

        return $code;

    }




    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     *          siteId => (string) id сайта
     *   )
     * @return mixed
     */
    public function checkParams( $params  ) {


        if ( !isset( $params['siteId'] ) || empty( $params['siteId'] ) ) {
            throw new \Exception( 'В параметрах не найден siteId' );
        }


        $this->ownerItemDbData = array();
        $siteId = $params['siteId'];

        $siteDbRes = \CSite::GetList( $by="lid", $order="desc", array('LID' => $siteId )  );
        if ( $siteDbRes === false || !$siteDbRes->SelectedRowsCount() ) {
            throw new \Exception( 'Не найден сайт с id = ' . $siteId );
        }

        $siteData = $siteDbRes->Fetch();

        $this->ownerItemDbData = $siteData;



    }



}

?>

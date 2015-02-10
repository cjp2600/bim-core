<?php

namespace Bim\Db\Lib;

use \Bitrix\Highloadblock as HL;


/**
 * Class HighloadblockGen
 * класс для генерации кода изменений в highload инфоблоке
 *
 * @package Bitrix\Adv_Preset\HighloadblockGen
 */
class HighloadblockGen extends \Bim\Db\Lib\CodeGenerator
{


    public function __construct(){
        \CModule::IncludeModule("highloadblock");
    }

    /**
     * метод для генерации кода добавления нового highload инфоблока
     * @param array $hlblockId
     * @return mixed
     * @throws \Exception
     * @internal param array $params
     */
    public function generateAddCode( $hlblockId )
    {
        $hlblock = HL\HighloadBlockTable::getById( $hlblockId )->fetch();
        if ( !$hlblock ) {
            throw new \Exception( 'В системе не найден highload инфоблок с id = ' . $hlblockId );
        }
        return $this->getMethodContent('Bim\Db\Iblock\HighloadblockIntegrate', 'Add', array( $hlblock['NAME'], $hlblock['TABLE_NAME'] ));
    }

    /**
     * метод для генерации кода обновления highload инфоблока
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Обновляем highload ИБ */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $hlblockData  ){

            $code = $code . $this->buildCode('HighloadblockIntegrate', 'Update', array( $hlblockData['NAME'], $hlblockData['TABLE_NAME'], 'entity' ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления highload инфоблока
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Удаляем  ИБ   */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $hlblockData  ){

            $code = $code . $this->buildCode('HighloadblockIntegrate', 'Delete', array( $hlblockData['NAME'] ) ) .PHP_EOL.PHP_EOL;

        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     * hlblockId => array id highload инфоблоков
     * )
     * @return mixed
     * @throws \Exception
     */
    public function checkParams( $params ) {

        if ( !isset( $params['hlblockId'] ) || empty( $params['hlblockId'] ) ) {
            throw new \Exception( 'В параметрах не найден hlblockId' );
        }

        foreach( $params['hlblockId'] as $hlblockId ) {
            $hlblock = HL\HighloadBlockTable::getById( $hlblockId )->fetch();
            if ( !$hlblock ) {
                throw new \Exception( 'В системе не найден highload инфоблок с id = ' . $hlblockId );
            }


            $this->ownerItemDbData[] = $hlblock;
        }





    }



}

?>

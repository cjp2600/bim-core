<?php


/**
 * Class AdvContractGen
 * класс для генерации кода изменений рекламных контрактов
 *
 * @package Bitrix\Adv_Preset\Salecontract
 */
class AdvContractGen extends CodeGenerator
{


    public function __construct(){
        \CModule::IncludeModule('advertising');
    }
    /**
     * метод для генерации кода добавления рекламного контракта
     * @param $params array
     * @return mixed
     */
    public function generateAddCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Добавляем рекламный контракт */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $contractData  ){
            $addFields = $contractData;

            $code = $code . $this->buildCode('ADVContractIntegrate', 'Set', array(  $addFields, '', 'N' ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }
    /**
     * метод для генерации кода обновления рекламного контракта
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Обновляем рекламный контракт */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $contractData  ){
            $updateFields = $contractData;


            $code = $code . $this->buildCode('ADVContractIntegrate', 'Set', array( $updateFields, $updateFields['NAME'], 'N' ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления  рекламного контракта
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Удаляем  рекламный контракт   */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $contractData  ){
            $code = $code . $this->buildCode('ADVcontractIntegrate', 'Delete', array( $contractData['NAME'], 'N' ) );
        }

        return $code;

    }




    /**
     * метод проверки передаваемых параметров
     * @param $params array(
                contractId => id рекламного контракта
     * )
     * @return mixed
     */
    public function checkParams( $params  ) {

        if ( !isset( $params['contractId'] ) || empty( $params['contractId'] ) ) {
            throw new \Exception( 'В параметрах не найден contractId' );
        }

        foreach( $params['contractId'] as $contractId ) {

            $contractDbRes = \CAdvContract::GetByID( $contractId );
            $contractData = $contractDbRes->Fetch();
            if ( !strlen( $contractData['NAME'] ) ) {
                throw new \Exception( 'У контракта с id = ' . $contractId . ' не указан заголовок' );
            }
            unset( $contractData['ID'] );

            $this->ownerItemDbData[] = $contractData;
        }





    }



}

?>

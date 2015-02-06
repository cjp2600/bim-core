<?php


/**
 * Class SaleStatusGen
 * класс для генерации кода изменений статуса заказа
 *
 * @package Bitrix\Adv_Preset\SaleStatusGen
 */
class SaleStatusGen extends CodeGenerator
{


    public function __construct(){
        \CModule::IncludeModule('sale');
    }
    /**
     * метод для генерации кода добавления статуса заказа
     * @param $params array
     * @return mixed
     */
    public function generateAddCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Добавляем статус заказа */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $statusData  ){
            $addFields = $statusData;

            $code = $code . $this->buildCode('SaleStatusIntegrate', 'Add', array( $addFields ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }
    /**
     * метод для генерации кода обновления статуса заказа
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Обновляем статус заказа */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $statusData  ){
            $updateFields = $statusData;

            $code = $code . $this->buildCode('SaleStatusIntegrate', 'Update', array( $updateFields['ID'],  $updateFields ) ) .PHP_EOL.PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления статуса заказа
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params ){
        $this->checkParams( $params );

        $code = '<?php'.PHP_EOL.'/*  Удаляем статус заказа   */'.PHP_EOL.PHP_EOL;
        foreach( $this->ownerItemDbData as $statusData  ){
            $code = $code . $this->buildCode('SaleStatusIntegrate', 'Delete', array( $statusData['ID'] ) );
        }

        return $code;

    }




    /**
     * метод проверки передаваемых параметров
     * @param $params array(
                statusId => id типа плательщика
     * )
     * @return mixed
     */
    public function checkParams( $params  ) {

        if ( !isset( $params['statusId'] ) || empty( $params['statusId'] ) ) {
            throw new \Exception( 'В параметрах не найден statusId' );
        }


        foreach( $params['statusId'] as $statusId ) {

            $statusData = \CSaleStatus::GetByID( $statusId  );
            if ( !$statusData ) {
                throw new \Exception( 'В системе не найден статус с id = "' . $statusId .'"' );
            }
            $dbLang = \CLangAdmin::GetList($by = "sort", $order = "asc");
            while ($arLang = $dbLang->Fetch())
            {
                $statusLang = \CSaleStatus::GetByID( $statusId, $arLang['LID']  );
                unset( $statusLang['ID'] );
                unset( $statusLang['SORT'] );
                $statusData['LANG'][] = $statusLang;

            }
            $statusPermDbRes = CSaleStatus::GetPermissionsList(array(), array('STATUS_ID' => $statusId));
            while( $statusPermData = $statusPermDbRes->Fetch() ) {
                $groupDbRes = CGroup::GetById( $statusPermData['GROUP_ID'] );
                $groupData = $groupDbRes->Fetch();
                unset( $statusPermData['ID'] );
                unset( $statusPermData['GROUP_ID'] );
                unset( $statusPermData['STATUS_ID'] );
                $statusPermData['GROUP_CODE'] = $groupData['STRING_ID'];

                $statusData['PERMS'][] = $statusPermData;

            }

            $this->ownerItemDbData[] = $statusData;
        }





    }



}

?>

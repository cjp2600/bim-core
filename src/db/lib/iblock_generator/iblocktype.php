<?php

namespace Bim\Db\Lib;
/**
 * Class IblockType
 * класс для генерацияя кода изменений в типах инфоблоков:
 *
 */
class IblockTypeGen extends \Bim\Db\Lib\CodeGenerator
{


    public function __construct(){
        \CModule::IncludeModule('iblock');
    }

    /**
     * метод для генерации кода добавления нового типа инфоблоков
     * @param $IblockTypeId
     * @return mixed
     * @internal param array $params
     */
    public function generateAddCode( $IblockTypeId )
    {
        $CIblockType = new \CIBlockType();
        $return = array();
        $dbIblockType = $CIblockType->GetByID($IblockTypeId);
        if ($arIblockType = $dbIblockType->GetNext())
        {
            $Iblock = new \CIBlock();
            $dbIblock = $Iblock->GetList(array(), array('TYPE' => $IblockTypeId));
            $check = true;
            while ($arIblock = $dbIblock->GetNext())
            {
                $IblockProperty = new \CIBlockProperty();
                $dbIblockProperty = $IblockProperty->GetList(array(), array('IBLOCK_CODE' => $arIblock['CODE']));
                while ($arIblockProperty = $dbIblockProperty->GetNext())
                {
                    $dbPropertyValues = \CIBlockPropertyEnum::GetList(array(), array("IBLOCK_ID" => $arIblockProperty['IBLOCK_ID'], "CODE" => $arIblockProperty['CODE']));
                    while($arPropertyValues = $dbPropertyValues->Fetch())
                        $arIblockProperty['VALUES'][$arPropertyValues['ID']] = $arPropertyValues;

                    unset($arIblockProperty['ID']);unset($arIblockProperty['~ID']);
                    unset($arIblockProperty['IBLOCK_ID']);unset($arIblockProperty['~IBLOCK_ID']);
                    $arIblockProperty['IBLOCK_CODE'] = $arIblock['CODE'];

                    foreach ($arIblockProperty as $k => $v){
                        if (strstr($k,"~") || is_null($v)){
                            unset($arIblockProperty[$k]);
                        }
                    }
                    if (isset($arIblockProperty['LINK_IBLOCK_ID'])) {
                        $res = \CIBlock::GetByID($arIblockProperty['LINK_IBLOCK_ID']);
                        if($ar_res = $res->GetNext()) {
                            unset($arIblockProperty['LINK_IBLOCK_ID']);
                            $arIblockProperty['LINK_IBLOCK_CODE'] = $ar_res['CODE'];
                        }
                    }
                    $return[] = $this->getMethodContent('Bim\Db\Iblock\IblockPropertyIntegrate', 'Add', array($arIblockProperty));
                }
                foreach ($arIblock as $k => $v){
                    if ( (strstr($k,"~")) || ($k == 'ID') ) {
                        unset($arIblock[$k]);
                    }
                }
                $return[] = $this->getMethodContent('Bim\Db\Iblock\IblockIntegrate', 'Add', array($arIblock));
            }

            foreach ($arIblockType as $k => $v){
                if ( strstr($k,"~") || is_null($v)){
                    unset($arIblockType[$k]);
                }
            }

            //Устанавливаем названия елементов и разделов в типе.
            $rsLang = \CLanguage::GetList($by="lid", $order="desc");
            while ($arLang = $rsLang->Fetch())
            {
                $arTypeLang = \CIblockType::GetByIDLang($IblockTypeId,$arLang['LID']);
                $arIblockType["LANG"][$arLang['LID']] = array(
                    'NAME' => $arTypeLang['NAME'],
                    'SECTION_NAME' => $arTypeLang['SECTION_NAME'],
                    'ELEMENT_NAME' => $arTypeLang['ELEMENT_NAME'],
                );
            }
            $return[] = $this->getMethodContent('Bim\Db\Iblock\IblockTypeIntegrate', 'Add', array($arIblockType));

            $return = array_reverse($return);
            return implode(PHP_EOL, $return);
        } else {
            return false;
        }


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
     * @param array $IblockTypeId
     * @return mixed
     * @internal param array $params
     */
    public function generateDeleteCode( $IblockTypeId )
    {
        return  $this->getMethodContent('Bim\Db\Iblock\IblockTypeIntegrate', 'Delete', array( $IblockTypeId ) );
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
     * @throws \Exception
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

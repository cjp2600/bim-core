<?php

/**
 * Class FormGen
 * класс для генерацияя кода изменений в веб-формах
 *
 * @package Bitrix\Adv_Preset\Form_Generator
 */
class FormGen extends CodeGenerator
{


    public function __construct(){
        \CModule::IncludeModule("form");
    }
    /**
     * метод для генерации кода добавления новой веб-формы
     * @param $params array
     * @return string
     */
    public function generateAddCode( $params ){
        $this->checkParams( $params );

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php'.PHP_EOL.'/*  Добавляем новую веб-форму */'.PHP_EOL.PHP_EOL;
        foreach( $ownerItemDbData as $formData ) {
            unset( $formData['ID'] );
            $addFields = $formData;

            $code = $code . $this->buildCode('FormIntegrate', 'FormSet', array(  $addFields, '' ) ) .PHP_EOL.PHP_EOL;
        }

        return $code;

    }
    /**
     * метод для генерации кода обновления веб-формы
     * @param $params array
     * @return string
     */
    public function generateUpdateCode( $params ){
        $this->checkParams( $params );

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php'.PHP_EOL.'/*  Обновляем веб-форму */'.PHP_EOL.PHP_EOL;
        foreach( $ownerItemDbData as $formData ) {
            unset( $formData['ID'] );
            $updateFields = $formData;

            $code = $code . $this->buildCode('FormIntegrate', 'FormSet', array( $updateFields,  $updateFields['SID'] ) ) .PHP_EOL.PHP_EOL;
        }

        return $code;

    }

    /**
     * метод для генерации кода удаления  веб-формы
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params ){
        $this->checkParams( $params );

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php'.PHP_EOL.'/*  Удаляем веб-форму  */'.PHP_EOL.PHP_EOL;
        foreach( $ownerItemDbData as $formData ) {

            $code = $code . $this->buildCode('FormIntegrate', 'FormDelete', array(  $formData['SID'] ) ) .PHP_EOL.PHP_EOL;
        }

        return $code;

    }




    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     *          formId => (array) массив с id веб-форм
     *   )
     * @return mixed
     */
    public function checkParams( $params ) {


        if ( !isset( $params['formId'] ) || empty( $params['formId'] ) ) {
            throw new \Exception( 'В параметрах не найден formId' );
        }


        $this->ownerItemDbData = array();

        foreach( $params['formId'] as $formId ) {
            
            $formDbRes = \CForm::GetByID( $formId );
            if ( $formDbRes === false || !$formDbRes->SelectedRowsCount() ) {
                throw new \Exception( 'Не найдена форма с id = ' . $formId );    
            }
            $formData = $formDbRes->Fetch();
            if ( empty( $formData['SID'] )  ) {
                throw new \Exception( 'У формы с id = ' . $formId . ' не найден символьный код');
            }
            $templateList = \CForm::GetMailTemplateArray( $formId );
            if ( !empty( $templateList ) ) {
                $tmpTemplateList = $templateList;
                $templateList = array();
                foreach( $tmpTemplateList as $templateId ) {
                    $eventMessageDbRes = \CEventMessage::GetList($by = "id", $order="desc", array('ID' => $templateId ) );
                    if ( $eventMessageDbRes !== false && $eventMessageDbRes->SelectedRowsCount() ) {
                        $eventMessageData = $eventMessageDbRes->Fetch();
                        $templateList[] = $eventMessageData['EVENT_NAME'];
                    }
                }
            }
            $siteList = \CForm::GetSiteArray( $formId );

            $menuList = array();
            $menuDbRes = \CForm::GetMenuList(array('FORM_ID' => $formId ));
            if ( $menuDbRes !== false && $menuDbRes->SelectedRowsCount() ) {
                while( $menuData = $menuDbRes->Fetch() ) {
                    $menuList[ $menuData['LID'] ] = $menuData['MENU'];
                }
            }
            $formData['arSITE'] = $siteList;
            $formData['arMAIL_TEMPLATE'] = $templateList;
            $formData['arMENU'] = $menuList;

            $this->ownerItemDbData[] = $formData;

        }

    }



}

?>

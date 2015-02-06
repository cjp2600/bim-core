<?php

/**
 * Class FormAnswerGen
 * класс для генерацияя кода изменений в ответах вопросов веб-формы
 *
 * @package Bitrix\Adv_Preset\Form_Generator
 */
class FormAnswerGen extends CodeGenerator
{


    public function __construct(){
        \CModule::IncludeModule("form");
    }
    /**
     * метод для генерации кода добавления нового ответа для вопроса веб-формы
     * @param $params array
     * @return string
     */
    public function generateAddCode( $params ){
        $this->checkParams( $params );

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php'.PHP_EOL.'/*  Добавляем ответ вопроса веб-формы */'.PHP_EOL.PHP_EOL;
        foreach( $ownerItemDbData['formAnswers'] as $formAnswerData ) {
            unset( $formAnswerData['ID'] );
            unset( $formAnswerData['FIELD_ID'] );
            unset( $formAnswerData['QUESTION_ID'] );
            $formAnswerData['QUESTION_SID'] = $ownerItemDbData['formQuestion']['SID'];
            $addAnswers = $formAnswerData;

            $code = $code . $this->buildCode('FormAnswerIntegrate', 'FormAnswerSet', array( $addAnswers,  '' ) ) .PHP_EOL.PHP_EOL;
        }

            


        return $code;

    }
    /**
     * метод для генерации кода обновления ответа для вопроса веб-формы
     * @param $params array
     * @return string
     */
    public function generateUpdateCode( $params ){
        $this->checkParams( $params );

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php'.PHP_EOL.'/*  Обновляем ответ вопроса веб-формы */'.PHP_EOL.PHP_EOL;
        foreach( $ownerItemDbData['formAnswers'] as $formAnswerData ) {
            unset( $formAnswerData['ID'] );
            unset( $formAnswerData['FIELD_ID'] );
            unset( $formAnswerData['QUESTION_ID'] );
            $formAnswerData['QUESTION_SID'] = $ownerItemDbData['formQuestion']['SID'];
            $updateAnswers = $formAnswerData;
            $answerFilter = $updateAnswers['MESSAGE'];
            if ( strlen( $updateAnswers['VALUE'] ) ) {
                $answerFilter = $updateAnswers['VALUE'];
            }
            $code = $code . $this->buildCode('FormAnswerIntegrate', 'FormAnswerSet', array( $updateAnswers,  $answerFilter ) ) .PHP_EOL.PHP_EOL;
        }

        return $code;

    }

    /**
     * метод для генерации кода удаления  ответа для вопроса веб-формы
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params ){
        $this->checkParams( $params );

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php'.PHP_EOL.'/*  Удаляем ответ вопроса веб-формы  */'.PHP_EOL.PHP_EOL;
        foreach( $ownerItemDbData['formAnswers'] as $formAnswerData ) {
            unset( $formAnswerData['ID'] );
            $questionSID = $ownerItemDbData['formQuestion']['SID'];

            $answerFilter = $formAnswerData['MESSAGE'];
            if ( strlen( $formAnswerData['VALUE'] ) ) {
                $answerFilter = $formAnswerData['VALUE'];
            }
            $code = $code . $this->buildCode('FormAnswerIntegrate', 'FormAnswerReset', array( $questionSID,  $answerFilter ) ) .PHP_EOL.PHP_EOL;
        }

        return $code;

    }




    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     *          formQuestionId =>  id вопроса веб-формы
     *          formAnswerId => (array) массив id вопросов веб-формы
     *   )
     * @return mixed
     */
    public function checkParams( $params  ) {


        if ( !isset( $params['formQuestionId'] ) || !(int) $params['formQuestionId']  ) {
            throw new \Exception( 'В параметрах не найден formQuestionId' );
        }

        if ( !isset( $params['formAnswerId'] ) || empty( $params['formAnswerId'] )  ) {
            throw new \Exception( 'В параметрах не найден formAnswerId' );
        }

        $this->ownerItemDbData = array();

        $formQuestionDbRes = \CFormField::GetByID( $params['formQuestionId'] );
        if ( $formQuestionDbRes === false || !$formQuestionDbRes->SelectedRowsCount() ) {
            throw new \Exception( 'Не найден вопрос с id = ' . $params['formQuestionId'] );
        }
        $formQuestion = $formQuestionDbRes->Fetch();
        if ( empty( $formQuestion['SID'] )  ) {
            throw new \Exception( 'У вопроса с id = ' . $params['formAnswerId'] . ' не найден символьный код');
        }

        $this->ownerItemDbData['formQuestion'] = $formQuestion;

        foreach( $params['formAnswerId'] as $formAnswerId ) {
            
            $formAnswerDbRes = \CFormAnswer::GetByID( $formAnswerId );
            if ( $formAnswerDbRes === false || !$formAnswerDbRes->SelectedRowsCount() ) {
                throw new \Exception( 'Не найдено поле с id = ' . $formAnswerId );
            }
            $formAnswerData = $formAnswerDbRes->Fetch();

            $this->ownerItemDbData['formAnswers'][] = $formAnswerData;

        }

    }



}

?>

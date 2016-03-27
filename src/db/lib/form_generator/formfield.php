<?php

/**
 * Class FormFieldGen
 * класс для генерацияя кода изменений в полях веб-формы
 *
 * @package Bitrix\Adv_Preset\Form_Generator
 */
class FormFieldGen extends CodeGenerator
{


    public function __construct()
    {
        \CModule::IncludeModule("form");
    }

    /**
     * метод для генерации кода добавления нового поля веб-формы
     * @param $params array
     * @return string
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php' . PHP_EOL . '/*  Добавляем поле веб-формы */' . PHP_EOL . PHP_EOL;
        foreach ($ownerItemDbData['formFields'] as $formFieldData) {
            unset($formFieldData['ID']);
            $addFields = $formFieldData;

            $code = $code . $this->buildCode('FormFieldIntegrate', 'FormFieldSet',
                    array($ownerItemDbData['formData']['SID'], $addFields)) . PHP_EOL . PHP_EOL;
        }

        return $code;

    }

    /**
     * метод для генерации кода обновления поля веб-формы
     * @param $params array
     * @return string
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php' . PHP_EOL . '/*  Обновляем поле веб-формы */' . PHP_EOL . PHP_EOL;
        foreach ($ownerItemDbData['formFields'] as $formFieldData) {
            unset($formFieldData['ID']);
            $updateFields = $formFieldData;

            $code = $code . $this->buildCode('FormFieldIntegrate', 'FormFieldSet',
                    array($ownerItemDbData['formData']['SID'], $updateFields)) . PHP_EOL . PHP_EOL;
        }

        return $code;

    }

    /**
     * метод для генерации кода удаления  поля веб-формы
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php' . PHP_EOL . '/*  Удаляем поле веб-формы  */' . PHP_EOL . PHP_EOL;
        foreach ($ownerItemDbData['formFields'] as $formFieldData) {

            $code = $code . $this->buildCode('FormFieldIntegrate', 'FormFieldDelete',
                    array($formFieldData['SID'])) . PHP_EOL . PHP_EOL;
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     *          formId =>  id веб-формы
     *          formFieldId => (array) массив id полей веб-формы
     *   )
     * @return mixed
     */
    public function checkParams($params)
    {


        if (!isset($params['formId']) || !(int)$params['formId']) {
            throw new \Exception('В параметрах не найден formId');
        }

        if (!isset($params['formFieldId']) || empty($params['formFieldId'])) {
            throw new \Exception('В параметрах не найден formFieldId');
        }

        $this->ownerItemDbData = array();

        $formDbRes = \CForm::GetByID($params['formId']);
        if ($formDbRes === false || !$formDbRes->SelectedRowsCount()) {
            throw new \Exception('Не найдена форма с id = ' . $params['formId']);
        }
        $formData = $formDbRes->Fetch();
        if (empty($formData['SID'])) {
            throw new \Exception('У веб-формы с id = ' . $params['formId'] . ' не найден символьный код');
        }

        $this->ownerItemDbData['formData'] = $formData;

        foreach ($params['formFieldId'] as $formFieldId) {

            $formFieldDbRes = \CFormField::GetByID($formFieldId);
            if ($formFieldDbRes === false || !$formFieldDbRes->SelectedRowsCount()) {
                throw new \Exception('Не найдено поле с id = ' . $formFieldId);
            }
            $formFieldData = $formFieldDbRes->Fetch();
            if (empty($formFieldData['SID'])) {
                throw new \Exception('У поля с id = ' . $formFieldId . ' не найден символьный код');
            }
            $answerList = array();
            $formAnswerDbRes = \CFormAnswer::GetList($formFieldData['ID'], $by = "s_id", $order = "desc", array(),
                $filter = false);
            while ($formAnswerData = $formAnswerDbRes->Fetch()) {
                unset($formAnswerData['ID']);
                unset($formAnswerData['FIELD_ID']);
                $answerList[] = $formAnswerData;
            }
            if (!empty($answerList)) {
                $formFieldData['arANSWER'] = $answerList;
            }


            $this->ownerItemDbData['formFields'][] = $formFieldData;

        }

    }


}

?>

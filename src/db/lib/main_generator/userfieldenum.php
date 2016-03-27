<?php

/**
 * Class UserFieldEnumGen
 * класс для генерацияя кода изменений значений списка пользователького поля типа "список"
 *
 * @package Bitrix\Adv_Preset\Main_Generator
 */
class UserFieldEnumGen extends CodeGenerator
{


    public function __construct()
    {
    }

    /**
     * метод для генерации кода добавления нового значения
     * @param $params array
     * @return string
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $userFieldData = $this->ownerItemDbData['userFieldData'];
        $enumRowList = $this->ownerItemDbData['enumRowList'];

        $code = '<?php' . PHP_EOL . '/*  Добавляем значение пользовательского поля "список" */' . PHP_EOL . PHP_EOL;
        foreach ($enumRowList as $enumRow) {
            $addFields = $enumRow;
            unset($addFields['ID']);
            unset($addFields['USER_FIELD_ID']);


            $code = $code . $this->buildCode('UserFieldEnumIntegrate', 'SetEnumValues', array(
                    $userFieldData['ENTITY_ID'],
                    $userFieldData['FIELD_NAME'],
                    array('n' => $addFields)
                )) . PHP_EOL . PHP_EOL;

        }

        return $code;

    }

    /**
     * метод для генерации кода обновления значения
     * @param $params array
     * @return string
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $userFieldData = $this->ownerItemDbData['userFieldData'];
        $enumRowList = $this->ownerItemDbData['enumRowList'];
        $code = '<?php' . PHP_EOL . '/*  Обновляем значение пользовательского поля "список" */' . PHP_EOL . PHP_EOL;
        foreach ($enumRowList as $enumRow) {
            $updateFields = $enumRow;
            unset($updateFields['ID']);
            unset($updateFields['USER_FIELD_ID']);
            $code = $code . $this->buildCode('UserFieldEnumIntegrate', 'UpdateEnumValues', array(
                    $userFieldData['ENTITY_ID'],
                    $userFieldData['FIELD_NAME'],
                    $enumRow['XML_ID'],
                    $updateFields
                )) . PHP_EOL . PHP_EOL;

        }

        return $code;

    }

    /**
     * метод для генерации кода удаления значения
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $userFieldData = $this->ownerItemDbData['userFieldData'];
        $enumRowList = $this->ownerItemDbData['enumRowList'];
        $code = '<?php' . PHP_EOL . '/*  Удаляем значение пользовательского поля "список" */' . PHP_EOL . PHP_EOL;
        foreach ($enumRowList as $enumRow) {
            $updateFields = $enumRow;
            unset($updateFields['ID']);
            unset($updateFields['USER_FIELD_ID']);
            $updateFields['DEL'] = 'Y';
            $code = $code . $this->buildCode('UserFieldEnumIntegrate', 'UpdateEnumValues', array(
                    $userFieldData['ENTITY_ID'],
                    $userFieldData['FIELD_NAME'],
                    $enumRow['XML_ID'],
                    $updateFields
                )) . PHP_EOL . PHP_EOL;

        }

        return $code;

    }

    /**
     * получаем список значения пользовательского поля типа "список"
     */
    private function _getUserFieldEnumList($userFieldId)
    {
        $result = array();
        $userFieldEnumDbRes = \CUserFieldEnum::GetList(array('SORT' => 'ASC'), array('USER_FIELD_ID' => $userFieldId));
        if ($userFieldEnumDbRes !== false && $userFieldEnumDbRes->SelectedRowsCount()) {
            while ($userFieldEnumRow = $userFieldEnumDbRes->Fetch()) {
                unset($userFieldEnumRow['ID']);
                $result[] = $userFieldEnumRow;
            }
        }
        return $result;

    }

    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     *          userFieldId =>  id пользовательского поля
     *          enumRowId => array() массив Id значений пользовательского поля
     *   )
     * @return mixed
     */
    public function checkParams($params)
    {


        if (!isset($params['userFieldId']) || empty($params['userFieldId'])) {
            throw new \Exception('В параметрах не найден userFieldId');
        }

        if (!isset($params['enumRowId']) || empty($params['enumRowId'])) {
            throw new \Exception('В параметрах не найден enumRowId');
        }

        $this->ownerItemDbData = array();

        $userFieldData = \CUserTypeEntity::GetByID($params['userFieldId']);
        if ($userFieldData === false || empty($userFieldData)) {
            throw new \Exception('Не найдено свойство с id = ' . $params['userFieldId']);
        }
        $this->ownerItemDbData['userFieldData'] = $userFieldData;

        foreach ($params['enumRowId'] as $enumRowId) {

            $userFieldEnumDbRes = \CUserFieldEnum::GetList(array('SORT' => 'ASC'),
                array('USER_FIELD_ID' => $params['userFieldId'], 'ID' => $enumRowId));
            if ($userFieldEnumDbRes === false || !$userFieldEnumDbRes->SelectedRowsCount()) {
                throw new \Exception('Не найдены данные значения списка с id = ' . $enumRowId);
            }

            $userFieldEnumRow = $userFieldEnumDbRes->Fetch();

            $this->ownerItemDbData['enumRowList'][] = $userFieldEnumRow;

        }

    }


}

?>

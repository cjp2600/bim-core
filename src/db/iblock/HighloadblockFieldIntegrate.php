<?php

namespace Bim\Db\Iblock;

\CModule::IncludeModule("highloadblock");
use Bitrix\Highloadblock as HL;

/*
* класс для работы с  полями сущностей highload инфоблока
*/
class HighloadblockFieldIntegrate {


	/*
	* Add() добавляет новое поле
	* @param string $entityName название сущности highload инфоблока
	* @param array $fields Массив значений полей
	    FIELD_NAME - Название поля;
	    SORT - Значение сортировки;
	    USER_TYPE_ID
	    XML_ID
	    MULTIPLE - Множественность свойства;
	    MANDATORY
	    SHOW_FILTER
	    SHOW_IN_LIST
	    EDIT_IN_LIST
	    IS_SEARCHABLE
	* Summary:
	3 required
	* return array - массив массив с флагом успешности добаления или с текстом возникшей в процессе ошибки
	*/
    function Add($entityName, $fields)
    {
        if (empty($entityName) || empty($fields)) {
            throw new \Exception('entityName or fields is empty');
        }
        if (empty($fields['FIELD_NAME'])) {
            throw new \Exception('Field FIELD_NAME is required.');
        }
        if (empty($fields['USER_TYPE_ID'])) {
            throw new \Exception('Field USER_TYPE_ID is required.');
        }
        if (isset($fields['ID'])) {
            unset($fields['ID']);
        }
        $userFieldEntity = self::_getEntityId($entityName);
        $fields['ENTITY_ID'] = $userFieldEntity;

        $typeEntityDbRes = \CUserTypeEntity::GetList(array(), array(
            "ENTITY_ID" => $fields["ENTITY_ID"],
            "FIELD_NAME" => $fields["FIELD_NAME"],
        ));
        if ($typeEntityDbRes !== false && $typeEntityDbRes->SelectedRowsCount()) {
            throw new \Exception('Hlblock field with name = "' . $fields["FIELD_NAME"] . '" already exist.');
        }
        $UserType = new \CUserTypeEntity;
        $ID = $UserType->Add($fields);
        if (!(int)$ID) {
            throw new \Exception('Not added Hlblock field');
        }
        return $ID;
    }


	/*
	* Update() - обновляет поле сущности highload инфоблока
	* @param string entityName - название сущности highload инфоблока - req
	* @param string fieldName - Название поля req
	* @param array fields - массив изменяемых параметров с ключами:
	* 	SORT - порядок сортировки
	*   MANDATORY - признак обязательности ввода значения Y/N
	*	SHOW_FILTER - признак показа в фильтре списка Y/N
	*	SHOW_IN_LIST - признак показа в списке Y/N
	*	EDIT_IN_LIST - разрешать редактирование поля в формах админки или нет Y/N
	*	IS_SEARCHABLE - признак поиска Y/N
	*	SETTINGS - массив с настройками свойства зависимыми от типа свойства. Проходят "очистку" через обработчик типа PrepareSettings.
	*	EDIT_FORM_LABEL - массив языковых сообщений вида array("ru"=>"привет", "en"=>"hello")
	*	LIST_COLUMN_LABEL
	*	LIST_FILTER_LABEL
	*	ERROR_MESSAGE
	*	HELP_MESSAGE
	* Summary:
	2 required
	* return array - массив массив с флагом успешности изменения или с текстом возникшей в процессе ошибки
	*/
    function Update($entityName, $fieldName, $fields)
    {
        if (empty($entityName)) {
            throw new \Exception('entityName is required');
        }

        if (empty($fieldName)) {
            throw new \Exception('fieldName is required.');
        }

        if (isset($fields['ID'])) {
            unset($fields['ID']);
        }
        $userFieldEntity = self::_getEntityId($entityName);
        $fields['ENTITY_ID'] = $userFieldEntity;

        $typeEntityDbRes = CUserTypeEntity::GetList(array(), array(
            "ENTITY_ID" => $userFieldEntity,
            "FIELD_NAME" => $fieldName,
        ));
        if ($typeEntityDbRes === false || !$typeEntityDbRes->SelectedRowsCount()) {
            throw new \Exception('Hlblock field with name = "' . $fieldName . '" not found.');
        }
        $hlBlockFieldData = $typeEntityDbRes->Fetch();
        $userType = new CUserTypeEntity;
        if (!$userType->Update($hlBlockFieldData['ID'], $fields)) {
            throw new \Exception('Not update Hlblock field');
        }
        return $hlBlockFieldData['ID'];
    }


    /**
     * Функция удаляет поле сущности highload инфоблока
     * @param $entityName
     * @param $fieldName
     * @return array
     * @throws \Exception
     * @internal param bool $isRevert
     * @internal param entityName $string - название сущности highload инфоблока - req
     * @internal param fieldName $string - Название поля req
     * Summary:
     * 2 required
     * return array - массив массив с флагом успешности удаления или с текстом возникшей в процессе ошибки
     */
	function Delete( $entityName, $fieldName)
    {
        if ( empty( $entityName ) ) {
            throw new \Exception('entityName is required');
        }

        if ( empty( $fieldName ) ) {
            throw new \Exception('fieldName is required.');
        }

        $filter = array('NAME' => $entityName);
        $hlBlockDbRes = HL\HighloadBlockTable::getList(array(
            "filter" => $filter
        ));
        if ( !$hlBlockDbRes->getSelectedRowsCount() ) {
            throw new \Exception('Not found highloadBlock with entityName = ' . $entityName );
        }
        $hlBlockRow = $hlBlockDbRes->fetch();

        $entity = HL\HighloadBlockTable::compileEntity($hlBlockRow);
        $entityDataClass = $entity->getDataClass();

        $obList = $entityDataClass::getList();
        if ( $obList->getSelectedRowsCount() > 0) {
            throw new \Exception('Unable to remove a highloadBlock['.$entityName.'], because it has elements');
        }

        $userFieldEntity = self::_getEntityId( $entityName );
        $typeEntityDbRes = \CUserTypeEntity::GetList(array(), array(
            "ENTITY_ID" => $userFieldEntity,
            "FIELD_NAME" => $fieldName,
        ));
        if ($typeEntityDbRes->SelectedRowsCount() > 0 ) {
            $hlBlockFieldData = $typeEntityDbRes->Fetch();
            $userType = new \CUserTypeEntity;
            if (!$userType->Delete($hlBlockFieldData['ID'])) {
                throw new \Exception('Not delete Hlblock field');
            }
            return $hlBlockFieldData['ID'];
        }
	}

    /**
     * @param $entityName - название сущности highload инфоблока - req
     * @return string
     * @throws \Exception
     */
    static public function _getEntityId( $entityName )
    {
        if ( !strlen( $entityName ) ) {
            return false;
        }
        $filter = array('NAME' => $entityName);
        $hlBlockDbRes = HL\HighloadBlockTable::getList(array(
            "filter" => $filter
        ));
        if ( !$hlBlockDbRes->getSelectedRowsCount() ) {
            throw new \Exception('Not found highloadBlock with entityName = "' . $entityName .'"');
        }
        $hlBlockRow = $hlBlockDbRes->fetch();

        $userFieldEntity = sprintf('HLBLOCK_%s', $hlBlockRow['ID'] );

        return $userFieldEntity;
    }

}
?>
<?php

namespace Bim\Db\Iblock;

/*
 * класс взаимодействия с типами инфоблоков
 */
class IblockTypeIntegrate
{
    /*
     * Add() - метод добавляет тип инфоблока
     * @param array $arFields - набор параметров добавляемого типа инфоблока:
     * @param string $arFields['ID'] - Идентификатор (ID) - no default/required
     * @param string/boolean ('Y'/'N') $arFields['SECTIONS'] - Использовать ли древовидный классификатор элементов по разделам - default 'Y'
     * @param string $arFields['EDIT_FILE_BEFORE'] - Файл для редактирования элемента, позволяющий модифицировать поля перед сохранением - default ''
     * @param string $arFields['EDIT_FILE_AFTER'] - Файл с формой редактирования элемента - default ''
     * @param string/boolean $arFields['IN_RSS'] - Использовать экспорт в RSS - default 'N'
     * @param int $arFields['SORT'] - Сортировка - default 500
     * @param array $arFields['LANG'] - Языкозависимые названия и заголовки объектов - default (либо один из двух языков, если на сайте присутствует только один):
     *  array (
     *      'ru' => array (
     *          'NAME' => 'Название',
     *          'SECTION_NAME' => 'Разделы',
     *          'ELEMENT_NAME' => 'Элементы',
     *      ),
     *      'en' => array (
     *          'NAME' => 'Title',
     *          'SECTION_NAME' => 'Sections',
     *          'ELEMENT_NAME' => 'Elements',
     *      ),
     *  );
     *
     * Summary:
     * 1 required
     * 5 optional with defaults
     *
     * return array - массив с идентификатором добавленного типа инфоблоков или с текстом возникшей в процессе добавления ошибки
     */
    public function Add($arFields,$isRevert = false)
    {
        if (!isset($arFields['SECTIONS']) || empty($arFields['SECTIONS']))
            $arFields['SECTIONS'] = 'Y';

        if (!isset($arFields['IN_RSS']) || empty($arFields['IN_RSS']))
            $arFields['IN_RSS'] = 'N';

        if (isset($arFields['SORT']))
        {
            if (!is_int($arFields['SORT']))
            {
                if (intval($arFields['SORT']))
                    $arFields['SORT'] = intval($arFields['SORT']);
                else
                    $arFields['SORT'] = 500;
            }
        }
        else
            $arFields['SORT'] = 500;

        if(!isset($arFields['LANG']) || empty($arFields['LANG']))
        {
            $langDefaults = array(
                'ru' => array(
                    'NAME'          => 'Название',
                    'SECTION_NAME'  => 'Разделы',
                    'ELEMENT_NAME'  => 'Элементы',
                ),
                'en' => array(
                    'NAME'          => 'Common',
                    'SECTION_NAME'  => 'Sections',
                    'ELEMENT_NAME'  => 'Elements',
                ),
            );

            $l = CLanguage::GetList($lby="sort", $lorder="asc");
            while($arIBTLang = $l->GetNext())
            {
                if (array_key_exists($arIBTLang["LID"], $langDefaults))
                    $arFields["LANG"][$arIBTLang["LID"]] = $langDefaults[$arIBTLang["LID"]];
            }
        }

        $CIblockType = new \CIBlockType();
        if ($CIblockType->Add($arFields))
        {
            return true;
        }
        else {
            throw new \Exception($CIblockType->LAST_ERROR);
            return false;
        }
    }

    /*
     * Update() - метод изменяет тип инфоблока
     * @param string $IblockTypeCode - символьный идентификатор изменяемого типа инфоблока - no defaults/required
     * @param array $arFields - набор параметров изменяемого типа инфоблока:
     * @param string/boolean ('Y'/'N') $arFields['SECTIONS'] - Использовать ли древовидный классификатор элементов по разделам - default 'Y'
     * @param string $arFields['EDIT_FILE_BEFORE'] - Файл для редактирования элемента, позволяющий модифицировать поля перед сохранением - default ''
     * @param string $arFields['EDIT_FILE_AFTER'] - Файл с формой редактирования элемента - default ''
     * @param string/boolean $arFields['IN_RSS'] - Использовать экспорт в RSS - default 'N'
     * @param int $arFields['SORT'] - Сортировка - default 500
     * @param array $arFields['LANG'] - Языкозависимые названия и заголовки объектов - default (либо один из двух языков, если на сайте присутствует только один):
     *  array (
     *      'ru' => array (
     *          'NAME' => 'Название',
     *          'SECTION_NAME' => 'Разделы',
     *          'ELEMENT_NAME' => 'Элементы',
     *      ),
     *      'en' => array (
     *          'NAME' => 'Title',
     *          'SECTION_NAME' => 'Sections',
     *          'ELEMENT_NAME' => 'Elements',
     *      ),
     *  );
     *
     * Summary:
     * 1 required
     * 5 optional with defaults
     *
     * return array - массив с флагом успешности изменения типа инфоблоков или с текстом возникшей в процессе ошибки
     */
    public function Update($IblockTypeCode, $arFields, $isRevert = false)
    {
        global $RESPONSE;
/*
        if (!isset($arFields['SECTIONS']) || empty($arFields['SECTIONS']))
            $arFields['SECTIONS'] = 'Y';

        if (!isset($arFields['IN_RSS']) || empty($arFields['IN_RSS']))
            $arFields['IN_RSS'] = 'N';

        if (isset($arFields['SORT']))
        {
            if (!is_int($arFields['SORT']))
            {
                if (intval($arFields['SORT']))
                    $arFields['SORT'] = intval($arFields['SORT']);
                else
                    $arFields['SORT'] = 500;
            }
        }
        else
            $arFields['SORT'] = 500;

        if(!isset($arFields['LANG']) || empty($arFields['LANG']))
        {
            $langDefaults = array(
                'ru' => array(
                    'NAME'          => 'Название',
                    'SECTION_NAME'  => 'Разделы',
                    'ELEMENT_NAME'  => 'Элементы',
                ),
                'en' => array(
                    'NAME'          => 'Title',
                    'SECTION_NAME'  => 'Sections',
                    'ELEMENT_NAME'  => 'Elements',
                ),
            );

            $l = CLanguage::GetList($lby="sort", $lorder="asc");
            while($arIBTLang = $l->GetNext())
            {
                if (array_key_exists($arIBTLang["LID"], $langDefaults))
                    $arFields["LANG"][$arIBTLang["LID"]] = $langDefaults[$arIBTLang["LID"]];
            }
        }
*/
        $CIblockType = new CIBlockType();
        $dbIblockType = $CIblockType->GetByID($IblockTypeCode);
        if ($arIblocKType = $dbIblockType->Fetch())
        {
            $NewArFields = array_merge($arIblocKType, $arFields);

            foreach ($NewArFields as $fieldKey => $field)
                if ($field === null)
                    $NewArFields[$fieldKey] = false;

            if (!$isRevert) {

                $IblockTypeRevert = new IblockTypeRevertIntegrate();
                if ($IblockTypeRevert->Update($IblockTypeCode))
                {
                    $res = $CIblockType->Update($IblockTypeCode, $NewArFields);
                    if ($res)
                        return $RESPONSE[] = array('type' => 'success');
                    else
                        return $RESPONSE[] = array('type' => 'error', 'error_text' => $CIblockType->LAST_ERROR);
                }
                else {
                    return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Cant complete "iblock type update revert" operation');
                }

            } else {

                $res = $CIblockType->Update($IblockTypeCode, $NewArFields);
                if ($res)
                    return $RESPONSE[] = array('type' => 'success');
                else
                    return $RESPONSE[] = array('type' => 'error', 'error_text' => $CIblockType->LAST_ERROR);

            }
        }
        else
            return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Not find iblock type with code '.$IblockTypeCode);
    }

    /*
     * Delete() - метод удаляет тип инфоблока
     * @param string $IblockTypeCode - символьный идентификатор изменяемого типа инфоблока - no defaults/required
     */
    public function Delete($IblockTypeCode,$isRevert = false)
    {
        global $RESPONSE;
        $IblockTypeRevert = new IblockTypeRevertIntegrate();

//        $resIblock = CIBlock::GetList(array(),array('TYPE'=>$IblockTypeCode),true);
//        if ($arIblocks = $resIblock->Fetch()) {
//            return $RESPONSE[] = array('type' => 'error', 'error_text' => $IblockTypeCode.' type is not empty!');
//        }

        $Iblock = new CIBlock();
        $dbIblock = $Iblock->GetList(array(), array('TYPE' => $IblockTypeCode));
        while( $dbRow = $dbIblock->Fetch() ) {
            $iblockElDbRes = CIBlockElement::GetList( array(), array('IBLOCK_ID' => $dbRow['ID'] ) );
            if ( $iblockElDbRes !== false && $iblockElDbRes->SelectedRowsCount() ) {
                return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Can not delete iblock type: iblock id =' . $dbRow['ID'] . ' have elements');
            }
        }
        if (!$isRevert) {

            if ($IblockTypeRevert->Add($IblockTypeCode))
            {

                if(!CIBlockType::Delete($IblockTypeCode))
                    return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Delete iblock type error!');

                return $RESPONSE[] = array('type' => 'success');
            }
            else
                return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Cant complete "iblock type delete revert" operation');

        } else {

            if(!CIBlockType::Delete($IblockTypeCode))
                return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Delete iblock type error!');

            return $RESPONSE[] = array('type' => 'success');
        }

    }
}
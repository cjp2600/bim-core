<?php

namespace Bim\Db\Iblock;

\CModule::IncludeModule("iblock");

/**
 * Класс для работы с Инфоблоками.
 *
 * Class IblockIntegrate
 *
 * Documentation: http://cjp2600.github.io/bim-core/
 * @package Bim\Db\Iblock
 */
class IblockIntegrate
{
    /**
     * Метод создания инфоблока.
     *
     * @param $input
     * @param bool $isGenerated
     * @return bool
     * @throws \Exception
     */
    public static function Add($input,$isGenerated = true)
    {
        if (isset($input['SORT'])) {
            if (!is_int($input['SORT'])) {
                if (intval($input['SORT'])) {
                    $input['SORT'] = intval($input['SORT']);
                } else {
                    $input['SORT'] = 500;
                }
            }
        } else {
            $input['SORT'] = 500;
        }
        # default values
        $defaultValue = array(
            'ACTIVE' => 'Y',
            'LIST_PAGE_URL' => '#SITE_DIR#/' . $input['IBLOCK_TYPE_ID'] . '/index.php?ID=#IBLOCK_ID#',
            'SECTION_PAGE_URL' => '#SITE_DIR#/' . $input['IBLOCK_TYPE_ID'] . '/list.php?SECTION_ID=#ID#',
            'DETAIL_PAGE_URL' => '#SITE_DIR#/' . $input['IBLOCK_TYPE_ID'] . '/detail.php?ID=#ID#',
            'INDEX_SECTION' => 'Y',
            'INDEX_ELEMENT' => 'Y',
            'PICTURE' => array(
                'del' => null,
                'MODULE_ID' => 'iblock',
            ),
            'DESCRIPTION' => '',
            'DESCRIPTION_TYPE' => 'text',
            'EDIT_FILE_BEFORE' => '',
            'EDIT_FILE_AFTER' => '',
            'WORKFLOW' => 'N',
            'BIZPROC' => 'N',
            'SECTION_CHOOSER' => 'L',
            'LIST_MODE' => '',
            'FIELDS' => array(),
            'ELEMENTS_NAME' => 'Элементы',
            'ELEMENT_NAME' => 'Элемент',
            'ELEMENT_ADD' => 'Добавить элемент',
            'ELEMENT_EDIT' => 'Изменить элемент',
            'ELEMENT_DELETE' => 'Удалить элемент',
            'SECTIONS_NAME' => 'Разделы',
            'SECTION_NAME' => 'Раздел',
            'SECTION_ADD' => 'Добавить раздел',
            'SECTION_EDIT' => 'Изменить раздел',
            'SECTION_DELETE' => 'Удалить раздел',
            'RIGHTS_MODE' => 'S',
            'GROUP_ID' => array(
                2 => 'R',
                1 => 'X'
            ),
            'VERSION' => 1
        );
        if (!strlen($input['CODE'])) {
            throw new \Exception('Not found iblock code');
        }
        $iblockDbRes = \CIBlock::GetList(array(), array('CODE' => $input['CODE'], 'CHECK_PERMISSIONS' => 'N'));
        if ($iblockDbRes !== false && $iblockDbRes->SelectedRowsCount()) {
            throw new \Exception('Iblock with code = "' . $input['CODE'] . '" already exist.');
        }
        foreach ($defaultValue as $defaultName => $defaultValue) {
            if (!isset($input[$defaultName]) || empty($input[$defaultName])) {
                $input[$defaultName] = $defaultValue;
            }
        }

        // Перегоняем имена групп (если были изменены при накатывании миграции) в идентификаторы групп
        $arGroups = BitrixUserGroupsHelper::getUserGroups();
        foreach($input['GROUP_ID'] as $groupCode => $right) {
            $groupId = BitrixUserGroupsHelper::getUserGroupId($groupCode, $arGroups);
            if($groupId != null && strlen($groupId) > 0) {
                $input['GROUP_ID'][$groupId] = $input['GROUP_ID'][$groupCode];
                unset($input['GROUP_ID'][$groupCode]);
            }
        }

        $iBlock = new \CIBlock();
        $ID = $iBlock->Add($input);
        if ($ID) {
            return $ID;
        } else {
            throw new \Exception($iBlock->LAST_ERROR);
        }
        return false;
    }

    /**
     * Метод удаления информационного блока.
     *
     * @param $IblockCode
     * @param bool $isGenerated
     * @return bool
     * @throws \Exception
     */
    public static function Delete($IblockCode,$isGenerated = true)
    {
        $dbIblock = \CIBlock::GetList(array(), array('CODE' => $IblockCode, 'CHECK_PERMISSIONS' => 'N'));
        if ($item = $dbIblock->Fetch()) {
            $iblockElDbRes = \CIBlockElement::GetList(array(), array('IBLOCK_ID' => $item['ID']));
            if ($iblockElDbRes !== false && $iblockElDbRes->SelectedRowsCount()) {
                throw new \Exception('Can not delete iblock id = ' . $item['ID'] . ' have elements');
            }
            if (\CIBlock::Delete($item['ID'])) {
                return true;
            } else {
                throw new \Exception('Iblock delete error!');
            }
        } else {
            throw new \Exception('Not find iblock with code ' . $IblockCode);
        }
        return false;
    }

}
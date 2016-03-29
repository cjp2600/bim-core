<?php
namespace Bim\Util;

/**
 * Вспомогательные методы
 *
 * Class Helper
 * @package Bim\Util
 */
class Helper
{
    /**
     * Получение id группы по имени
     *
     * @param string $groupName имя группы
     * @param array $groups результат вызова UserGroupsHelper::getUserGroups()
     * @return string идентификатор группы
     */
    public static function getUserGroupId($groupName, $groups)
    {
        foreach ($groups as $group) {
            if ($groupName == $group['STRING_ID']) {
                return $group['ID'];
            }
        }
        return null;
    }

    /**
     * Получение кода группы по id
     *
     * @param string $groupId : идентификатор группы
     * @param array $groups : результат вызова UserGroupsHelper::getUserGroups()
     * @return string имя группы
     */
    public static function getUserGroupCode($groupId, $groups)
    {
        foreach ($groups as $group) {
            if ($groupId == $group['ID']) {
                return $group['STRING_ID'];
            }
        }
        return null;
    }

    /**
     * Получение массива групп пользователей
     *
     * @return array массив групп пользователей
     */
    public static function getUserGroups()
    {
        $group = new \CGroup();
        $groupOrder = array('sort' => 'asc');
        $groupTmp = 'sort';
        $groupQuery = $group->GetList($groupOrder, $groupTmp);
        $groups = array();
        for ($i = 0; $item = $groupQuery->Fetch(); $i++) {
            $groups[$i] = $item;
        }
        return $groups;
    }

    /**
     * Удаление полей массива
     *
     * @param $needle
     * @param $data : Формируемый массив
     */
    public static function unsetFields($needle, &$data)
    {
        if (!is_array($needle)) {
            array($needle);
        }

        foreach ($needle as $item) {
            unset($data[$item]);
        }
    }
}
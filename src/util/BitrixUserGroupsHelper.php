<?php
namespace Bim\Util;

/**
 * Помогает извлекать идентификаторы групп по именам групп и наоборот
 */
class BitrixUserGroupsHelper {
    /**
     * @param string $sGroupName имя группы
     * @param array $arGroups результат вызова UserGroupsHelper::getUserGroups()
     * @return string идентификатор группы
     */
    static public function getUserGroupId($sGroupName, $arGroups){
        foreach($arGroups as $group){
            if($sGroupName == $group['STRING_ID']){
                return $group['ID'];
            }
        }
        return null;
    }

    /**
     * @param string $sGroupId идентификатор группы
     * @param array $arGroups результат вызова UserGroupsHelper::getUserGroups()
     * @return string имя группы
     */
    static public function getUserGroupCode($sGroupId, $arGroups){
        foreach($arGroups as $group){
            if($sGroupId == $group['ID']){
                return $group['STRING_ID'];
            }
        }
        return null;
    }

    /**
     * @return array массив групп пользователей
     */
    static public function getUserGroups(){
        $aGroupOrder = array('sort' => 'asc');
        $sGroupTmp = 'sort';
        $rsGroup = \CGroup::GetList($aGroupOrder, $sGroupTmp);
        $arGroups = array();
        for($i = 0 ; $aGroup = $rsGroup->Fetch() ; $i++) {
            $arGroups[$i] = $aGroup;
        }
        return $arGroups;
    }
}
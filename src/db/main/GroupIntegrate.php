<?php

namespace Bim\Db\Main;
\CModule::IncludeModule("main");

/**
 * Class GroupIntegrate
 *
 * Documentation: http://cjp2600.github.io/bim-core/
 * @package Bim\Db\Main
 */
class GroupIntegrate
{

    /**
     * Add
     * @param $fields
     * @return array
     * @throws \Exception
     * @internal param bool $isRevert
     */
	 public function Add($fields)
     {
         unset($fields['ID']);
         if (empty($fields['STRING_ID'])) {
             return array('type' => 'error', 'error_text' => 'Field STRING_ID is required.');
         }
         if (!isset($fields['ACTIVE']) || empty($fields['ACTIVE'])) {
             $fields['ACTIVE'] = "N";
         }
         if (!isset($fields['C_SORT']) || empty($fields['C_SORT'])) {
             $fields['C_SORT'] = 100;
         }
         $groupDbRes = \CGroup::GetList($by = 'sort', $sort = 'asc', array('STRING_ID' => $fields['STRING_ID']));
         if ($groupDbRes !== false && $groupDbRes->SelectedRowsCount()) {
             throw new \Exception('Group with STRING_ID = "' . $fields['STRING_ID'] . '" already exist.');
         }
         $group = new \CGroup;
         $ID = $group->Add($fields);
         if ($ID) {
             return $ID;
         } else {
             throw new \Exception($group->LAST_ERROR);
         }
	 }


    /**
     * Update
     * @param $CODE
     * @param $fields
     * @return bool
     */
	public function Update($CODE, $fields)
    {
        return true;
	}


    /**
     * Delete
     * @param $CODE
     * @return array
     * @throws \Exception
     * @internal param bool $isRevert
     */
    public function Delete($CODE)
    {
        $group = new \CGroup;
        if (!empty($CODE)) {
            $dbGroup = $group->GetList(($by = "ID"), ($order = "asc"), array('STRING_ID' => $CODE));
            if ($arGroup = $dbGroup->Fetch()) {
                $arReturn = $arGroup;
            }
        } else {
            throw new \Exception('Empty group code');
        }

        if (intval($arReturn['ID']) > 0) {
            $arUsers = \CGroup::GetGroupUser($arReturn['ID']);
            foreach ($arUsers as $UserID) {
                $arGroup = \CUser::GetUserGroup($UserID);
                $arGroup[] = "3";
                \CUser::SetUserGroup($UserID, $arGroup);
            }
            $res = $group->Delete($arReturn['ID']);

            if (is_object($res)) {
                return $arReturn['ID'];
            } else {
                throw new \Exception($group->LAST_ERROR);
            }
        } else {
            throw new \Exception('Group not found');
        }
    }


}
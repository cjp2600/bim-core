<?php

namespace Bim\Db\Generator\Providers;
use Bim\Db\Generator\Code;
use Bim\Exception\BimException;

/**
 * Class GroupGen
 * @package Bim\Db\Lib
 */
class Group extends Code
{
    /**
     * Генерация создания
     *
     * generateAddCode
     * @param array $groupId
     * @return string
     * @throws \Exception
     * @internal param array $params
     */
    public function generateAddCode($groupId)
    {
        $this->checkParams($groupId);
        if ($groupData = $this->ownerItemDbData) {
            unset($groupData['ID']);
            $addFields = $groupData;
            return $this->getMethodContent('Bim\Db\Main\GroupIntegrate', 'Add', array($addFields));
        }
        return true;
    }

    /**
     * Генерация кода обновления
     *
     * generateUpdateCode
     * @param array $params
     * @return mixed|void
     */
    public function generateUpdateCode($params)
    {
        // Update generate
    }

    /**
     * метод для генерации кода удаления
     *
     * generateDeleteCode
     * @param array $groupId
     * @return string
     * @throws \Exception
     * @internal param array $params
     */
    public function generateDeleteCode($groupId)
    {
        $this->checkParams($groupId);

        if ($groupData = $this->ownerItemDbData) {
            unset($groupData['ID']);
            return $this->getMethodContent('Bim\Db\Main\GroupIntegrate', 'Delete', array($groupData['STRING_ID']));
        }
        return false;
    }

    /**
     * Абстрактный метод проверки передаваемых параметров
     *
     * checkParams
     * @param array $groupId
     * @return mixed|void
     * @throws \Exception
     * @internal param array $params
     */
    public function checkParams($groupId)
    {
        $group = new \CGroup();
        if (!isset($groupId) || empty($groupId)) {
            throw new BimException('empty groupId param');
        }
        $this->ownerItemDbData = array();
        $groupDbRes = $group->GetList($by = 'id', $order = 'desc', array('ID' => $groupId));
        if ($groupDbRes === false || !$groupDbRes->SelectedRowsCount()) {
            throw new BimException('Group with id = ' . $groupId . ' not exists');
        }
        $groupData = $groupDbRes->Fetch();
        if (!strlen($groupData['STRING_ID'])) {
            throw new BimException('Group with id = ' . $groupId . ' have empty STRING_ID!');
        }
        $this->ownerItemDbData = $groupData;
    }


}
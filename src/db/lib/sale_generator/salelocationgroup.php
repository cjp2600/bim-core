<?php


/**
 * Class SaleLocationGroupGen
 * класс для генерации кода изменений группы местоположения
 *
 * @package Bitrix\Adv_Preset\SaleLocationGroupGen
 */
class SaleLocationGroupGen extends CodeGenerator
{


    public function __construct()
    {
        \CModule::IncludeModule('sale');
    }

    /**
     * метод для генерации кода добавления новой группы местоположения
     * @param $params array
     * @return mixed
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Добавляем новую группу местоположения */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $groupData) {
            $addFields = $groupData;


            $code = $code . $this->buildCode('SaleLocationGroupIntegrate', 'Add',
                    array($addFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода обновления группы местоположения
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Обновляем группу местоположения */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $groupData) {
            $updateFields = $groupData;

            $code = $code . $this->buildCode('SaleLocationGroupIntegrate', 'Update',
                    array($updateFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления  группы местоположения
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Удаляем  группу местоположения   */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $groupData) {
            $code = $code . $this->buildCode('SaleLocationGroupIntegrate', 'Delete',
                    array($groupData['LANG'][0]['LID'], $groupData['LANG'][0]['NAME']));
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     * groupId => id городов
     * )
     * @return mixed
     */
    public function checkParams($params)
    {

        if (!isset($params['groupId']) || empty($params['groupId'])) {
            throw new \Exception('В параметрах не найден groupId');
        }


        foreach ($params['groupId'] as $groupId) {

            $groupData['SORT'] = 100;

            $groupData['LOCATION_ID'] = array();
            $locationDbRes = CSaleLocationGroup::GetLocationList(array('LOCATION_GROUP_ID' => $groupId));
            while ($locationData = $locationDbRes->Fetch()) {
                $groupData['LOCATION_ID'][] = $locationData['LOCATION_ID'];
            }

            $dbLang = \CLangAdmin::GetList($by = "sort", $order = "asc");
            while ($arLang = $dbLang->Fetch()) {
                $groupLang = \CSaleLocationGroup::GetGroupLangByID($groupId, $arLang['LID']);
                unset($groupLang['ID']);
                unset($groupLang['LOCATION_GROUP_ID']);
                $groupData['LANG'][] = $groupLang;

            }


            $this->ownerItemDbData[] = $groupData;
        }


    }


}

?>

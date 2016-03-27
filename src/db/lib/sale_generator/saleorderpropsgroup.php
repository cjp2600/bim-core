<?php


/**
 * Class SaleOrderPropsGroupGen
 * класс для генерации кода изменений группы свойств заказа
 *
 * @package Bitrix\Adv_Preset\SaleOrderPropsGroupGen
 */
class SaleOrderPropsGroupGen extends CodeGenerator
{


    public function __construct()
    {
        \CModule::IncludeModule('sale');
    }

    /**
     * метод для генерации кода добавления новой группы свойств заказа
     * @param $params array
     * @return mixed
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Добавляем группу свойств заказа */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $groupData) {
            $addFields = $groupData;

            $code = $code . $this->buildCode('SaleOrderPropsGroupIntegrate', 'Add',
                    array($addFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода обновления группы свойств заказа
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Обновляем группу свойств заказа */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $groupData) {
            $updateFields = $groupData;

            $code = $code . $this->buildCode('SaleOrderPropsGroupIntegrate', 'Update',
                    array($updateFields['NAME'], $updateFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления группы свойств заказа
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Удаляем  группу свойств заказа   */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $groupData) {
            $code = $code . $this->buildCode('SaleOrderPropsGroupIntegrate', 'Delete', array($groupData['NAME']));
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     * groupId => id группы
     * )
     * @return mixed
     */
    public function checkParams($params)
    {

        if (!isset($params['groupId']) || empty($params['groupId'])) {
            throw new \Exception('В параметрах не найден groupId');
        }


        foreach ($params['groupId'] as $groupId) {

            $groupData = \CSaleOrderPropsGroup::GetByID($groupId);
            if (!$groupData) {
                throw new \Exception('В системе не найден группа св-в с id = "' . $groupId . '"');
            }
            unset($groupData['ID']);


            $this->ownerItemDbData[] = $groupData;
        }


    }


}

?>

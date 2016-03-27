<?php


/**
 * Class SalePaySystemActionGen
 * класс для генерации кода изменений обработчиков платежных систем
 *
 * @package Bitrix\Adv_Preset\SalePaySystemActionGen
 */
class SalePaySystemActionGen extends CodeGenerator
{


    public function __construct()
    {
        \CModule::IncludeModule('sale');
    }

    /**
     * метод для генерации кода добавления обработчика платежной системы
     * @param $params array
     * @return mixed
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Добавляем обработчик платежной системы  */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $actionData) {
            $addFields = $actionData;
            unset($addFields['ID']);

            $code = $code . $this->buildCode('SalePaySystemActionIntegrate', 'Add',
                    array($addFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода обновления обработчика платежной системы
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Обновляем обработчик платежной системы  */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $actionData) {
            $updateFields = $actionData;


            $code = $code . $this->buildCode('SalePaySystemActionIntegrate', 'Update',
                    array($updateFields['NAME'], $updateFields['PERSON_TYPE_ID'], $updateFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления  обработчика платежной системы
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Удаляем  обработчик платежной системы   */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $actionData) {
            $code = $code . $this->buildCode('SalePaySystemActionIntegrate', 'Delete',
                    array($actionData['NAME'], $actionData['PERSON_TYPE_ID']));
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     * actionId => id налога
     * )
     * @return mixed
     */
    public function checkParams($params)
    {

        if (!isset($params['actionId']) || empty($params['actionId'])) {
            throw new \Exception('В параметрах не найден actionId');
        }

        foreach ($params['actionId'] as $actionId) {
            $actionData = \CSalePaySystemAction::GetByID($actionId);


            unset($actionData['ID']);

            $this->ownerItemDbData[] = $actionData;
        }


    }


}

?>

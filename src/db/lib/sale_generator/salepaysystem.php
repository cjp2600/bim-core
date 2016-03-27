<?php


/**
 * Class SalePaySystemGen
 * класс для генерации кода изменений платежных систем
 *
 * @package Bitrix\Adv_Preset\SalePaySystemGen
 */
class SalePaySystemGen extends CodeGenerator
{


    public function __construct()
    {
        \CModule::IncludeModule('sale');
    }

    /**
     * метод для генерации кода добавления новой платежной системы
     * @param $params array
     * @return mixed
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Добавляем платежную системы */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $systemData) {
            $addFields = $systemData;

            $code = $code . $this->buildCode('SalePaySystemIntegrate', 'Add', array($addFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода обновления платежной системы
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Обновляем платежную системы */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $systemData) {
            $updateFields = $systemData;

            $code = $code . $this->buildCode('SalePaySystemIntegrate', 'Update',
                    array($updateFields['NAME'], $updateFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления платежной системы
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Удаляем  платежную системы   */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $systemData) {
            $code = $code . $this->buildCode('SalePaySystemIntegrate', 'Delete', array($systemData['NAME']));
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     * systemId => id группы
     * )
     * @return mixed
     */
    public function checkParams($params)
    {

        if (!isset($params['systemId']) || empty($params['systemId'])) {
            throw new \Exception('В параметрах не найден systemId');
        }


        foreach ($params['systemId'] as $systemId) {

            $systemData = \CSalePaySystem::GetByID($systemId);
            if (!$systemData) {
                throw new \Exception('В системе не найдена платежная система с id = "' . $systemId . '"');
            }
            unset($systemData['ID']);


            $this->ownerItemDbData[] = $systemData;
        }


    }


}

?>

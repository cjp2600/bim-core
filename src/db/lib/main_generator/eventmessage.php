<?php


/**
 * Class EventMessageGen
 * класс для генерации кода изменений в почтовых шаблонах
 *
 * @package Bitrix\Adv_Preset\Main_Generator
 */
class EventMessageGen extends CodeGenerator
{


    public function __construct()
    {
    }

    /**
     * метод для генерации кода добавления нового  почт. шаблона
     * @param $params array
     * @return string
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php' . PHP_EOL . '/*  Добавляем новый  почт. шаблон  */' . PHP_EOL . PHP_EOL;
        foreach ($ownerItemDbData as $eventMessData) {
            unset($eventMessData['ID']);
            $addFields = $eventMessData;

            $code = $code . $this->buildCode('EventMessageIntegrate', 'Add', array($addFields)) . PHP_EOL . PHP_EOL;
        }

        return $code;

    }

    /**
     * метод для генерации кода обновления  почт. шаблона
     * @param $params array
     * @return string
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php' . PHP_EOL . '/*  Обновляем почт. шаблон */' . PHP_EOL . PHP_EOL;
        foreach ($ownerItemDbData as $eventMessData) {
            unset($eventMessData['ID']);
            $updateFields = $eventMessData;

            $code = $code . $this->buildCode('EventMessageIntegrate', 'Update',
                    array($updateFields['EVENT_NAME'], $updateFields)) . PHP_EOL . PHP_EOL;
        }

        return $code;

    }

    /**
     * метод для генерации кода удаления  почт. шаблона
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php' . PHP_EOL . '/*  Удаляем почт. шаблон  */' . PHP_EOL . PHP_EOL;
        foreach ($ownerItemDbData as $eventMessData) {
            unset($eventMessData['ID']);
            $code = $code . $this->buildCode('EventMessageIntegrate', 'Delete',
                    array($eventMessData['EVENT_NAME'])) . PHP_EOL . PHP_EOL;
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     *          eventMessId => (array) массив с id почт. шаблонов
     *   )
     * @return mixed
     */
    public function checkParams($params)
    {


        if (!isset($params['eventMessId']) || empty($params['eventMessId'])) {
            throw new \Exception('В параметрах не найден eventMessId');
        }


        $this->ownerItemDbData = array();
        foreach ($params['eventMessId'] as $eventMessId) {
            $eventMessDbRes = \CEventMessage::GetList($by = "site_id", $order = "desc", array('ID' => $eventMessId));
            if ($eventMessDbRes === false || !$eventMessDbRes->SelectedRowsCount()) {
                throw new \Exception('Не найден почт. шаблон с id = ' . $eventMessId);
            }

            $eventMessData = $eventMessDbRes->Fetch();

            $this->ownerItemDbData[] = $eventMessData;

        }

    }


}

?>

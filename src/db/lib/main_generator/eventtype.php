<?php


/**
 * Class EventTypeGen
 * класс для генерацияя кода изменений в типах почтовых событий
 *
 * @package Bitrix\Adv_Preset\Main_Generator
 */
class EventTypeGen extends CodeGenerator
{


    public function __construct()
    {
    }

    /**
     * метод для генерации кода добавления нового типа почт. события
     * @param $params array
     * @return string
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php' . PHP_EOL . '/*  Добавляем новой тип почт. события */' . PHP_EOL . PHP_EOL;
        foreach ($ownerItemDbData as $eventTypeData) {
            unset($eventTypeData['ID']);
            $addFields = $eventTypeData;

            $code = $code . $this->buildCode('EventTypeIntegrate', 'Add', array($addFields)) . PHP_EOL . PHP_EOL;
        }

        return $code;

    }

    /**
     * метод для генерации кода обновления  типа почт. события
     * @param $params array
     * @return string
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php' . PHP_EOL . '/*  Обновляем тип почт. события */' . PHP_EOL . PHP_EOL;
        foreach ($ownerItemDbData as $eventTypeData) {
            unset($eventTypeData['ID']);
            $updateFields = $eventTypeData;

            $code = $code . $this->buildCode('EventTypeIntegrate', 'Update',
                    array($updateFields['LID'], $updateFields['EVENT_NAME'], $updateFields)) . PHP_EOL . PHP_EOL;
        }

        return $code;

    }

    /**
     * метод для генерации кода удаления  типа почт. события
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $ownerItemDbData = $this->ownerItemDbData;
        $code = '<?php' . PHP_EOL . '/*  Удаляем тип почт. события  */' . PHP_EOL . PHP_EOL;
        foreach ($ownerItemDbData as $eventTypeData) {
            unset($eventTypeData['ID']);
            $code = $code . $this->buildCode('EventTypeIntegrate', 'Delete',
                    array($eventTypeData['LID'], $eventTypeData['EVENT_NAME'])) . PHP_EOL . PHP_EOL;
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     *          eventTypeId => (array) массив с id типов собыйти
     *   )
     * @return mixed
     */
    public function checkParams($params)
    {


        if (!isset($params['eventTypeId']) || empty($params['eventTypeId'])) {
            throw new \Exception('В параметрах не найден eventTypeId');
        }


        $this->ownerItemDbData = array();

        foreach ($params['eventTypeId'] as $eventTypeId) {
            $eventTypeDbRes = \CEventType::GetList(array('ID' => $eventTypeId), array());
            if ($eventTypeDbRes === false || !$eventTypeDbRes->SelectedRowsCount()) {
                throw new \Exception('Не найдено почт. событие с id = ' . $eventTypeId);
            }

            $eventTypeData = $eventTypeDbRes->Fetch();

            $this->ownerItemDbData[] = $eventTypeData;

        }

    }


}

?>

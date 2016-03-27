<?php


/**
 * Class RubricGen
 * класс для генерации кода изменений рубрики подписки
 *
 * @package Bitrix\Adv_Preset\RubricGen
 */
class RubricGen extends CodeGenerator
{


    public function __construct()
    {
        \CModule::IncludeModule('subscribe');
    }

    /**
     * метод для генерации кода добавления рубрики подписки
     * @param $params array
     * @return mixed
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Добавляем рубрику подписки */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $rubricData) {
            $addFields = $rubricData;
            unset($addFields['ID']);

            $code = $code . $this->buildCode('RubricIntegrate', 'Add', array($addFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода обновления рубрики подписки
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Обновляем рубрику подписки */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $rubricData) {
            $updateFields = $rubricData;


            $code = $code . $this->buildCode('RubricIntegrate', 'Update',
                    array($updateFields['CODE'], $updateFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления  рубрики подписки
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Удаляем  рубрику подписки   */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $rubricData) {
            $code = $code . $this->buildCode('RubricIntegrate', 'Delete', array($rubricData['CODE']));
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     * rubricId => id рубрики
     * )
     * @return mixed
     */
    public function checkParams($params)
    {

        if (!isset($params['rubricId']) || empty($params['rubricId'])) {
            throw new \Exception('В параметрах не найден rubricId');
        }

        foreach ($params['rubricId'] as $rubricId) {
            $rubricData = \CRubric::GetByID($rubricId);

            if (!strlen($rubricData['CODE'])) {
                throw new \Exception('У рубрики с id = "' . $rubricData['ID'] . '" не указан CODE');
            }
            unset($rubricData['ID']);

            $this->ownerItemDbData[] = $rubricData;
        }


    }


}

?>

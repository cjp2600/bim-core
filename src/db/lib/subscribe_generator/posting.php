<?php


/**
 * Class PostingGen
 * класс для генерации кода изменений в выпусках
 *
 * @package Bitrix\Adv_Preset\PostingGen
 */
class PostingGen extends CodeGenerator
{


    public function __construct()
    {
        \CModule::IncludeModule('subscribe');
    }

    /**
     * метод для генерации кода добавления выпуска
     * @param $params array
     * @return mixed
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Добавляем выпуск */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $postingData) {
            $addFields = $postingData;
            unset($addFields['ID']);

            $code = $code . $this->buildCode('PostingIntegrate', 'Add', array($addFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода обновления выпуска
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Обновляем выпуск */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $postingData) {
            $updateFields = $postingData;


            $code = $code . $this->buildCode('PostingIntegrate', 'Update',
                    array($updateFields['SUBJECT'], $updateFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления  выпуска
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Удаляем  выпуск   */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $postingData) {
            $code = $code . $this->buildCode('PostingIntegrate', 'Delete', array($postingData['SUBJECT']));
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     * postingId => id рубрики
     * )
     * @return mixed
     */
    public function checkParams($params)
    {

        if (!isset($params['postingId']) || empty($params['postingId'])) {
            throw new \Exception('В параметрах не найден postingId');
        }

        foreach ($params['postingId'] as $postingId) {
            $postingDbRes = \CPosting::GetByID($postingId);
            $postingData = $postingDbRes->Fetch();

            if (!strlen($postingData['SUBJECT'])) {
                throw new \Exception('У выпуска с id = "' . $postingData['ID'] . '" не указан SUBJECT');
            }
            unset($postingData['ID']);

            $this->ownerItemDbData[] = $postingData;
        }


    }


}

?>

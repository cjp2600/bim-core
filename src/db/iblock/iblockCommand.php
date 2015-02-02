<?php
/**
 * Created for the project "bim-core"
 *
 * @author: Stanislav Semenov (CJP2600)
 * @email: cjp2600@ya.ru
 *
 * @date: 02.02.2015
 * @time: 9:27
 */

namespace Bim\Db\Iblock;


class IblockCommand extends \BaseCommand {

    /**
     * createWizard
     * @param $up_data
     * @param $down_data
     * @param $desc
     * @param $is_full
     */
    public function createWizard(&$up_data,&$down_data,&$desc,$is_full)
    {
        $dialog  = new \ConsoleKit\Widgets\Dialog($this->console);

        # get description options
        $desc = (isset($options['d'])) ? $options['d'] : "";
        if (!is_string($desc)) {
            $desk = "Type Description of migration file. Example: TASK-124";
            $desc = $dialog->ask($desk.PHP_EOL.$this->color('Description:',\ConsoleKit\Colors::BLUE), "",false);
        }

        if ($is_full){
            $this->padding("Full designer create an information block".PHP_EOL.$this->color('Warning! It may take a long time', \ConsoleKit\Colors::YELLOW));
        }

        $do = true;
        while ($do) {
            $desk = "Information block type - no default/required";
            $field_val = $dialog->ask($desk . PHP_EOL . $this->color('[IBLOCK_TYPE_ID]:', \ConsoleKit\Colors::YELLOW), '', false);
            $up_data['IBLOCK_TYPE_ID'] = $this->clear($field_val);

            $iblockTypeDbRes = \CIBlockType::GetByID($up_data['IBLOCK_TYPE_ID']);
            if (!$iblockTypeDbRes || (!$iblockTypeDbRes->SelectedRowsCount())) {

                $this->error('Iblock type with id = "' . $up_data['IBLOCK_TYPE_ID'] . '" not exist.');
                $field_val = $dialog->ask("Do you want to continue recording a non-existent id? [Y/N]", 'Y');
                if (strtolower($field_val) == "y") {
                    $do = false;
                }

            } else {
                $do = false;
            }
        }

        $desk = "Name of the information block - no default/required";
        $field_val = $dialog->ask($desk . PHP_EOL . $this->color('[NAME]:', \ConsoleKit\Colors::YELLOW), '', false);
        $up_data['NAME'] = $this->clear($field_val);

        $do = true;
        while ($do) {
            $desk = "Character code information block - no default/required";
            $field_val = $dialog->ask($desk . PHP_EOL . $this->color('[CODE]:', \ConsoleKit\Colors::YELLOW), '', false);
            $up_data['CODE'] = $this->clear($field_val);

            #check on exist
            $iblockDbRes = \CIBlock::GetList(array(), array('CODE' => $up_data['CODE']));
            if (!$iblockDbRes || (!$iblockDbRes->SelectedRowsCount())) {
                $do = false;
            } else {
                $this->error('Iblock with code = "' . $up_data['CODE'] . '" already exist.');
            }
        }

        # send down function var
        $down_data['IBLOCK_CODE'] = $this->clear($up_data['CODE']);

        $desk = "Sites to which the information block - no default/required. Пример: s1";
        $field_val = $dialog->ask($desk . PHP_EOL . $this->color('[LID]:', \ConsoleKit\Colors::YELLOW), "s1");
        $up_data['LID'] = $this->clear($field_val);

        if ($is_full){
            $desk = "text description of the information block - default ''";
            $field_val = $dialog->ask($desk . PHP_EOL . $this->color('[DESCRIPTION]:', \ConsoleKit\Colors::YELLOW), '', false);
            $up_data['DESCRIPTION'] = $this->clear($field_val);

            $desk = "Заголовок 'Разделы' - default 'Разделы'";
            $field_val = $dialog->ask($desk . PHP_EOL . $this->color('[SECTIONS_NAME]:', \ConsoleKit\Colors::YELLOW), 'Разделы', true);
            $up_data['SECTIONS_NAME'] = $this->clear($field_val);

            $desk = "Заголовок 'Раздел' - default 'Раздел'";
            $field_val = $dialog->ask($desk . PHP_EOL . $this->color('[SECTION_NAME]:', \ConsoleKit\Colors::YELLOW), 'Раздел', true);
            $up_data['SECTION_NAME'] = $this->clear($field_val);
        }


        if (empty($desc)) {
            $desk = "Type Description of migration file. Example: TASK-124";
            $desc = $dialog->ask($desk . PHP_EOL . $this->color('Description:', \ConsoleKit\Colors::BLUE), "", false);
        }
    }

}
<?php
/**
 * Created for the project "bim"
 *
 * @author: Stanislav Semenov (CJP2600)
 * @email: cjp2600@ya.ru
 *
 * @date: 21.01.2015
 * @time: 22:42
 */
use ConsoleKit\Console,
    ConsoleKit\Command,
    ConsoleKit\Colors,
    ConsoleKit\Widgets\Dialog,
    ConsoleKit\Widgets\ProgressBar;

/**
 * Getting information about the project
 */
class InfoCommand extends Command {

    public function execute(array $args, array $options = array())
    {
        $site_name = \Bitrix\Main\Config\Option::get("main", "site_name");
        $this->writeln('Getting information about the project:', Colors::YELLOW);
        $box = new ConsoleKit\Widgets\Box($this->console, $site_name, '');
        $box->write();
        $this->writeln('');

    }

}

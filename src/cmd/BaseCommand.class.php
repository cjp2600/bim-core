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
class BaseCommand extends Command {

    /**
     * info
     * @param $text
     */
    public function info($text)
    {
        $this->writeln($text, Colors::YELLOW);
    }

    /**
     * success
     * @param $text
     */
    public function success($text)
    {
        $this->writeln($text, Colors::GREEN);
    }

    /**
     * padding
     * @param $text
     * @throws \ConsoleKit\ConsoleException
     */
    public function padding($text)
    {
        $box = new ConsoleKit\Widgets\Box($this->console, $text, '');
        $box->write();
        $this->writeln('');
    }

    /**
     * info
     * @param $text
     */
    public function error($text)
    {
        $this->writeln($text, Colors::RED);
    }

}

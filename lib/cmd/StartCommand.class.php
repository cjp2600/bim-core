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

class StartCommand extends Command {

    public function execute(array $args, array $options = array())
    {
        $this->writeln('Привет это я',Colors::GREEN);
    }

}

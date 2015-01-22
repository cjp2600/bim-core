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

class InitCommand extends BaseCommand {

    public function execute(array $args, array $options = array())
    {
       $this->success("Initialize the project - completed");
    }

}

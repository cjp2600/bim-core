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

/**
 * Getting information about the project
 */
class InfoCommand extends BaseCommand {

    public function execute(array $args, array $options = array())
    {
        # get site name
        $site_name = \Bitrix\Main\Config\Option::get("main", "site_name");

        $this->info("Information about the current bitrix project:");
        $this->padding("Site Name: ".$site_name);

    }

}

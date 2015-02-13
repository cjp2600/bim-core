<?php

use ConsoleKit\Colors;
/**
 * Getting information about the project.
 */
class InfoCommand extends BaseCommand {

    public function execute(array $args, array $options = array())
    {
        $return = array();

        # get site name
        $site_name = \Bitrix\Main\Config\Option::get("main", "site_name");

        # get site name
        $return[] = Colors::colorize('Site:', Colors::YELLOW)." ".$site_name;

        # get bitrix version
        $MESS = array();
        include_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/lang/ru/interface/epilog_main_admin.php";
        $vendor = COption::GetOptionString("main", "vendor", "1c_bitrix");
        $info_text = $MESS["EPILOG_ADMIN_SM_".$vendor]." (".SM_VERSION.")";
        $return[] = Colors::colorize('Version:', Colors::YELLOW)." ".$info_text;

        $url = "https://packagist.org/search.json?q=bim";
        $json = file_get_contents($url);
        $data = json_decode($json);

        $dataPack = null;
        foreach ($data->results as $item) {
            if ($item->name == "cjp2600/bim-core"){
                $dataPack = $item;
            }
        }

        if (is_null($dataPack)) {
            $info_text = PHP_EOL.'Bitrix migration (BIM) v.0.0.1'.PHP_EOL.'http://cjp2600.github.io/bim-core'.PHP_EOL;
        } else {
            $info_text = PHP_EOL;
            foreach ((array) $item as $key => $val) {
                $info_text .= Colors::colorize($key, Colors::YELLOW).": ".$val.PHP_EOL;
            }
            $info_text .= PHP_EOL;
        }

        # edition
        $return[] = Colors::colorize('Edition:', Colors::YELLOW)." ".$this->checkRedaction();

        $this->info("About bim:");

        # for fun :)
        $this->padding($info_text);

        $this->info("About bitrix project:");

        # display
        $this->padding(implode(PHP_EOL,$return));
    }

    /**
     * checkRedaction
     * @return int|string
     */
    public function checkRedaction()
    {
        $bitrix_modules = $this->getModules();
        $redactions = array(
            'Первый сайт' => array("main","main"),
            'Старт'   => array("main","search"),
            'Стандарт'=> array("main","photogallery"),
            'Эксперт' => array("main","advertising"),
            'Малый бизнес' => array("main","sale"),
            'Бизнес' => array("main","workflow","report")
        );
        $current_redaction = "не определено";
        foreach ($redactions as $module => $ids ) {
            foreach ($ids as $id){
                $check = true;
                if (!isset($bitrix_modules[$id])){
                    $check = false;
                }
                if ($check){
                    $current_redaction = $module;
                }
            }
        }
        return $current_redaction;
    }

    /**
     * getModules
     * @return mixed
     */
    public function getModules()
    {
        $folders = array(
            "/local/modules",
            "/bitrix/modules",
        );
        foreach($folders as $folder)
        {
            $handle = @opendir($_SERVER["DOCUMENT_ROOT"].$folder);
            if($handle)
            {
                while (false !== ($dir = readdir($handle)))
                {
                    if(!isset($arModules[$dir]) && is_dir($_SERVER["DOCUMENT_ROOT"].$folder."/".$dir) && $dir!="." && $dir!=".." && $dir!="main" && strpos($dir, ".") === false)
                    {
                        $module_dir = $_SERVER["DOCUMENT_ROOT"].$folder."/".$dir;
                        if($info = CModule::CreateModuleObject($dir))
                        {
                            $arModules[$dir]["MODULE_ID"] = $info->MODULE_ID;
                            $arModules[$dir]["MODULE_NAME"] = $info->MODULE_NAME;
                            $arModules[$dir]["MODULE_DESCRIPTION"] = $info->MODULE_DESCRIPTION;
                            $arModules[$dir]["MODULE_VERSION"] = $info->MODULE_VERSION;
                            $arModules[$dir]["MODULE_VERSION_DATE"] = $info->MODULE_VERSION_DATE;
                            $arModules[$dir]["MODULE_SORT"] = $info->MODULE_SORT;
                            $arModules[$dir]["MODULE_PARTNER"] = (strpos($dir, ".") !== false) ? $info->PARTNER_NAME : "";
                            $arModules[$dir]["MODULE_PARTNER_URI"] = (strpos($dir, ".") !== false) ? $info->PARTNER_URI : "";
                            $arModules[$dir]["IsInstalled"] = $info->IsInstalled();
                        }
                    }
                }
                closedir($handle);
            }
        }
        uasort($arModules, create_function('$a, $b', 'if($a["MODULE_SORT"] == $b["MODULE_SORT"]) return strcasecmp($a["MODULE_NAME"], $b["MODULE_NAME"]); return ($a["MODULE_SORT"] < $b["MODULE_SORT"])? -1 : 1;'));
        return $arModules;
    }

}

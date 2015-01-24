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

use ConsoleKit\Colors;
/**
 * Getting information about the project
 */
class InfoCommand extends BaseCommand {

    public function execute(array $args, array $options = array())
    {
        $return = array();

        # get site name
        $site_name = \Bitrix\Main\Config\Option::get("main", "site_name");

        $this->info("Information about the current bitrix project:");

        # get site name
        $return[] = Colors::colorize('Site Name:', Colors::YELLOW)." ".$site_name;

        # get bitrix version
        $MESS = array();
        include_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/lang/ru/interface/epilog_main_admin.php";
        $vendor = COption::GetOptionString("main", "vendor", "1c_bitrix");
        $info_text = $MESS["EPILOG_ADMIN_SM_".$vendor]." (".SM_VERSION.")";
        $return[] = Colors::colorize('Version:', Colors::YELLOW)." ".$info_text;

        # edition
        $return[] = Colors::colorize('Edition:', Colors::YELLOW)." ".$this->checkRedaction();

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

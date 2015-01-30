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

    /**
     * setTemplate
     * @param $name of create
     * @param $method
     * @param array $data
     * @param string $type up|down
     * @return mixed|string
     */
    public function setTemplateMethod($name, $method, $data = array(),$type = "up")
    {
        $template = file_get_contents(__DIR__.'/../db/template/'.$name.'/'.$method.'/'.$type.'.txt');
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                $template = str_replace("#".$key."#", $val, $template);
            }
        }
        return $template;
    }

    /**
     * setTemplate
     * @param $class_name
     * @param $up_content
     * @param $down_content
     * @return mixed|string
     */
    public function setTemplate($class_name, $up_content, $down_content, $desc_content = "",$author = "")
    {
        $template = file_get_contents(__DIR__.'/../db/template/main.txt');
        $template = str_replace(array("#CLASS_NAME#", "#UP_CONTENT#", "#DOWN_CONTENT#","#DESC_CONTENT#","#AUTHOR_CONTENT#"), array($class_name, $up_content, $down_content,$desc_content,$author), $template);
        return $template;
    }

    /**
     * saveTemplate
     * @param $filename
     * @param $template
     */
    public function saveTemplate($filename,$template)
    {
        $migration_path = $this->getMigrationPath();
        if (!file_exists($migration_path)){
            mkdir($migration_path, 0777);
        }
        $save_file = $migration_path.$filename.'.php';
        $newFile = fopen($save_file, 'w');
        fwrite($newFile, $template);
        fclose($newFile);
        # output
        $this->writeln("Create new migration file: ");
        $this->success($save_file);
    }

    /**
     * getMigrationPath
     * @param bool $full
     * @return mixed|string
     */
    public function getMigrationPath($full = true)
    {
        $conf = new \Noodlehaus\Config(__DIR__."/../config/bim.json");
        $migration_path = $conf->get("migration_path");
        return ($full) ? $_SERVER["DOCUMENT_ROOT"] . "/".$migration_path."/" : $migration_path;
    }


    public function clear($text)
    {
        $text = str_replace("�"," ",$text);
        $text = str_replace('"',"'",$text);
        $text = str_replace('/',"",$text);
        return $text;
    }

    /**
     * getMigrationName
     * @return string
     */
    public function getMigrationName()
    {
        return time();
    }

    /**
     * fromCamelCase
     * @param $input
     * @return string
     */
    public function fromCamelCase($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    /**
     * camelCase
     * @param $str
     * @param array $noStrip
     * @return mixed|string
     */
    public function camelCase($str, array $noStrip = [])
    {
        // non-alpha and non-numeric characters become spaces
        $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
        $str = trim($str);
        // uppercase the first character of each word
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);
        $str = str_replace(":", "", $str);
        $str = preg_replace('/[0-9]+/', '', $str);
        $str = lcfirst($str);

        return $str;
    }

    /**
     * getDirectoryTree
     * @param $outerDir
     * @param $x
     * @return array
     */
    public function getDirectoryTree( $outerDir , $x)
    {
        $dirs = array_diff( scandir( $outerDir ), Array( ".", ".." ) );
        $dir_array = Array();
        foreach( $dirs as $d ){
            if( is_dir($outerDir."/".$d)  ){
                $dir_array[ $d ] = $this->getDirectoryTree( $outerDir."/".$d , $x);
            }else{
                if (($x)?ereg($x.'$',$d):1)
                    $dir_array[ str_replace(".".$x,"",$d) ] = $d;
            }
        }
        $return = array();
        foreach ($dir_array as $key => $val){
                $return[$key] = array(
                    "name" => $key,
                    "file" => $val,
                    "date" => $key
                );
        }
        return $return;
    }

    /**
     * green
     * @param $text
     * @return string
     */
    public function color($text,$color)
    {
        return Colors::colorize($text, $color);
    }

    /**
     * checkInDb
     * @param $migration_id
     * @return bool
     */
    public function checkInDb($migration_id)
    {
        # check migration table
        $this->checkMigrationTable();
        $obMigration = Bim\Db\Entity\MigrationsTable::getList(array(
            "filter" => array(
                "id" => $migration_id
            )
        ));
        if ($arMigration = $obMigration->fetch()){
            # This Bitrix - Babe
            if ($arMigration['id'] == $migration_id){
                return true;
            }
        }
        return false;
    }

    /**
     * checkMigrationTable
     * @throws Exception
     */
    public function checkMigrationTable()
    {
        global $DB;
        if ( !$DB->Query("SELECT 'id' FROM bim_migrations", true) ) {
            throw new Exception("Migration table not found, run init command. Example: php bim init");
        }
    }

    /**
     * getIdByDescription
     * @param array $list
     * @param $desc_find
     * @return bool|int|string
     */
    public function getIdByDescription(array $list, $desc_find)
    {
        $return = $desc_find;
        foreach ($list as $id => $data) {
            if (!empty($data['file'])) {
                include_once $this->getMigrationPath() . $data['file'];
            }
            $class_name = "Migration".$id;
            $desc = false;
            if ((method_exists($class_name, "getDescription"))) {
                $desc = $class_name::getDescription();
                if (!empty($desc)){
                    if (strtolower($desc_find) == strtolower($desc)){
                        $return = $id;
                    }
                }
            }
        }
        return $return;
    }

}

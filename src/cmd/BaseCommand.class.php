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
     * @param array $data
     * @param string $type up|down
     * @return mixed|string
     */
    public function setTemplateMethod($name, $data = array(),$type = "up")
    {
        $template = file_get_contents(__DIR__.'/../db/template/'.$name.'/'.$type.'.txt');
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
        $template = str_replace(array("#CLASS_NAME#", "#UP_CONTENT#", "#DOWN_CONTENT#","#DESC_CONTENT#","#AUTHOR_CONTENT#"), array($this->camelCase($class_name), $up_content, $down_content,$desc_content,$author), $template);
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

    /**
     * getMigrationName
     * @param $name
     * @return string
     */
    public function getMigrationName($name)
    {
        return time()."_".randString(7, array(
            "abcdefghijklnmopqrstuvwxyz",
            "1234567890"
        ))."_".$name;
    }

    /**
     * fromCamelCase
     * @param $input
     * @return string
     */
    public function fromCamelCase($input) {
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
    public function getDirectoryTree( $outerDir , $x){
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
            $date = explode("_",$key);
            if (isset($date[1])){
                $return[$date[1]] = array(
                    "name" => $key,
                    "file" => $val,
                    "date" => $date[0]
                );
            }
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

}

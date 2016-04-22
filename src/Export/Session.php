<?php
namespace Bim\Export;

use Alchemy\Zippy\Exception\RuntimeException;
use Alchemy\Zippy\Zippy;
use Bim\Exception\BimException;
use MySQLDump;
use mysqli;

class Session extends \BaseCommand
{
    const EXPORT_FOLDER = 'export';
    const EXPORT_FILE_PREFIX = 'core_export_';

    private $sessionExportPathName = null;
    private $sessionExportFileName = null;
    private $sessionDBtFileName = null;


    private $sessionExportId = null;
    private $dataResponse = array();

    /**
     * Session constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return null
     */
    public function getSessionDBtFileName()
    {
        return $this->sessionDBtFileName;
    }

    /**
     * @param null $sessionDBtFileName
     */
    public function setSessionDBtFileName($sessionDBtFileName)
    {
        $this->sessionDBtFileName = $sessionDBtFileName;
    }

    /**
     * @return null
     */
    public function getSessionExportId()
    {
        return $this->sessionExportId;
    }

    /**
     * @param null $sessionExportId
     */
    public function setSessionExportId($sessionExportId)
    {
        $this->sessionExportId = $sessionExportId;
    }


    public function init()
    {
        $this->setSessionExportId(date("d_m_Y_H_i_s"));
        if (!file_exists($this->getMigrationPath() . self::EXPORT_FOLDER)) {
            mkdir($this->getMigrationPath() . self::EXPORT_FOLDER, 0777);
        }

        $sessionPath = $this->getMigrationPath() . self::EXPORT_FOLDER . "/" . $this->getSessionExportId();
        if (!file_exists($sessionPath)) {
            mkdir($sessionPath, 0777);
        }

        $this->setSessionExportPathName($sessionPath);
        $this->setSessionExportFileName(self::EXPORT_FILE_PREFIX . $this->getSessionExportId() . ".tar.gz");
        $this->setSessionDBtFileName($this->getSessionExportPathName() . '/' . 'db_' . self::EXPORT_FILE_PREFIX . $this->getSessionExportId() . '.sql.gz');

    }

    /**
     * @return null
     */
    public function getSessionExportPathName()
    {
        return $this->sessionExportPathName;
    }

    /**
     * @param null $sessionExportPathName
     */
    public function setSessionExportPathName($sessionExportPathName)
    {
        $this->sessionExportPathName = $sessionExportPathName;
    }

    /**
     * @return null
     */
    public function getSessionExportFileName()
    {
        return $this->sessionExportFileName;
    }

    /**
     * @param null $sessionExportFileName
     */
    public function setSessionExportFileName($sessionExportFileName)
    {
        $this->sessionExportFileName = $sessionExportFileName;
    }

    /**
     * @return string
     */
    public function getFullPath()
    {
        return $this->getSessionExportPathName() . '/' . $this->getSessionExportFileName();
    }

    /**
     * @return \Alchemy\Zippy\Archive\ArchiveInterface
     * @throws BimException
     */
    public function packCore()
    {
        try {
            $zippy = Zippy::load();
            $archive = $zippy->create($this->getFullPath(), $this->getBitrixCore(), true);

            $this->dataResponse[] = $this->color(strtoupper("completed"),
                    \ConsoleKit\Colors::GREEN) . " : " . $this->getFullPath();

            $this->saveInfoToJson();
            $this->dumpMysql();

            $this->dataResponse[] = $this->color(strtoupper("completed"),
                    \ConsoleKit\Colors::GREEN) . " : " . $this->getSessionDBtFileName();

            return $archive;
        } catch (RuntimeException $e) {
            $this->clearExport();
            throw new BimException("EXPORT ERROR: " . $e->getMessage());
        }
    }

    public function dumpMysql()
    {
        $DBHost = $DBLogin = $DBPassword = $DBName = "";
        include $_SERVER["DOCUMENT_ROOT"] . '/bitrix/php_interface/dbconn.php';
        $charset = 'utf8';
        if (!BX_UTF) {
            $charset = 'cp1251';
        }
        $dump = new MySQLDump(new mysqli($DBHost, $DBLogin, $DBPassword, $DBName), $charset);
        $dump->save($this->getSessionDBtFileName());
    }

    /**
     * @return array
     */
    public function getBitrixCore()
    {
        $excludeFolder = array("cache", "managed_cache", "stack_cache");
        $dirs = array_diff(scandir($_SERVER["DOCUMENT_ROOT"] . "/bitrix/"), Array(".", ".."));
        $folder = array();
        foreach ($dirs as $dir) {
            if (in_array($dir, $excludeFolder)) {
                continue;
            }
            $folder[] = $_SERVER["DOCUMENT_ROOT"] . "/bitrix/" . $dir;
        }
        return $folder;
    }

    private function saveInfoToJson()
    {
        $data = [];
        $exportJson = $this->getMigrationPath() . self::EXPORT_FOLDER . "/export.json";
        if (file_exists($exportJson)) {
            $data = json_decode(file_get_contents($exportJson), true);
            unlink($exportJson);
        }

        $dataItem = array(
            "id" => $this->getSessionExportId(),
            "core_name" => $this->getSessionExportFileName(),
            "core_path" => $this->getFullPath(),
            "db_file" => $this->getSessionDBtFileName(),
        );
        $data['items'][] = $dataItem;
        file_put_contents($exportJson, json_encode($data));

    }


    private function clearExport()
    {
        $this->deleteDirectory($this->getSessionExportPathName());
    }

    /**
     * @param $dir
     * @return bool
     */
    function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }

    /**
     * @return mixed
     */
    public function getDataResponse()
    {
        return implode("\n", $this->dataResponse);
    }

    public function upExport($args, $json)
    {
        global $DB, $APPLICATION;

        if (!isset($args[1])) {
            throw new BimException("number 0f export not fount. Example: pgp vendor/bin/bim export install 1");
        }

        if (empty($json)) {
            throw new BimException("empty export.json");

        }
        $num = $args[1];
        if (isset($json['items'][$num])) {
            $dump = $json['items'][$num]['db_file'];

            $this->getSQL($dump);


//            #sql
//            $errors = false;
//            $errors = $DB->RunSQLBatch($out_file_name,true);
//
//            if($errors !== false)
//            {
//                throw new BimException(implode("\n", $errors));
//            }
//
//            print_r($out_file_name . " ++1"); die();
        }
    }

    private function getSQL($dump)
    {
        $buffer_size = 4096;
        $out_file_name = str_replace('.gz', '', $dump);
        $file = gzopen($dump, 'rb');
        $out_file = fopen($out_file_name, 'wb');
        while (!gzeof($file)) {
            fwrite($out_file, gzread($file, $buffer_size));
        }
        fclose($out_file);
        gzclose($file);

        if (!file_exists($out_file_name) || !is_file($out_file_name)) {
            throw new BimException("!File $out_file_name is not found.");
        }

        $arErr = array();
        $contents = file_get_contents($out_file_name);
        $contents = str_replace("-- --------------------------------------------------------", "", $contents);
        $contents = trim($contents);
        $contents = explode(";", $contents);
        print_r($contents);
        die();

    }

}
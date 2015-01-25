<?php

use ConsoleKit\Widgets\ProgressBar,ConsoleKit\Colors;

class UpdateCommand extends BaseCommand
{
    public function execute(array $args, array $options = array())
    {
        $list = $this->getDirectoryTree($this->getMigrationPath(), "php");
        if (!empty($list)) {
            foreach ($list as $id => $data) {
                $row  = $data['file'];
                $name = $data['name'];

                # check in db
                $is_new = (!$this->checkInDb($id));
                $class_name = $this->camelCase($name);

                if ($is_new) {
                    $return_array_new[$id] = array($class_name,"" . $this->getMigrationPath() . $row . "",$name);
                } else {
                    $return_array_apply[$id] =  array($class_name,"" . $this->getMigrationPath() . $row . "",$name);
                }
            }

            $return = array();
            # filer
            $f_id = false;
            if ((isset($options['id']))) {
                $f_id = (is_string($options['id'])) ? $options['id'] : false;
            }

            if ($f_id){
                if (isset ($return_array_new[$f_id])) {
                    $return_array_new = array($f_id => $return_array_new[$f_id]);
                } else {
                    if (isset ($return_array_apply[$f_id])) {
                        throw new Exception($f_id . " - is already applied");
                    } else {
                        throw new Exception($f_id . " - is not found");
                    }
                }
            }

            foreach ( $return_array_new as $id => $mig) {
                include_once "" . $mig[1] . "";
                if ((method_exists($mig[0], "up"))) {
                    if ($do = $mig[0]::up()) {
                        if ($do === true) {
                            $obSelect = Bim\Db\Entity\MigrationsTable::getList(array("filter" => array("id" => $id)));
                            if (!$obSelect->fetch()) {
                                $content = base64_encode(file_get_contents($mig[1]));
                                $ob = Bim\Db\Entity\MigrationsTable::add(array(
                                    "id" => $id,
                                    "migration" => $mig[2],
                                    "content" => $content
                                ));
                                if ($ob->isSuccess()) {
                                    $return[] = $this->color("Applied : " . $mig[2], Colors::GREEN);
                                }
                            }
                        }
                    } else {
                        $return[] = $this->color("Error : " . $mig[2], Colors::RED).PHP_EOL.$this->color("Method Up return false",Colors::YELLOW);
                    }
                }
            }
            $this->padding(implode(PHP_EOL,$return));
        }
    }
}
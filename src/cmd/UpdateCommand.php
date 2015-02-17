<?php

use ConsoleKit\Colors;
/**
 * .
 * That command applied a new list of migration.
 * =========================================================================================
 *
 * 1) Applied all list of new migrations (sort by timestamp name):
 *              Example: php bim up
 *
 * 2) Applied single migration (php bim up [name]):
 *              Example: php bim up 1423573720
 *
 * 3) Applied list of new migrations for a certain period of time (sort by timestamp name):
 *              Example:  php bim up --from="29.01.2015 00:01" --to="29.01.2015 23:55"
 *
 * 4) Applied list of new migrations with tag (sort by timestamp name):
 *              Example: php bim up --tag=iws-123
 *
 *
 * Documentation: http://cjp2600.github.io/bim-core/
 * .
 */
class UpdateCommand extends BaseCommand
{
    public function execute(array $args, array $options = array())
    {
        global $DB;
        $list = $this->getDirectoryTree($this->getMigrationPath(), "php");
        ksort($list); # по возрастанию
        if (!empty($list)) {
            foreach ($list as $id => $data) {
                $row  = $data['file'];
                $name = $data['name'];

                # check in db
                $is_new = (!$this->checkInDb($id));
                $class_name = "Migration".$id;

                if ($is_new) {
                    $return_array_new[$id] = array($class_name,"" . $this->getMigrationPath() . $row . "",$name, $data['tags']);
                } else {
                    $return_array_apply[$id] =  array($class_name,"" . $this->getMigrationPath() . $row . "",$name, $data['tags']);
                }
            }

            # filer
            $f_id = false;
            if ((isset($options['id']))) {
                if (is_string($options['id'])) {
                    $f_id = $options['id'];
                } else {
                    $dialog = new \ConsoleKit\Widgets\Dialog($this->console);
                    $f_id  = $dialog->ask('Type migration id:', $f_id);
                }
            } else if (isset($args[0])){
                if (is_string($args[0])) {
                    $f_id  = $args[0];
                }
            }
            #check tag list
            $filer_tag = (isset($options['tag'])) ? $options['tag'] : false;

            if ($f_id){
                if (isset ($return_array_new[$f_id])) {
                    $return_array_new = array($f_id => $return_array_new[$f_id]);
                } else {
                    if (isset ($return_array_apply[$f_id])) {
                        throw new Exception("Migration ".$f_id . " - is already applied");
                    } else {
                        throw new Exception("Migration ".$f_id . " - is not found in new migrations list");
                    }
                }
            }
            # check to tag list
            if ($filer_tag) {
                $this->padding("up migration for tag : ".$filer_tag);
                $newArrayList = array();
                foreach ($return_array_new as $id => $mig) {
                    if (!empty($mig[3])) {
                        if (in_array(strtolower($filer_tag), $mig[3])) {
                            $newArrayList[$id] = $mig;
                        }
                    }
                }
                if (!empty($newArrayList)) {
                    $return_array_new = $newArrayList;
                } else {
                    $return_array_new = array();
                }
            }

            if (empty($return_array_new)){
                $this->info("New migrations list is empty.");
                return false;
            }

            $time_start = microtime(true);
            $this->info(" -> Start applying migration:");
            $this->writeln('');
            foreach ( $return_array_new as $id => $mig) {
                include_once "" . $mig[1] . "";
                # check bim migration.
                if ((method_exists($mig[0], "up"))) {
                    try {

                        $DB->StartTransaction();

                        # call up function
                        if (false !== $mig[0]::up()) {
                            if (!Bim\Db\Entity\MigrationsTable::isExistsInTable($id)) {
                                if (Bim\Db\Entity\MigrationsTable::add($id)) {

                                    $DB->Commit();

                                    $this->writeln($this->color("     - applied   : " . $mig[2], Colors::GREEN));
                                } else {

                                    $DB->Rollback();

                                    throw new Exception("add in migration table error");
                                }
                            }
                        } else {
                            $this->writeln(Colors::colorize("     - error : " . $mig[2], Colors::RED) . " " . Colors::colorize("(Method Up return false)", Colors::YELLOW));
                        }
                    } catch (Exception $e) {

                        if ((isset($options['debug']))) {
                            $debug = "[" . $e->getFile() . ">" . $e->getLine() . "] ";
                        } else {
                            $debug = "";
                        }

                        $DB->Rollback();

                        $this->writeln(Colors::colorize("     - error : " . $mig[2], Colors::RED) . " " . Colors::colorize("( ".$debug."". $e->getMessage() . ")", Colors::YELLOW));
                    }
                }
            }
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            $this->writeln('');
            $this->info(" -> ".round($time, 2)."s");
        }
    }


}
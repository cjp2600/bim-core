<?php

use ConsoleKit\Widgets\ProgressBar,ConsoleKit\Colors;

class ListCommand extends BaseCommand
{
    public function execute(array $args, array $options = array())
    {
        $list = $this->getDirectoryTree($this->getMigrationPath(), "php");

        # get filename
        $file = (isset($options['f'])) ? true : false;
        $filter_apply = (isset($options['a'])) ? $options['a'] : false;
        $filter_new = (isset($options['n'])) ? $options['n'] : false;

        if (!empty($list)) {

            $headers = array('№', 'id', 'Author', 'Date');
            if ($file) {
                $headers[] = 'File';
            }

            $headers[] = 'Description';
            $headers[] = 'Status';

            $table = new \cli\Table();
            $table->setHeaders($headers);

            $count = 0;
            $applied = 0;
            $new = 0;
            $i = 1;
            $return_array_new = array();
            $return_array_apply = array();

            $progress = new ProgressBar($this->console, count($list));
            foreach ($list as $id => $data) {
                $progress->incr();
                $count++;

                $row = $data['file'];
                $name = $data['name'];

                # check in db
                $is_new = (!$this->checkInDb($id));
                $class_name = $this->camelCase($name);
                include_once "" . $this->getMigrationPath() . $row . "";

                $color = ConsoleKit\Colors::GREEN;
                $status = ConsoleKit\Colors::colorize('apply', Colors::GREEN);

                # check in db
                if ($is_new) {
                    $new++;
                    $color = ConsoleKit\Colors::RED;
                    $status = ConsoleKit\Colors::colorize('new', Colors::RED);
                } else {
                    $applied++;
                }

                $rowArray = array(
                    ConsoleKit\Colors::colorize($i, $color),
                    ConsoleKit\Colors::colorize($id, $color),
                    (method_exists($class_name, "getAuthor")) ? $class_name::getAuthor() : "",
                    date("d.m.y G:h", $data['date'])
                );
                if ($file) {
                    $rowArray[] = $row;
                }
                $rowArray[] = (method_exists($class_name, "getDescription")) ? $class_name::getDescription() : "";
                $rowArray[] = $status;

                if ($is_new) {
                    $return_array_new[] = $rowArray;
                } else {
                    $return_array_apply[] = $rowArray;
                }

                $i++;
            }

            if ($filter_new) {
                $table->setRows($return_array_new);
            } else if ($filter_apply) {
                $table->setRows($return_array_apply);
            } else {
                $table->setRows(array_merge($return_array_apply,$return_array_new));
            }

            $progress->stop();
            $table->display();

            # count info
            $return[] = Colors::colorize('New:', Colors::RED) . " " . $new;
            $return[] = Colors::colorize('Applied:', Colors::GREEN) . " " . $applied;
            $return[] = "Count: " . $count;

            # display
            $this->padding(implode(PHP_EOL, $return));

        } else {
            $this->info('Empty list');
        }
    }

}

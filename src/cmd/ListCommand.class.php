<?php

use ConsoleKit\Widgets\ProgressBar,ConsoleKit\Colors;

class ListCommand extends BaseCommand {

    public function execute(array $args, array $options = array())
    {
        $list = $this->getDirectoryTree( $this->getMigrationPath(), "php" );

        if (!empty($list)) {

            $headers = array(
                '№',
                'id',
                'Author',
                'Date',
                'Class',
                'File',
                'Description',
                'Status'
            );

            $table = new \cli\Table();
            $table->setHeaders($headers);

            $count = 0;
            $applied = 0;
            $new = 0;
            $i = 1;
            $progress = new ProgressBar($this->console, count($list));
            foreach ($list as $id => $row) {
                $progress->incr();
                $count ++;

                $date = explode("_",$id);
                if (isset($date[0])){
                    $format = $date[0];
                }

                $class_name = $this->camelCase($id);
                include_once "".$this->getMigrationPath() . $row."";

                $color = ConsoleKit\Colors::GREEN;
                $status = ConsoleKit\Colors::colorize('apply', Colors::GREEN);

                # проверка на установку
                if ($i == 3 || $i == count($list)){
                    $new ++;
                    $color = ConsoleKit\Colors::RED;
                    $status = ConsoleKit\Colors::colorize('new', Colors::RED);
                } else {
                    $applied ++;
                }

                #id
                $id = (isset($date[1])) ? $date[1] : "";

                $table->addRow(array(
                    ConsoleKit\Colors::colorize($i,$color),
                    $id,
                    (method_exists($class_name,"getAuthor")) ? $class_name::getAuthor() : "",
                    date("d.m.y G:h:i",$format),
                    $class_name,
                    $row,
                    (method_exists($class_name,"getDescription")) ? $class_name::getDescription() : "",
                    $status
                ));

                $table->addRow(array('','','','','','','',''));

            $i++;}
            $progress->stop();
            $table->display();

            # count info
            $return[] = Colors::colorize('New:', Colors::RED)." ".$new;
            $return[] = Colors::colorize('Applied:', Colors::GREEN)." ".$applied;

            # display
            $this->padding(implode(PHP_EOL,$return));

        } else {
            $this->writeln('');
            $this->info('Пусто');
            $this->writeln('');
        }

        
    }

}

<?php

use ConsoleKit\Widgets\ProgressBar,ConsoleKit\Colors;

class ListCommand extends BaseCommand {

    public function execute(array $args, array $options = array())
    {
        $list = $this->getDirectoryTree( $this->getMigrationPath(), "php" );

        if (!empty($list)) {

            $headers = array(
                '№',
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
                if ($i == 3){
                    $new ++;
                    $color = ConsoleKit\Colors::RED;
                    $status = ConsoleKit\Colors::colorize('new', Colors::RED);
                } else {
                    $applied ++;
                }

                $table->addRow(array(
                    $this->color($i,$color),
                    (method_exists($class_name,"getAuthor")) ? $this->color($class_name::getAuthor(),$color) : "",
                    $this->color(date("d.m.y G:h:i",$format),$color),
                    $this->color($class_name,$color),
                    $this->color($row,$color),
                    (method_exists($class_name,"getDescription")) ? $this->color($class_name::getDescription(),$color) : "",
                    $status
                ));

                //$table->addRow(array('','','',''));

            $i++;}
            $progress->stop();
            $table->display();

            # count info
            $return[] = Colors::colorize('New:', Colors::RED)." 1";
            $return[] = Colors::colorize('Applied:', Colors::GREEN)." 10";

            # display
            $this->padding(implode(PHP_EOL,$return));

        } else {
            $this->writeln('');
            $this->info('Пусто');
            $this->writeln('');
        }

        
    }

}

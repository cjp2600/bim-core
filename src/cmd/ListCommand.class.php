<?php

use ConsoleKit\Widgets\ProgressBar;

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

            $i = 1;
            $progress = new ProgressBar($this->console, count($list));
            foreach ($list as $id => $row) {
                $progress->incr();

                $date = explode("_",$id);
                if (isset($date[0])){
                    $format = $date[0];
                }

                $class_name = $this->camelCase($id);
                include_once "".$this->getMigrationPath() . $row."";

                $color = ConsoleKit\Colors::GREEN;

                $table->addRow(array(
                    $this->color($i,$color),
                    (method_exists($class_name,"getAuthor")) ? $this->color($class_name::getAuthor(),$color) : "",
                    $this->color(date("d.m.y G:h:i",$format),$color),
                    $this->color($class_name,$color),
                    $this->color($row,$color),
                    (method_exists($class_name,"getDescription")) ? $this->color($class_name::getDescription(),$color) : "",
                    ""
                ));

                //$table->addRow(array('','','',''));

            $i++;}
            $progress->stop();
            $table->display();

        } else {
            $this->writeln('');
            $this->info('Пусто');
            $this->writeln('');
        }

        
    }

}

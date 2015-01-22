<?php


/**
 * Class CreateCommand
 */
class CreateCommand extends BaseCommand {

    public function execute(array $args, array $options = array())
    {
        try{
            $this->{"create".ucfirst($args[0])}($args,$options);
        }catch (Exception $e) {
            throw new Exception("missing command, see help Example: php help create");
        }
    }

    /**
     * createIblock
     * @param array $args
     * @param array $options
     */
    public function createIblock(array $args, array $options = array())
    {
        $this->success("iblock");
    }

}

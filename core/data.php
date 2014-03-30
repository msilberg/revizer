<?php
/**
 * Created by PhpStorm.
 * User: mike
 */

abstract class Data
{
    /**
     * @var string
     * describes the operation that needs to be done
     */
    private $action;

    /**
     * @var string
     * name of the variable
     */
    protected $var;

    /**
     * @var mixed
     * value of the variable
     */
    protected $val;

    public function __construct($arg)
    {
        if (count($arg) > 1)
        {
            $this->action = $arg[1];
            $this->var = (isset($arg[2]))? $arg[2] : null;
            $this->val = (isset($arg[3]))? $arg[3] : null;
        }
        else die ("None of the commands was entered.\nPlease specify one of supported commands, e.g. get, set, increment or flush.\n");
    }

    public function do_action()
    {
        switch ($this->action)
        {
            case 'get': $this->get_var();
                break;
            case 'set': $this->set_var();
                break;
            case 'increment': $this->increment();
                break;
            case 'flush': $this->flush();
                break;
            default: die("Unsupported action requested. Please specify one of supported commands, e.g. get, set, increment or flush.\n");
        }
    }

    /**
     * outputs a value of the requested variable
     */
    abstract function get_var();

    /**
     * sets a value for the specified variable both for the existing and new ones
     */
    abstract function set_var();

    /**
     * increments a value of the variable with a specified number
     * variable's type must be integer only to run this function
     */
    abstract function increment();

    /**
     * deletes all the cache stored in the data file
     */
    abstract function flush();

}

class PerformData extends Data
{
    private $file_address = 'storage/data.json';
    private $file_content;

    public function __construct($arg)
    {
        parent::__construct($arg);
        $this->file_content = json_decode(file_get_contents($this->file_address, true));
    }

    public function get_var()
    {
        foreach (explode(',',$this->var) as $val)
            print (
                (isset($this->file_content->{$val}))?
                    "\"$val\": {$this->file_content->{$val}}" :
                    "The variable \"{$this->var}\" is not existing. Please specify it using \"set\" command."
                )."\n";
    }

    public function set_var()
    {
        $file = (array)$this->file_content;
        $file[$this->var] = (is_numeric($this->val))? (integer)$this->val : (string)$this->val;
        file_put_contents($this->file_address, json_encode($file));
        print "The variable \"{$this->var}\" has been successfully set to \"{$this->val}\".\n";
    }

    public function increment()
    {
        if (
            !isset($this->val) ||
            !is_numeric($this->val) ||
            !is_numeric($this->file_content->{$this->var})
        ) die("Both incrementing values must be a numbers only.\n");
        $this->file_content->{$this->var} += intval($this->val);
        file_put_contents($this->file_address, json_encode((array)$this->file_content));
        print "The variable \"{$this->var}\" has been successfully incremented to {$this->file_content->{$this->var}}.\n";
    }

    public function flush()
    {
        print "Are you sure you want to do this? Type 'yes' to continue: ";
        $handle = fopen ("php://stdin","r");
        $line = fgets($handle);
        if(trim($line) != 'yes') die("Aborting!\n");
        $file = fopen($this->file_address, 'r+');
        ftruncate($file, 0);
        fclose($file);
        print "All the cache was deleted...\n";
    }
}
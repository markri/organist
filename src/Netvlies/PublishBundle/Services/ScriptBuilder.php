<?php
/**
 * @author: M. de Krijger
 * Creation date: 20-12-11
 */
namespace Netvlies\PublishBundle\Services;


class ScriptBuilder
{
    protected $scriptBasePath;
    protected $scriptPath;
    protected $script;
    protected $uid;


    /**
     * Constructor
     * Will create temp directory where scriptbuilder scripts are stored
     * It defaults to app/cache/scripts directory
     */
    public function __construct(){
        $this->uid = md5(time().rand(0, 10000));
        $root = dirname(dirname(dirname(dirname(__DIR__))));
        $this->scriptBasePath = $root.'/app/cache/scripts';

        if(!file_exists($this->scriptBasePath)){
            mkdir($this->scriptBasePath);
        }

        if(!file_exists($this->scriptBasePath)){
            throw new \Exception('Couldnt create script directory');
        }

        $this->createScriptFile($this->uid);
    }

    /**
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }


    /**
     * Add chunk of script (string)
     * @param $scriptChunk
     */
    public function addScript($scriptChunk){
        $this->script.=$scriptChunk."\n";
        $this->writeScript();
    }

    /**
     * Write a single line, which will be terminated with UNIX EOL
     * @param $line
     */
    public function addLine($line)
    {
        $this->script.=$line."\n";
        $this->writeScript();
    }

    /**
     * Internal method to write the script
     */
    protected function writeScript()
    {
       // $script = 'cd '.$this->workingDirectory. "\n".$this->script;
        file_put_contents($this->scriptPath, $this->script);
    }

    /**
     * @return string Get absolute path to the written script
     */
    public function getScriptPath()
    {
        return $this->scriptPath;
    }



    public static function getUIDFromPath($sPath)
    {
        return basename($sPath);
    }


    /**
     * Initial creation of the scriptfile (unique filename)
     */
    protected function createScriptFile($uid)
    {
        $this->scriptPath = $this->scriptBasePath.'/'.$uid;
        touch($this->scriptPath);
        shell_exec('chmod 777 '.$this->scriptPath);
    }

}

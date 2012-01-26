<?php
/**
 * @author: M. de Krijger
 * Creation date: 20-12-11
 */
namespace Netvlies\PublishBundle\Entity;

class ScriptBuilder
{

    protected $scriptBasePath;
    protected $scriptPath;
    protected $script;
    protected $workingDirectory;
    protected $uid;


    /**
     * Constructor
     * Will create temp directory where scriptbuilder scripts are stored
     * It defaults to app/cache/scripts directory
     */
    public function __construct($uid){
        $this->uid = $uid;
        $root = dirname(dirname(dirname(dirname(__DIR__))));
        $this->scriptBasePath = $root.'/app/cache/scripts';
        if(!file_exists($this->scriptBasePath)){
            mkdir($this->scriptBasePath);
        }

        if(!file_exists($this->scriptBasePath)){
            throw new Exception('Couldnt create script directory');
        }

        $this->createScriptFile($uid);
        $this->workingDirectory = $root;
    }


    public function getUid(){
        return $this->uid;
    }


    /**
     * Returns encoded script url for updating local git repository. Which can be used as a link to the consolecontroller
     * @return string
     */
//    public function getGitUpdateScript($application){
//        //$scriptBuilder = new ScriptBuilder(time());
//        $this->setWorkingDirectory($application->getAbsolutePath());
//        $this->addLine('git pull --all');
//        return $this->getEncodedScriptPath();
//    }

    /**
     * Add chunk of script (string)
     * @param $scriptChunk
     */
    public function addScript($scriptChunk){
        $this->script.=$scriptChunk;
        $this->writeScript();
    }

    /**
     * Write a single line, which will be terminated with UNIX EOL
     * @param $line
     */
    public function addLine($line){
        $this->script.=$line."\n";
        $this->writeScript();
    }

    /**
     * Internal method to write the script
     */
    protected function writeScript(){
        $script = 'cd '.$this->workingDirectory. "\n".$this->script;
        file_put_contents($this->scriptPath, $script);
    }

    /**
     * @return string Get absolute path to the written script
     */
    public function getScriptPath(){
        return $this->scriptPath;
    }

    /**
     * Returns base64 encoded scriptpath, suitable for usage in URL
     * @return string
     */
    public function getEncodedScriptPath(){
        return base64_encode($this->getScriptPath());
    }

    /**
     * Optionally override workingdirectory
     * @param $workingDirectory
     */
    public function setWorkingDirectory($workingDirectory){
        $this->workingDirectory = $workingDirectory;
    }


    public static function getUIDFromPath($sPath){
        return basename($sPath);
    }


    /**
     * Initial creation of the scriptfile (unique filename)
     */
    protected function createScriptFile($uid){
        $this->scriptPath = $this->scriptBasePath.'/'.$uid;
        touch($this->scriptPath);
        //$this->scriptPath = tempnam($this->scriptBasePath, '');
        shell_exec('chmod 777 '.$this->scriptPath);
    }

}

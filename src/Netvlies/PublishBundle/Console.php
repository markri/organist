<?php

namespace Netvlies\PublishBundle;

use Netvlies\PublishBundle\Ansi2Html;

class Console {

    protected $bInit = false;
    
    public function __construct(){

    }
    
    protected function init(){
        
        if($this->bInit){
            return;
        }
        
        // Register a shutdown function to close all HTML
        register_shutdown_function(array($this, 'printConsoleFooter'));

        // Use a custom error handler to display errors in the appropiate layout
        set_error_handler(array($this, 'handleError'));        
        
        // Never interrupt script
        ignore_user_abort(true);
        
        // Allow to run script forever
        set_time_limit(0);       
        
     
        // Flush implicitly
        ob_implicit_flush(true);
        ob_end_clean();

        // Print the header HTML before any actions are taken
        $this->printConsoleHeader();   
        $this->bInit = true;
    }
    
    /**
     * Prints the console header
     *
     * @return void
     */
    protected function printConsoleHeader() {
        echo '
        <html>
        	<head>
            
        		<script type="text/javascript">
        			var scrolling = false;
                    
        			function scrollToBottom() {
        				window.scroll(0, document.body.scrollHeight);
        				if (scrolling) {
        					setTimeout("scrollToBottom()", 100);
        				}
        			}
                    
        			function scrollStart() {
        				scrolling = true
        				scrollToBottom();
        			}
                    
        			function scrollStop() {
        				scrolling = false
        			}
                    
        			parent.document.getElementById(\'consoleClose\').style.display = "none";
        		</script>
                
        		<style type="text/css">
        			html, body {
        				margin: 0;
        				padding: 0;
        			}
        			body {
        				padding: 1px;
        				background-color: #000000;
        				font: 14px "Courier New";
        				color: #ffffff;
        				cursor: wait;
        			}
        			
        			.consolelinepart {
        				white-space: nowrap;
        			}
        		</style>
        	</head>
        	<body>
        		<script type="text/javascript">scrollStart();</script>';
    }

    /**
     * Prints the page footer
     *
     * Is called automatically by register_shutdown_function
     *
     * @return void
     */
    public function printConsoleFooter() {
        echo '
        		<script type="text/javascript">
        			scrollStop();
        			document.body.style.cursor = "auto";
        			parent.document.getElementById(\'consoleClose\').style.display = "block";
        		</script>
        	</body>
        </html>';
    }

    /**
     * Custom errorHandler to display errors inside the console
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     */
    protected function handleError($errno, $errstr, $errfile, $errline) {
        $sMessage = '<strong>PHP ERROR: ' . $errstr . "</strong> ($errfile:$errline)";
        $this->printLine($sMessage, '#ff0000', '#ffffff');
        debug_print_backtrace();
        exit(0);
    }

    /**
     * Prints a line to the console
     *
     * @param string $sMessage
     * @param string $sBgColor
     * @param string $sFontColor
     */
    protected function printLine($sMessage, $sBgColor = '#000000', $sFontColor = '#ffffff') {
        
        if($sBgColor == '#000000' && $sFontColor = '#ffffff'){
            
            $sMessage = str_replace("\x1B", '', $sMessage);
            $oAnsi = Ansi2Html::getInstance();
            echo $oAnsi->convertLine($sMessage);
        }
        else{
            echo '<div class="output" style="background:' . $sBgColor . '; color: ' . $sFontColor . '">' . $sMessage . '</div>';
        }

    }


    protected function printStart($sMessage) {
        $this->printLine($sMessage, '#666666');
    }


    protected function printError($sMessage) {
        $this->printLine($sMessage, '#ff0000', '#ffff00');
    }


    protected function printOk($sMessage) {
        $this->printLine($sMessage, '#008000');
    }


    /**
     * This will execute the phing target
     * @param string $sCmd
     */
    public function execute($sCmd, $bLastCommand = true) {

        $this->init();
        
        // Ignore user abort. Will be catched when iterating through the stages
        $fTimeStart = microtime(true);

        // Print stage header
        $this->printStart("Executing: $sCmd");
        $this->printLine('<br>');

        if (ob_get_level() == 0) {
            ob_start();
        }

        $rShell = popen($sCmd.' 2>&1; echo exitcode:$?', 'r');
        $sLineBuffer = '';
        $iExitCode = 0;

        while (!feof($rShell)) {

            $sChar = fread($rShell, 1);

            if (ord($sChar) == 10) {
                // line ending
                if(false!==strpos($sLineBuffer, 'exitcode:')){
                    $aExitCode = explode(':', $sLineBuffer);
                    $iExitCode = $aExitCode[1];
                    $sLineBuffer = '';
                }
                else{
                    $this->printLine($sLineBuffer);
                    $sLineBuffer = '';
                }
            } else {
                $sLineBuffer.=$sChar;
            }

            ob_flush();
            flush();
        }

        $this->printLine($sLineBuffer . '<br>');        
        pclose($rShell);

        $fTimeEnd = microtime(true);
        $fTimeDelta = $fTimeEnd - $fTimeStart;

        if (connection_aborted()) {
            $this->printError('ERROR: The client aborted.'); 
            exit(1);
        }

        if($iExitCode == 0){
            $this->printOk(sprintf('Command ' . $sCmd . ' was succesfully executed in %.3f seconds', $fTimeDelta));
        }
        else{
            $this->printError('Shell returned unsuccesfull exit code '.$iExitCode);
        }
        
        if($bLastCommand){
            $this->stopReadingTty();
        }
    }
    
    
    public function stopReadingTty(){
        // printPageFooter is called by register_shutdown_function
        exit(0);
    }
    
    
    public function getHeadTags(){
        return '
        <style type="text/css">
            #consoleMessage {
                background-color: #000000;
                border: 3px solid #C0C0C0;
                display: none;
                height: 624px;
                left: 50%;
                margin-left: -400px;
                margin-top: -125px;
                position: absolute;
                top: 50%;
                width: 1000px;
                z-index: 1400;
            }
            #consoleMelding {
                background-color: #0262F6;
                color: #FFFFFF;
                font-weight: bold;
                padding: 5px;
                position: relative;
            }
            #consoleClose {
                background-color: #E54D1F;
                color: #FFFFFF;
                cursor: pointer;
                display: block;
                height: 10px;
                line-height: 10px;
                padding: 5px;
                position: absolute;
                right: 2px;
                text-align: center;
                top: 2px;
                width: 10px;
            }
            div#consoleMeldingen {
                background-color: black;
                color: #FFFFFF;
                font-family: courier;
                font-size: 8px !important;
                height: 600px;
                margin-left: 5px;
                overflow-x: hidden;
                overflow-y: scroll;
                position: relative;
            }
            div#consoleMeldingen h2, div#consoleMeldingen span {
                font-family: courier;
                font-size: 8px !important;
            }
            iframe#consoleMeldingenFrame {
                border-width: 0;
                height: 600px;
                margin: 0;
                overflow-x: auto;
                overflow-y: scroll;
                padding: 0;
                position: relative;
                width: 100%;
            }
            .consoleHeader {
                background-color: red;
                color: #FFFFFF;
                margin: 10px 0 0;
                padding: 0;
            }
         </style>
         <script type="text/javascript">
                function showConsole(url) {
                    if(confirm(\'Weet u zeker dat u deze target wilt uitvoeren?\')){
                        document.getElementById("consoleMessage").style.display="block";
                        document.getElementById("consoleMeldingen").style.display="none";
                        document.getElementById("consoleMeldingenFrame").style.display="block";
                        document.getElementById("consoleMeldingenFrame").src=url;

                        return true;
                    }
                }

                function hideConsole(){
                    document.getElementById(\'consoleMessage\').style.display="none";
                    document.getElementById(\'consoleMeldingen\').html="";
                }  
         </script>
         ';        
    }
    
    public function getConsoleDivTag(){
        return '
                 <div id="consoleMessage" style="position: absolute; display: none; ">
                    <div id="consoleMelding">Console<div id="consoleClose" onclick="hideConsole();" style="display: block; ">X</div></div>
                    <div id="consoleMeldingen" style="display: none; "></div>
                    <iframe id="consoleMeldingenFrame" style="display: block;"></iframe>
                </div>';
    }
    

}

?>
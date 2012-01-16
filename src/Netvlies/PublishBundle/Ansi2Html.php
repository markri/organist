<?php

namespace Netvlies\PublishBundle;

class Ansi2Html{
    
    
    protected static $oInstance;
    
    protected $sDefaultTextColor = '#c0c0c0';
    protected $sDefaultBackgroundColor = '#000000';
    protected $aNormalTable = array('#000000', '#800000', '#008000', '#808000', '#000080', '#800080', '#008080', '#c0c0c0');
    protected $aBrightTable = array('#808080', '#ff0000', '#00ff00', '#ffff00', '#0000ff', '#ff00ff', '#00ffff', '#ffffff');

    // Status dependant ANSI attributes
    protected $sCurrentTextColor = '';
    protected $sCurrentBackgroundColor = '';    
    protected $bBright = false;
    protected $bItalic = false;
    protected $bUnderline = false;
    protected $bOverline = false;
    protected $bBlinking = false;
    
    
    
    protected function __construct(){
        $this->reset();
    }
    
    protected function reset(){
        $this->sCurrentTextColor = $this->sDefaultTextColor;
        $this->sCurrentBackgroundColor = $this->sDefaultBackgroundColor;
        $this->bBright = false;
        $this->bItalic = false;
        $this->bUnderline = false;
        $this->bOverline = false;
        $this->bBlinking = false;
    }
    
    
    public static function getInstance(){
        
        if(is_null(self::$oInstance)){
            self::$oInstance = new self();
        }
        
        return self::$oInstance;
    }
    
    
    public function convertLine($sLine){
        
        // Otherwise regex would fail for not finding an int
        $sLine = str_replace('[m', '[0m', $sLine);
        
        $sReturn = '<div class="consoleline">';
        $sReturn.=$this->processLine(array(), true);
        $sReturn.=preg_replace_callback('#\[(\d*?)(;(\d*?))?m#', array($this, 'processLine'), $sLine);
        $sReturn.='</div></div>';
        echo $sReturn;
    }
    
    protected function processLine($aMatches, $bFirstLine=false){
        
        if(isset($aMatches[1])){
            $this->setCode($aMatches[1]);
        }
        if(isset($aMatches[3])){
            $this->setCode($aMatches[3]);
        }
        
        $aTextDecorations = array();
        
        if($this->bBlinking){
            $aTextDecorations[] = 'blink';
        }
        if($this->bUnderline){
            $aTextDecorations[] = 'underline';
        }
        if($this->bOverline){
            $aTextDecorations[] = 'overline';
        }
        
        if(empty($aTextDecorations)){
            $aTextDecorations[] = 'none';
        }
        
        $aFontStyles = array();
        
        if($this->bItalic){
            $aFontStyles[] = 'italic';
        }
        
        if(empty($aFontStyles)){
            $aFontStyles[] = 'normal';
        }
        
        $sPrefix = '';
        if(!$bFirstLine){
            $sPrefix = '</div>';
        }
        
        return $sPrefix.'<div class="consolelinepart" style="background:'.$this->sCurrentBackgroundColor.'; color:'.$this->sCurrentTextColor.'; text-decoration: '.implode(' ', $aTextDecorations).'; font-style:'.implode(' ', $aFontStyles).'">';
    }
    
    protected function setCode($iCode){
        
        // http://en.wikipedia.org/wiki/ANSI_escape_code
        $iCode = intval($iCode);

        switch($iCode){
            case 0:
                $this->reset();
                break;
            case 1:
                $this->bBright = true;
                break;
            case 3: 
                $this->bItalic = true;
                break;
            case 4:
                $this->bUnderline = true;
                break;
            case 5:
            case 6:
                $this->bBlinking = true;
                break;
            case 7:
                $sSwap = $this->sCurrentTextColor;
                $this->sCurrentTextColor = $this->sCurrentBackgroundColor;
                $this->sCurrentBackgroundColor = $sSwap;
                break;
            case 22:
                $this->bBright = false;
                break;
            case 23:
                $this->bItalic = false;
                break;
            case 24:
                $this->bUnderline = false;
                break;
            case 25:
                $this->bBlinking = false;
                break;
            case 30:
            case 31:
            case 32:
            case 33:
            case 34:
            case 35:
            case 36:
            case 37:
                $iPosition = $iCode - 30;
                if($this->bBright){
                    $this->sCurrentTextColor = $this->aBrightTable[$iPosition];
                }
                else{
                    $this->sCurrentTextColor = $this->aNormalTable[$iPosition];
                }
                break;
            case 39:
                $this->sCurrentTextColor = $this->sDefaultTextColor;
                break;
            case 40:
            case 41:
            case 42:
            case 43:
            case 44:
            case 45:
            case 46:
            case 47:
                
                $iPosition = $iCode - 40;
                if($this->bBright){
                    $this->sCurrentBackgroundColor = $this->aBrightTable[$iPosition];
                }
                else{
                    $this->sCurrentBackgroundColor = $this->aNormalTable[$iPosition];
                }
                break;
            case 49:
                $this->sCurrentBackgroundColor = $this->sDefaultBackgroundColor;
                break;
            case 53:
                $this->bOverline = true;
                break;
            case 55:
                $this->bOverline = false;
                break;
            default:
                break;
        }
        
    }
    
    
    
}

?>

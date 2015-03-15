<?php
namespace Chop;
use stdClass, \Oda\OdaPrepareReqSql, \Oda\OdaLibBd;
//--------------------------------------------------------------------------
//Header
require("../API/php/header.php");
require("../php/ChopInterface.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new \Oda\OdaPrepareInterface();
$params->arrayInput = array("keyScenar","lang");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/getScenarioInPdf.php?milis=123450&keyScenar=BPAD-EXER&lang=FR

//--------------------------------------------------------------------------
$text = "monwarkermark";
$pathwaterMark = "../img/watermark/waterMark140901.png";

$CHOP_INTERFACE->createImgWaterMark($text, $pathwaterMark);
        
//--------------------------------------------------------------------------

// convert in PDF
require(dirname(__FILE__).'/../API/php/HTML2PDF/html2pdf.class.php');
try
{
    $now = new \DateTime();
    $content = "";
    
    //ELT DE BASE
    $params = new stdClass();
    $params->nameObj = "tab_scenario_def";
    $params->keyObj = ["key" => $CHOP_INTERFACE->inputs["keyScenar"]];
    $defScenar = $CHOP_INTERFACE->BD_ENGINE->getSingleObject($params);
    
    $params = new stdClass();
    $params->nameObj = "tab_elements";
    $params->keyObj = ["id" => $defScenar->titre_element];
    $retour = $CHOP_INTERFACE->BD_ENGINE->getSingleObject($params);
    $keyTitre = $retour->key;
    
    $myContent = new stdClass();
    $myContent->data = "[[".$keyTitre."]]";
    $myContent->lang = $CHOP_INTERFACE->inputs["lang"];
    $myContent->mode = "pdf";
    $obj = $CHOP_INTERFACE->functionDeepTrad($myContent);
    $titre_scenar = $obj->data;
    
    $namefichier = strtr($titre_scenar, 
     'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ$&#%!§', 
     'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy------');
    $namefichier = preg_replace('/([^.a-z0-9]+)/i', '-', $namefichier);
    $namefichier = addslashes($namefichier.'_'.$now->format('YmdHis').'.pdf');
    
    $params = new stdClass();
    $params->nameObj = "tab_elements";
    $params->keyObj = ["id" => $defScenar->resume_element];
    $retour = $CHOP_INTERFACE->BD_ENGINE->getSingleObject($params);
    $keyResume = $retour->key;
    
    $myContent = new stdClass();
    $myContent->data = "[[".$keyResume."]]";
    $myContent->lang = $CHOP_INTERFACE->inputs["lang"];
    $myContent->mode = "pdf";
    $obj = $CHOP_INTERFACE->functionDeepTrad($myContent);
    $resume_scenar = $obj->data;
    
    //ENTENTE DOC
    $content .= "<page>".PHP_EOL;
    $content .= '<br><br><br><br><br><br><br><br><br><br><br><br><p style="text-align: center;"><img src="../img/log_text_112h.png"></p>'.PHP_EOL;
    $content .= '<br><br><br><br><br><br><table style="width:180mn"><tr><td style="width:90mm;">&nbsp;</td><td style="text-align:right;width:90mm;"><h1>Bonita BPM</h1><h4>6.3.3 - '.$now->format('d/m/Y').'</h4></td></tr></table>'.PHP_EOL;
    $content .= '<br><br><br><br><br><br><table style="width:180mn"><tr><td style="width:90mm;">&nbsp;</td><td style="text-align:right;width:90mm;"><h1>'.$titre_scenar.'</h1><h2>'.$resume_scenar.'</h2></td></tr></table>'.PHP_EOL;
    $content .= "</page>".PHP_EOL;
    
    //BUILD SUMMARY
    $params = new stdClass();
    $params->keyScenar = $CHOP_INTERFACE->inputs["keyScenar"];
    $objectSommaire = $CHOP_INTERFACE->buildSommaire($params);
    $content .= '<page backtop="30mm" backbottom="20mm">'.PHP_EOL;
    $content .= '<page_header>'.PHP_EOL;
    $content .= '<table style="width:200mn"><tr><td style="width:100mm;"><img src="../img/log_text_20h.png"></td><td style="text-align:right;width:100mm;">'.$titre_scenar.'</td></tr></table>'.PHP_EOL;
    $content .= '</page_header>'.PHP_EOL;
    $content .= '<br><br><br><br><br><br><p>Summary</p>'.PHP_EOL;
    foreach ($objectSommaire as $keySom => $valueSom){
        if($keySom == "modules"){
            $modules = $valueSom;
            $content .= "<ol>".PHP_EOL;
            foreach ($modules as $nameModule => $module){
                
                $myContent = new stdClass();
                $myContent->data = "[[".$module->titre_key."]]";
                $myContent->lang = $CHOP_INTERFACE->inputs["lang"];
                $myContent->mode = "pdf";
                $obj = $CHOP_INTERFACE->functionDeepTrad($myContent);
                $titre = $obj->data;
                
                $content .= '<li>'.$titre.PHP_EOL;
                
                foreach ($module as $keyMod => $valueMod){
                    if($keyMod == "pages"){
                        $content .= "<ol>".PHP_EOL;
                        $pages = $valueMod;
                        foreach ($pages as $namePage => $page){
                            
                            $params = new stdClass();
                            $params->contentKey = $namePage;
                            $params->lang = $CHOP_INTERFACE->inputs["lang"];
                            $retour = $CHOP_INTERFACE->getRenderContentPage($params);
                            
                            $content .= '<li>'.$retour->titre.'</li>'.PHP_EOL;
                        }
                        $content .= "</ol>".PHP_EOL;
                    }
                }
                $content .= "</li>".PHP_EOL;
            }
        }
    }
    $content .= "</ol>".PHP_EOL;
    $content .= '<page_footer>'.PHP_EOL;
    $content .= '<table style="width:200mn"><tr><td style="width:100mm;">'.$now->format('d/m/Y').' |  www.bonitasoft.com | &copy; BonitaSoft S.A. </td><td style="text-align:right;width:100mm;">[[page_cu]]/[[page_nb]]</td></tr></table>'.PHP_EOL;
    $content .= '</page_footer>'.PHP_EOL;
    $content .= "</page>".PHP_EOL;
    
    //BUILD CONTENT
    foreach ($objectSommaire as $keySom => $valueSom){
        if($keySom == "modules"){
            $modules = $valueSom;
            foreach ($modules as $nameModule => $module){
                $content .= '<page backtop="30mm" backbottom="20mm" backimg="'.$pathwaterMark.'">'.PHP_EOL;
                $content .= '<page_header>'.PHP_EOL;
                $content .= '<table style="width:200mn"><tr><td style="width:100mm;"><img src="../img/log_text_20h.png"></td><td style="text-align:right;width:100mm;">'.$titre_scenar.'</td></tr></table>'.PHP_EOL;
                $content .= '</page_header>'.PHP_EOL;
                
                $myContent = new stdClass();
                $myContent->data = "[[".$module->titre_key."]]";
                $myContent->lang = $CHOP_INTERFACE->inputs["lang"];
                $myContent->mode = "pdf";
                $obj = $CHOP_INTERFACE->functionDeepTrad($myContent);
                $titre = $obj->data;
                
                $content .= '<p style="text-align: center;color:red;font-size:20px;">'.$titre.'</p>'.PHP_EOL;
                
                $myContent = new stdClass();
                $myContent->data = "[[".$module->resume_key."]]";
                $myContent->lang = $CHOP_INTERFACE->inputs["lang"];
                $myContent->mode = "pdf";
                $obj = $CHOP_INTERFACE->functionDeepTrad($myContent);
                $resume = $obj->data;
                
                $content .= '<p style="text-align: center;"><i>'.$resume.'</i></p>'.PHP_EOL;
                
                $content .= '<br><br><br><br>'.PHP_EOL;
                
                foreach ($module as $keyMod => $valueMod){
                    if($keyMod == "pages"){
                        $pages = $valueMod;
                        foreach ($pages as $namePage => $page){
                            
                            $params = new stdClass();
                            $params->contentKey = $namePage;
                            $params->lang = $CHOP_INTERFACE->inputs["lang"];
                            $retour = $CHOP_INTERFACE->getRenderContentPage($params);
                            
                            $content .= '<b>'.$retour->titre.'</b>'.PHP_EOL;
                            
                            $content .= ''.$retour->content.''.PHP_EOL;
                            
                            $content .= '<br><br>'.PHP_EOL;
                        }
                    }
                }
                
                $content .= '<page_footer>'.PHP_EOL;
                $content .= '<table style="width:200mn"><tr><td style="width:100mm;">'.$now->format('d/m/Y').' | www.bonitasoft.com | &copy; BonitaSoft S.A. </td><td style="text-align:right;width:100mm;">[[page_cu]]/[[page_nb]]</td></tr></table>'.PHP_EOL;
                $content .= '</page_footer>'.PHP_EOL;
                $content .= "</page>".PHP_EOL;
            }
        }
    }

    if($CHOP_INTERFACE->modeDebug){
        var_dump($content);
    }else{
        $html2pdf = new \HTML2PDF('P','A4','fr');
        $html2pdf->WriteHTML($content);
        $html2pdf->Output($namefichier);
    }
}
catch(HTML2PDF_exception $e) {
    echo $e;
    exit;
}

<?php

class BlaatSchaap {

    // TODO: dependency tester for example class_exists(SimpleXMLElement);



  public function GenerateOptions($options, $action=NULL, $values=NULL, $echo=true) {
    // how to handle id on edit // hidden fields
    // rename the colums in the database?

    // sort the options in tabs
    // general / oauth / api / hidden
    // --> This requires also some JavaScript to support it..
    // --> We also require JavaScript to show/hide required fields <--
   // Well... planned features ... let's get the basics to work first! 



    // what about using XML to generate a form rather then just
    // typing some HTML here?
    $xmlroot = new SimpleXMLElement('<form method="post" />');
    if ($action) $xmlroot->addAttribute("action", $action);

    $xmltable = $xmlroot->addChild("table");  
    foreach ($options as $option) {
    $xmlrow = $xmlroot->addChild("tr");
    $xmlrow->addChild("th", $option['title']);


      // if we don't have $xmlselect $xmltextarea $xmlinput
      // but a single name we can set the common attributes at the same place.
      switch ($option['type']) {
        case "select":
          $xmlselect = $xmlrow->addChild("td")->addChild("select");
          $xmlselect->addAttribute("name",$option['name']);
          $xmlselect->addAttribute("id",$option['name']);     
          foreach($option['options'] as $opt) {
            $xmloption=$xmlselect->addChild("option", $opt['display']);
            $xmloption->addAttribute("value",$opt['value']);
            if ($values && $values[$option['name']]) {
              if ($values[$option['name']]==$opt['value']) $xmloption->addAttribute("selected","true");
            } else
            if ($option['default']==$opt['value']) $xmloption->addAttribute("selected","true");
          } 
          break;
        case "textarea":
          if ($values && isset($values[$option['name']])) {
            $xmltextarea = $xmlrow->addChild("td")->addChild("textarea",$values[$option['name']]);      
          } else {
            $xmltextarea = $xmlrow->addChild("td")->addChild("textarea");
          }
          $xmltextarea->addAttribute("name",$option['name']);
          $xmltextarea->addAttribute("id",$option['name']);    
          break;
        default:
          $xmlinput = $xmlrow->addChild("td")->addChild("input");
          $xmlinput->addAttribute("type",$option['type']);
          $xmlinput->addAttribute("name",$option['name']);
          $xmlinput->addAttribute("id",$option['name']);      
          if ($values && isset($values[$option['name']])) {
            $xmlinput->addAttribute("value",$values[$option['name']]);      
          } 
          if ($option['type']=="checkbox" && $option['default']==true) $xmlinput->addAttribute("checked","true");
      } // end switch
    } // end foreach

    $xmlrow = $xmlroot->addChild("tr");
    $xmlrow->addChild("th");
                                        // TODO:: Distinguise between Add/Update
    $xmlrow->addChild("td")->addChild("button",  __("Save"))->addAttribute("type","submit");

  


    if ($echo) echo $xmlroot->AsXML();
    return $xmlroot;
  }

}



?>

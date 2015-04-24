<?php

class BlaatSchaap {

    // TODO: dependency tester for example class_exists(SimpleXMLElement);



  public function GenerateOptions($tabs, $values=NULL, $action=NULL, $echo=true) {
    // todo tab hiding
    // configuration for submit button?

    $xmlroot = new SimpleXMLElement('<form method="post" />');
    if ($action) $xmlroot->addAttribute("action", $action);
    $xmlmenu = $xmlroot->addChild("menu");
    $xmltabs = $xmlroot->addChild("tabs");    

    $firstTab = true;
    foreach  ($tabs as $tab) {
      $xmlbutton = $xmlmenu->addChild("button", $tab->display);
      $xmlbutton->addAttribute("name", $tab->name ."_btn");
      $xmlbutton->addAttribute("id", $tab->name   ."_btn");
      $xmlbutton->addAttribute("class", "blaatConfigBtn");
      $xmlbutton->addAttribute("onclick", "alert('".$tab->name."')"); // test

      $xmltab = $xmltabs->addChild("tab");    
      $xmltab->addAttribute("name", $tab->name ."_tab");
      $xmltab->addAttribute("id", $tab->name   ."_tab");
      $xmltab->addAttribute("class", "blaatConfigTab shown");

      $xmltable = $xmltab->addChild("table");  

      foreach ($tab->options as $option) {

        $xmlrow = $xmltab->addChild("tr");
        $xmlrow->addChild("th", $option->title);
        switch ($option->type) {
          case "select":
            $xmloption = $xmlrow->addChild("td")->addChild("select");
            foreach($option->options as $opt) {
              $xmlselectoption=$xmloption->addChild("option", $opt->display);
              $xmlselectoption->addAttribute("value",$opt->value);
          // TODO values will be turned into array later
              if ($values && $values[$option->name]) {
                if ($values[$option->name]==$opt->value) $xmlselectoption->addAttribute("selected",true);
              } else
              if ($option->default==$opt->value) $xmlselectoption->addAttribute("selected",true);
            } 
            break;
          case "textarea":
            if ($values && isset($values[$option->name])) {
              $xmloption = $xmlrow->addChild("td")->addChild("textarea",$values[$option->name]);      
            } else {
              $xmloption = $xmlrow->addChild("td")->addChild("textarea");
            }
            break;
          default:
            $xmloption = $xmlrow->addChild("td")->addChild("input");
            $xmloption->addAttribute("type",$option->type);
          if ($option->type=="checkbox") {
            if ($values) { 
              if (isset($values[$option->name]) && $values[$option->name]) 
                $xmloption->addAttribute("checked","true");
              } else if ($option->default==true) $xmloption->addAttribute("checked",true);
          } else {
            if ($values) {
              if(isset($values[$option->name])) {
                $xmloption->addAttribute("value",$values[$option->name]);      
              }
            } elseif ($option->default) $xmloption->addAttribute("value",$option->default); 
          }
        }
        $xmloption->addAttribute("name",$option->name);
        $xmloption->addAttribute("id",$option->name);    
        if ($option->required==true) $xmloption->addAttribute("required",true);
      }
    }  
   if ($echo) echo $xmlroot->AsXML();
    return $xmlroot;
  }

















 
}



?>

<?php

class BlaatSchaap {

    // TODO: dependency tester for example class_exists(SimpleXMLElement);


    
    

  function enqueueAdminJS(){
    wp_enqueue_script("BlaatSchaapJS" , plugin_dir_url(__FILE__) . "../js/BlaatSchaap.js");
  }


  public function isPageRegistered($menu_slug){
    global $_parent_pages;
    return isset($_parent_pages[$menu_slug]) ;
  }


  public function GenerateOptions($tabs, $values=NULL, $action=NULL, $echo=true) {
    // todo tab hiding
    // configuration for submit button?


    $xmlroot = new SimpleXMLElement('<div />');
    
    $xmlmenu = $xmlroot->addChild("span");
    $xmltabs = $xmlroot->addChild("form");    
    $xmltabs->addAttribute("enctype","multipart/form-data");
    $xmltabs->addAttribute("method","post");
    if ($action) $xmltabs->addAttribute("action", $action);



    $hide=array();
    $allTabs=array();
    $firstTab = true;
    foreach  ($tabs as $tab) {

      $alltabs[]=$tab->name   ."_tab"; // to export to JS client side 

      $xmlbutton = $xmlmenu->addChild("button", $tab->display);
      $xmlbutton->addAttribute("name", $tab->name ."_btn");
      $xmlbutton->addAttribute("id", $tab->name   ."_btn");
      $xmlbutton->addAttribute("class", "blaatConfigBtn");
      $xmlbutton->addAttribute("onclick", "showOnlyElement('".$tab->name."_tab', alltabs)"); // test

      $xmltab = $xmltabs->addChild("tab");    
      $xmltab->addAttribute("name", $tab->name ."_tab");
      $xmltab->addAttribute("id", $tab->name   ."_tab");
      $xmltab->addAttribute("class", "blaatConfigTab");

      $xmltable = $xmltab->addChild("table");  

      foreach ($tab->options as $option) {

        $xmlrow = $xmltable->addChild("tr");
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

    $xmlSaveButton = $xmltabs->addChild("button", __("Save"));


    $xmlroot->addChild("script", "
      var alltabs = JSON.parse('" . json_encode($alltabs) ."');  
      window.onload = function () { showFirstElement(alltabs);};"
    );
    return BlaatSchaap::xml2html($xmlroot, $echo);  
     //if ($echo) echo $xmlroot->AsXML();
     //return $xmlroot;
  }

  function xml2html($xmlroot, $echo=true) {
    $dom_xml = dom_import_simplexml($xmlroot);
    if (!$dom_xml) {
      // TODO handle this error condition
      exit;
    }

    $dom = new DOMDocument();
    $dom_xml = $dom->importNode($dom_xml, true);
    $dom_xml = $dom->appendChild($dom_xml);

    //if ($echo) echo $dom->saveHTML($dom_xml); // requires php 5.3.6
    if ($echo) echo $dom->saveHTML();
    return $dom;
  }

















 
}



?>

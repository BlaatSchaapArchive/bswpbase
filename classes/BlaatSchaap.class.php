<?php

class BlaatSchaap {

    // TODO: dependency tester for example class_exists(SimpleXMLElement);

  function DBTableExists($table_name){
    // php.net/manual/en/function.method-exists.php
    global $wpdb;
    return ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name);
  }
    
    
//------------------------------------------------------------------------------
  function enqueueAdminJS(){
    wp_enqueue_script("BlaatSchaapJS" , plugin_dir_url(__FILE__) . "../js/BlaatSchaap.js");
  }
//------------------------------------------------------------------------------
  function enqueueAdminCSS(){
    wp_register_style("BlaatConfig" , plugin_dir_url(__FILE__) . "../css/BlaatConfig.css");
    wp_enqueue_style( "BlaatConfig");
  }
//------------------------------------------------------------------------------
  public function isPageRegistered($menu_slug){
    global $_parent_pages;
    return isset($_parent_pages[$menu_slug]) ;
  }
//------------------------------------------------------------------------------
  public function GenerateOptions($tabs, $values=NULL, $header=NULL,$buttonID=NULL, $buttonTitle=NULL ,$action=NULL, $echo=true) {

    $xmlroot = new SimpleXMLElement('<div />');
    
    if ($header!=NULL)     $xmlmenu = $xmlroot->addChild("h1", $header);

    $xmlmenu = $xmlroot->addChild("span");
    $xmltabs = $xmlroot->addChild("form");    
    $xmltabs->addAttribute("enctype","multipart/form-data");
    $xmltabs->addAttribute("method","post");
    if ($action) $xmltabs->addAttribute("action", $action);

    $hide=array();
    $allTabs=array();
    $firstTab = true;
    foreach  ($tabs as $tab) {

      if ($tab->hidden) {
        $xmltab = $xmltabs->addChild("tab");    
        $xmltab->addAttribute("name", $tab->name ."_hiddentab");
        $xmltab->addAttribute("id", $tab->name   ."_hiddentab");
        $xmltab->addAttribute("class", "blaatConfigHiddenTab");

        } else {
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
      }
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
          case "checkbox":
            $xmltd = $xmlrow->addChild("td");

            $xmlhiddenoption=$xmltd->addChild("input");
            $xmlhiddenoption->addAttribute("type","hidden");
            $xmlhiddenoption->addAttribute("value","0");
            $xmlhiddenoption->addAttribute("name",$option->name);
            $xmlhiddenoption->addAttribute("id",$option->name."_hidden");    

            $xmloption=$xmltd->addChild("input");
            $xmloption->addAttribute("type","checkbox");
            $xmloption->addAttribute("value","1");
            if ($values) { 
              if (isset($values[$option->name]) && $values[$option->name]) 
                $xmloption->addAttribute("checked","true");
            } elseif ($option->default==true) {
              $xmloption->addAttribute("checked",true);
            }
            break;

          default:
            $xmloption = $xmlrow->addChild("td")->addChild("input");
            $xmloption->addAttribute("type",$option->type);
            if ($values) {
              if(isset($values[$option->name])) {
                $xmloption->addAttribute("value",$values[$option->name]);      
              }
            } elseif ($option->default) $xmloption->addAttribute("value",$option->default); 
        }

        $xmloption->addAttribute("name",$option->name);
        $xmloption->addAttribute("id",$option->name);    
        //if ($option->required==true) $xmloption->addAttribute("required",true);
        // required doesn't seem to work as desired? disable it for now.
      }
    }  

    if ($buttonTitle==NULL) $buttonTitle=__("Save");
    $xmlSaveButton = $xmltabs->addChild("button", $buttonTitle);
    if ($buttonID==NULL) $buttonID = "save";
    $xmlSaveButton->addAttribute("id", $buttonID);
    $xmlSaveButton->addAttribute("name", $buttonID);


    $xmlCancelButton = $xmltabs->addChild("button", __("Cancel"));
    $xmlCancelButton->addAttribute("id",   "cancel");
    $xmlCancelButton->addAttribute("name", "cancel");


    $xmlroot->addChild("script", "
      var alltabs = JSON.parse('" . json_encode($alltabs) ."');  
      window.onload = function () { showFirstElement(alltabs);};"
    );
    return BlaatSchaap::xml2html($xmlroot, $echo);  
  }

  function setupPageSelect(&$configOptions){
    $item = $configOptions->name;
    $pages = get_pages();
    $selectedPageName = get_option($item);
    $configOptions->default = $selectedPageName;

    if ($selectedPageName==NULL) $selectedPageName="";
    if (!strlen($selectedPageName)) {
      $configOptions->addOption(new BlaatConfigSelectOption("",__("None")));    
    }

    foreach ( $pages as $page ) {
      $pagename = $page->post_title;//$page->post_name;
      $pageid = $page->ID;
      $configOptions->addOption(new BlaatConfigSelectOption($pageid,$pagename));    
    }
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

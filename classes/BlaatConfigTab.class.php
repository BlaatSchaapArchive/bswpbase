<?php

class BlaatConfigTab {
	public $name;
	public $display;
	public $hidden;
	// TODO possible dependencies?
	public $options;

	function BlaatConfigTab($name, $display, $hidden=false) {
		$this->name=$name;
		$this->display=$display;
		$this->hidden=$hidden;
		$this->options=array();
	}
	
	function addOption($option){
		$this->options[]=$option;
	}
}
?>

<?php


class BlaatConfigOption{
	public $title;
	public $name;
	public $type;
	public $required;

	public $default;
	public $options;

    function BlaatConfigOption($name, $title, $type="text", $required=false, $default=null){
		$this->name=$name;
		$this->title=$title;
		$this->type=$type;
		$this->required=$required;
		$this->default=$default;
		$this->options=array();
    }
	function addOption($option) {
		$this->options[]=$option;
	}

}

class BlaatConfigSelectOption{
	public $value;
	public $display;

	function BlaatConfigSelectOption($value, $display) {
		$this->value=$value;
		$this->display=$display;
	}
}

?>

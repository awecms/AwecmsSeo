<?php 
class AwecmsSeoSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $awecms_seo_metas = array(
		'id' => array('type' => 'integer', 'null' => false, 'length' => 11, 'key' => 'primary'),
		'model' => array('type' => 'string', 'null' => false),
		'key' => array('type' => 'string', 'null' => false),
		'title' => array('type' => 'text', 'null' => true),
		'description' => array('type' => 'text', 'null' => true),
		'keywords' => array('type' => 'text', 'null' => true),
		'is_active' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'indexes' => array(
			
		),
		'tableParameters' => array()
	);

}

<?php
App::uses('AwecmsSeoMeta', 'AwecmsSeo.Model');

/**
 * AwecmsSeoMeta Test Case
 *
 */
class AwecmsSeoMetaTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.awecms_seo.awecms_seo_meta'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->AwecmsSeoMeta = ClassRegistry::init('AwecmsSeo.AwecmsSeoMeta');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AwecmsSeoMeta);

		parent::tearDown();
	}

}

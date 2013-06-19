<?php
/*
 * AweCMS: SEO Plugin
 * Copyright (c) David Gallagher (http://thegallagher.net)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) David Gallagher (http://thegallagher.net)
 * @link          http://awecms.com
 * @package       AwecmsSeo
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('CakeEventListener', 'Event');

class AwecmsSeo implements CakeEventListener {
	
	public function implementedEvents() {
		return array(
			'Controller.initialize' => 'initialize',
		);
	}
	
	public function initialize($event) {
		$Controller =& $event->subject();
		if (Configure::read('AwecmsSeo.meta.autoload')) {
			$Controller->helpers['Meta'] = array('className' => 'AwecmsSeo.AwecmsSeoMeta');
		}
	}
}
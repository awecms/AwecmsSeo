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
App::uses('Hash', 'Utility');
App::uses('String', 'Utility');
App::uses('ClassRegistry', 'Utility');
App::uses('AppHelper', 'View/Helper');

class AwecmsSeoMetaHelper extends AppHelper {

	public $helpers = array('Html');
	public $settings = array(
		'defaults' => array(
			'actions' => array(
				'model' => null,
				'path' => null,
				'primaryKey' => null,
				'title' => array(
					'field' => null,
					'default' => null,
					'truncate' => false,
					'html' => false,
				),
				'description' => array(
					'field' => 'content',
					'default' => null,
					'truncate' => 160,
					'html' => true,
				),
				'keywords' => array(
					'field' => null,
					'default' => null,
					'truncate' => 160,
					'html' => false,
				),
			),
		),
		'useTitleFormat' => true,
	);
	protected $_init = false;
	
	protected function _init() {
		if (!$this->_init) {
			$this->AwecmsSeoMeta = ClassRegistry::init('AwecmsSeo.AwecmsSeoMeta');
			$config = (array) Configure::read('AwecmsSeo.meta');
			$this->settings = Hash::normalize(Hash::merge($config, $this->settings));
			$this->_init = true;
		}
	}
	
	public function beforeLayout($layoutFile) {
		$this->setupTags();
	}
	
	public function setupTags() {
		$this->_init();
		if (!$this->settings['actions']) {
			return;
		}
		
		$controller = $this->_View->request['controller'];
		$action = $this->_View->request['action'];
		$key = $controller . '.' . $action;
		if (!isset($this->settings['actions'][$key])) {
			return;
		}
		$options = Hash::merge($this->settings['defaults']['actions'], $this->settings['actions'][$key]);
		
		$model = $options['model'];
		if (empty($model)) {
			$model = Inflector::classify($this->_View->request['controller']);
		}
		
		$path = $options['path'];
		if ($path === null && $action === 'view') {
			$path = Inflector::variable($model) . '.' . $model;
		}
		
		$metaData = $pageData = null;
		if (empty($path)) {
			$metaData = $this->AwecmsSeoMeta->findByModelAndKey($model, $action);
		} else {
			$primaryKey = $options['primaryKey'];
			if (empty($primaryKey)) {
				$primaryKey = ClassRegistry::init($model)->primaryKey;
			}
			
			$pageData = Hash::get($this->_View->viewVars, $path);
			if (!empty($pageData[$primaryKey])) {
				$metaData = $this->AwecmsSeoMeta->findByModelAndKey($model, $pageData[$primaryKey]);
			}
		}
		
		if (empty($metaData) && empty($pageData)) {
			return;
		}
		
		$title = $description = $keywords = null;
		if (!empty($metaData)) {
			$title = $metaData['AwecmsSeoMeta']['title'];
			$description = $metaData['AwecmsSeoMeta']['description'];
			$keywords = $metaData['AwecmsSeoMeta']['keywords'];
		}
		
		$this->_metaValue('title', $title, $pageData, $options);
		$this->_metaValue('description', $description, $pageData, $options);
		$this->_metaValue('keywords', $keywords, $pageData, $options);
		
		if (!empty($title)) {
			if ($this->settings['useTitleFormat'] && Configure::check('Awecms.titleFormat')) {
				$title = sprintf(Configure::read('Awecms.titleFormat'), $title);
			}
			$this->_View->set('title_for_layout', $title);
		}
		if (!empty($description)) {
			$this->_View->append('meta', $this->Html->meta('description', $description));
		}
		if (!empty($keywords)) {
			$this->_View->append('meta', $this->Html->meta('keywords', $keywords));
		}
	}
	
	protected function _metaValue($type, &$value, $pageData, $options) {
		if (empty($value) && !empty($options[$type]['field']) && !empty($pageData[$options[$type]['field']])) {
			$value = trim($pageData[$options[$type]['field']]);
			if ($options[$type]['html']) {
				$value = trim(htmlspecialchars_decode(strip_tags($value)));
			}
		}
		if (empty($value) && !empty($options[$type]['default'])) {
			$value = $options[$type]['default'];
		}
		
		if (!empty($options[$type]['truncate'])) {
			$value = String::truncate($value, $options[$type]['truncate'], array('exact' => false, 'ellipsis' => ''));
		}
	}
}
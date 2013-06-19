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

App::uses('AwecmsSeoAppController', 'AwecmsSeo.Controller');
App::uses('ClassRegistry', 'Utility');
App::uses('CakeTime', 'Utility');
App::uses('CakeEvent', 'Event');
App::uses('Hash', 'Utility');

class SeoController extends AwecmsSeoAppController {

	public $uses = array();
	public $helpers = array('Cache');
	
	public function beforeFilter() {
		$this->_setupCache();
	}
	
	protected function _setupCache() {
		$cache = Configure::read('AwecmsSeo.cache');
		$default = !empty($cache['default']) ? $cache['default'] : 86400;
		$this->cacheAction = array();
		$this->cacheAction['robots'] = !empty($cache['robots']) ? $cache['robots'] : $default;
		$this->cacheAction['sitemap'] = !empty($cache['sitemap']) ? $cache['sitemap'] : $default;
	}
	
	public function robots() {
		$config = $this->_initRobotsConfig();
		$this->layout = 'text';
		$this->response->type('txt');
		$this->set($config);
	}
	
	protected function _initRobotsConfig() {
		$default = array(
			'userAgents' => array(
				'*' => array(
					'Crawl-delay' => 10,
					'Allow' => array(
						Router::url('/'),
					),
					'Disallow' => array(
						Router::url('/admin'),
						Router::url('/' . CSS_URL),
						Router::url('/' . JS_URL),
						Router::url('/' . IMAGES_URL),
					),
				),
			),
			'sitemaps' => array(
				Router::url(array('plugin' => 'awecms_seo', 'controller' => 'seo', 'action' => 'sitemap'))
			),
		);
		$config = Hash::normalize(Hash::merge($default, (array) Configure::read('AwecmsSeo.robots')));
		
		$event = new CakeEvent('AwecmsSeo.robotsConfig', $this, $config);
		$this->getEventManager()->dispatch($event);
		return Hash::normalize($event->data);
	}
	
	public function sitemap() {
		$config = $this->_initSitemapConfig();
		$this->layout = 'text';
		$this->response->type('xml');
		
		$urls = array();
		if (!empty($config['models'])) {
			foreach ($config['models'] as $key => $options) {
				list($plugin, $modelName) = pluginSplit($key);
				$options = Hash::merge($config['default'], $options);
				
				if (!isset($options['controller'])) {
					$options['controller'] = Inflector::tableize($modelName);
				}
				
				$Model = ClassRegistry::init($key);
				$fields = (array) $options['id'];
				if (!empty($options['lastmod'])) {
					$fields[] = $options['lastmod'];
				}
				$data = $Model->find('all', array(
					'conditions' => $options['conditions'],
					'fields' => $fields,
					'recursive' => -1,
				));
				
				foreach ($data as $values) {
					foreach ($options['id'] as $field) {
						if (!empty($values[$modelName][$field])) {
							$id = $values[$modelName][$field];
							break;
						}
					}
					$url = Router::url(array(
						'plugin' => strtolower($plugin),
						'controller' => $options['controller'],
						'action' => $options['view'],
						$id,
						'full' => false,
					));
					$urls[$url] = array(
						'priority' => number_format($options['priority'], 1),
						'changefreq' => $options['changefreq'],
					);
					if (!empty($options['lastmod'])) {
						$urls[$url]['lastmod'] = CakeTime::format($values[$modelName][$options['lastmod']], 'Y-m-d');
					}
				}
			}
		}
		$urls = Hash::merge($urls, $config['urls']);
		$this->set(compact('urls'));
	}
	
	protected function _initSitemapConfig() {
		$default = array(
			'default' => array(
				'lastmod' => null,
				'changefreq' => 'weekly',
				'priority' => 0.5,
				'index' => array('action' => 'index'),
				'view' => 'view',
				'conditions' => array('is_active' => 1),
				'id' => array('slug', 'id'),
			),
			'urls' => array(
				Router::url('/') => array(
					'priority' => '1.0',
					'changefreq' => 'daily',
				),
			),
		);
		$config = Hash::normalize(Hash::merge($default, (array) Configure::read('AwecmsSeo.sitemap')));
		
		$event = new CakeEvent('AwecmsSeo.sitemapConfig', $this, $config);
		$this->getEventManager()->dispatch($event);
		return Hash::normalize($event->data);
	}
}
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
foreach ($userAgents as $userAgent => $directives):
	echo sprintf("User-agent: %s\n", $userAgent);
	foreach ($directives as $directive => $values):
		foreach ((array) $values as $value):
			echo sprintf("%s: %s\n", $directive, $value);
		endforeach;
	endforeach;
endforeach;

foreach ((array) $sitemaps as $sitemap):
	echo sprintf("Sitemap: %s\n", $sitemap);
endforeach;
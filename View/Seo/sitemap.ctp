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

echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<?php foreach ($urls as $url => $options): ?>
		<url>
			<loc><?php echo h($url); ?></loc>
			<?php foreach ($options as $option => $value): ?>
				<?php echo sprintf('<%1$s>%2$s</%1$s>', $option, h($value)); ?>
			<?php endforeach; ?>
		</url>
	<?php endforeach; ?>
</urlset>
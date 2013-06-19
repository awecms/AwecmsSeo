<?php
Router::connect('/robots.txt', array('plugin' => 'awecms_seo', 'controller' => 'seo', 'action' => 'robots'));
Router::connect('/sitemap.xml', array('plugin' => 'awecms_seo', 'controller' => 'seo', 'action' => 'sitemap'));
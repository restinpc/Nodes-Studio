<?php
echo 'User-agent: *
Host: '.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'
Disallow: /engine/
Disallow: /res/
Disallow: /script/
Disallow: /fonts/
Disallow: /admin$
Disallow: /account$
Disallow: /install.php
Disallow: /account.php
Disallow: /captcha.php
Disallow: /cron.php
Disallow: /edit.php
Disallow: /graph.php
Disallow: /messages.php
Disallow: /paypal.php
Disallow: /update.php
Disallow: /uploader.php
Sitemap: http://'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'/sitemap.xml';
<?php

/* Database constants */
define("DB_USER", "tempkartadmin");
define("DB_PASS", "tempkartpass");
define("DB_NAME", "tempkart_db");
define("DB_HOST", "127.0.0.1");

define("BASEURL", $_SERVER['DOCUMENT_ROOT'].'/ecommerce/');

define('CART_COOKIE', 'TKc6r9a4hul');

define('CART_COOKIE_EXPIRE', time() + (86400 * 30));

define('TAXRATE', 0.013);

define('CURRENCY', 'usd');
define('CHECKOUTMODE', 'TEST');

if(CHECKOUTMODE == 'TEST'){
	define("STRIPE_PRIVATE",'sk_test_ZcW3bXIt8KCWN2kxgC25xnm7');
	define("STRIPE_PUBLIC",'pk_test_BjOobhwSyTHkoZmfZ6zlRIsx');
}

if(CHECKOUTMODE == 'LIVE'){
	define("STRIPE_PRIVATE",'sk_live_ROVEWCOABIEC9lRIWFyfEs3p');
	define("STRIPE_PUBLIC",'pk_live_CU4bbQU05nJ8TFQBqXoC7u5M');
}


?>
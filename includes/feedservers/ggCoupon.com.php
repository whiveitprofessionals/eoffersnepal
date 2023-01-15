<?php

/*

ggCoupon.com FEED SERVER

CONFIG FILE

*/

// Server Timezone
$server['TIMEZONE'] = 'America/New_York';

// Information about a coupon
$server['COUPON_URL'] = 'https://ggcoupon.com/api/v1/coupon';

// The list of coupons
$server['COUPONS_URL'] = 'https://ggcoupon.com/api/v1/coupons';

// Information about a product
$server['PRODUCT_URL'] = 'https://ggcoupon.com/api/v1/product';

// The list of products
$server['PRODUCTS_URL'] = 'https://ggcoupon.com/api/v1/products';

// Information about a store/brand
$server['STORE_URL'] = 'https://ggcoupon.com/api/v1/store';

// The list of stores
$server['STORES_URL'] = 'https://ggcoupon.com/api/v1/stores';

// The list of categories
$server['CATEGORIES_URL'] = 'https://ggcoupon.com/api/v1/categories';

// Authentication type, HTTP (for Basic HTTP Authentication) or GET (using authentication via GET parameter)
// Disallow to select other type of authentication, but GET.
$server['AUTHENTICATION'] = 'GET';

/*

INFORMATION ABOUT THIS SERVER

*/

// Server homepage
$server['URL'] = '//ggcoupon.com';

// Contact email address
$server['CONTACT'] = 'feed@ggcoupon.com';

// Description
$server['DESCRIPTION'] = 'You can visit us at https://ggcoupon.com';
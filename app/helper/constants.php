<?php

// define("PUBLIC_DIR", 'public'); // this for server
define('PUBLIC_DIR', ''); // this for local

define('ADMIN_PER_PAGE', 10);
define('API_PER_PAGE', 10);
define('ADMIN_LANG_DEFAULT', 'en');
define('DIR_UPLOAD', 'uploads');
define('DIR_WEB', 'web');

// WEB site
define('WEBSITE', 'WebSite');
define('SITE_VIEWS_DIR', 'site');
define('SITE_PUBLIC_DIR', 'site');
define('SITE_ROUTE', 'site');

define('DEFAULT_IMAGE', 'no_image.png');

/*
 * System
 */
define('DATE_FORMAT', 'Y-m-d');
define('DATE_FORMAT_FULL', 'Y-m-d H:i:s');
define('TIME_FORMAT', 'H:i:s');

define('ROOT_NAMESPACE', 'Api\v1');
define('PASSWORD', '123456');

define('API_ACCESS_KEY', '');

// Errors
define('IS_ERROR', 'isError');
define('ERRORS', 'errors');
define('ERROR', 'error');

// boolean
define('YES', 1);
define('NO', 0);

// Gender
define('MALE', 1);
define('FEMALE', 2);

define('USER_TYPE', [
    'CLIENT' => 'client',
    'ADMIN' => 'admin',
    'UESR' => 'user',
    // other constants...
]);

define('CLIENT_TYPE', [
    'FAMILY_MEMBER' => 'family_member',
    'LIFE_FAMILY_MEMBER' => 'life_family_member',
    'PROFISSION_EMPLOYEE' => 'profission_employee',
    'PASSENGER_MEMBER' => 'PASSENGER_MEMBER',
    'PENEFICIARIES_MEMBER' => 'PENEFICIARIES_MEMBER',
    'DRIVERS_MEMBER' => 'DRIVERS_MEMBER',
]);
// Father mother son daughter brother sister
define('FAMILY_MEMBERS', [
    'PARENT' => 'Father',
    'WIFE' => 'Wife',
    'SON' => 'Son',
    'DAUGHTER' => 'Daughter',
    'BROTHER' => 'Brother',
    'SISTER' => 'Sister',
    'HUSBAND' => 'Husband',
    'MOTHER' => 'Mother',
]);
define('ORDER_BY', [
    // 1 => oldest_first, 2 => newest_first
    'OLDEST_FIRST' => 1,
    'NEWEST_FIRST' => 2,
]);

/*
 *
 * Notification types
 */
define('notification_types', [
    'general_notification' => 'general_notification',
    'policyOffer_result_notification' => 'policyOffer_result_notification',
    'checkout_success_notification' => 'checkout_success_notification',
]);
/*
 * System
 */
define('DATE_FORMAT_DOTTED', 'd.m.Y');
define('TIME_FORMAT_WITHOUT_SECONDS', 'h:i A');

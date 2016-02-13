<?php

$blacklist = array('188.143.232.31', '91.121.170.197', '188.143.232.111', '146.0.74.205', '46.161.41.34');

if(in_array($_SERVER['REMOTE_ADDR'], $blacklist)) {
    die("We can not process your request at this time, Please try again later.");
}

$whitelist = array('67.169.172.175');

// put the site in maintenance mode
if(false) {
    if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
        die('<h1>Shopintoit is undergoing a scheduled maintenance and will be back to service soon.</h1>
            <p><a href="http://blog.shopintoit.com/" target="_blank">Blog</a></p>
            <p><a href="https://twitter.com/shopintoit" target="_blank">Follow Shopintoit on Twitter</a></p>
            <p><a href="http://www.facebook.com/shopintoit" target="_blank">Like Shopintoit on Facebook</a></p>');
    }
}

// shutdown page
if((true || isset($_REQUEST['shutdown'])) && !in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
    if(substr($_SERVER['REQUEST_URI'], 0, 9) != "/shutdown"){
        header("Location: /shutdown/");
        exit;
    }
}

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

require_once APPLICATION_PATH.'/autoload.php';
require_once APPLICATION_PATH.'/constants.php';
require_once APPLICATION_PATH.'/errors.php';
require_once APPLICATION_PATH.'/page_acl.php';
require_once APPLICATION_PATH.'/globals.php';
require_once APPLICATION_PATH.'/utils/utils.php';

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// custom setup
require_once 'Zend/Loader/Autoloader.php';
require_once APPLICATION_PATH . '/../library/facebook/src/facebook.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->pushAutoloader('__autoload');
$application_config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini', APPLICATION_ENV, true);

// setup site(domain, version, https) info
$site_versions = $application_config->site->versions;
list($site_domain, $site_merchant_url, $site_version) = getSiteVersionInfo($site_versions);
$site_https_enable = $application_config->site->https->enable;

// set version sepcific config
$config_version_key = "v$site_version";
foreach($application_config->$config_version_key as $k => $v) {
    if(isset($application_config, $k)) {
        $application_config->$k->merge($v);
    } else {
        $application_config->$k = $v;
    }
}
$application_config->setReadOnly();

// get config items
$dbconfig = $application_config->database;
$paypalconfig = $application_config->paypal;
$facebookconfig = $application_config->facebook;
$fileuploader_config = $application_config->fileuploader;
$amazonconfig = $application_config->amazon;
$transaction_config = $application_config->transaction_config;
$shopinterest_config = $application_config->shopinterest;
$pinterest_config = $application_config->pinterest;
$sphinx_config = $application_config->sphinx;
$redis_config = $application_config->redis;
$sendgridconfig = $application_config->sendgrid;
$filepicker = $application_config->filepicker;
$googleconfig = $application_config->google;
$cloudinary_config = $application_config->cloudinary;

// errnos global array
$GLOBALS['errnos'] = array();
get_abtests_cookie();

// load mustache
require APPLICATION_PATH.'/../library/mustache/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();

// load google client api
require APPLICATION_PATH.'/../library/google-api-php-client/src/Google_Autoloader.php';
Google_Autoloader::register();

// load paypal rest api
require APPLICATION_PATH.'/../library/paypal/rest/vendor/autoload.php';
require APPLICATION_PATH.'/../library/paypal/rest/common.php';

// load composer installed libarries: cloudinary, amazon product advertising api
require APPLICATION_PATH.'/../library/vendor/autoload.php';
\Cloudinary::config(array(
    "cloud_name" => $cloudinary_config->api->cloud_name,
    "api_key" => $cloudinary_config->api->key,
    "api_secret" => $cloudinary_config->api->secret,
));

// connect to redis
$account_dbobj = DBObj::getAccountDBObj();
$job_dbobj = DBObj::getJobDBObj();
$redis = new RedisCache($account_dbobj);
$redis->connect($redis_config->server->host, $redis_config->server->port);

if(APPLICATION_ENV != 'production' && APPLICATION_ENV != 'staging'){
    // update table info on every request under dev env, overide data in tables.php
    get_table_info(true);
}
require_once APPLICATION_PATH.'/cachekey_lists.php';
session_start();
// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();

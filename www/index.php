<?php

require_once(dirname(dirname(__FILE__)).'/lib/_autoload.php');

use \SimpleSAML\Module\monitor\DependencyInjection as DependencyInjection;
use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Configuration as ApplicationConfiguration;
use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\Monitor as Monitor;
use Symfony\Component\HttpFoundation\JsonResponse;

assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 1);

$serverVars = new DependencyInjection($_SERVER);
$requestVars = new DependencyInjection($_REQUEST);
$globalConfig = ApplicationConfiguration::getInstance();

assert(!is_null($globalConfig));

$authSourceConfig = ApplicationConfiguration::getOptionalConfig('authsources.php');
$moduleConfig = ApplicationConfiguration::getOptionalConfig('module_monitor.php');

$testConfiguration = new TestConfiguration($serverVars, $requestVars, $globalConfig, $authSourceConfig, $moduleConfig);
$monitor = new Monitor($testConfiguration);

$monitor->invokeTestSuites();
$results = $monitor->getResults();

$healthInfo = array(
    State::SKIPPED => array('SKIPPED', 'yellow'),
    State::FATAL   => array('FATAL',   'purple'),
    State::ERROR   => array('NOK',     'red'   ),
    State::NOSTATE => array('NOSTATE', 'cyan'  ),
    State::WARNING => array('WARNING', 'orange'),
    State::OK      => array('OK',      'green' )
);

$state = $monitor->getState();
if ($state === State::OK) {
    $responseCode = 200;
} else if ($state === State::WARNING) {
    $responseCode = 417;
} else {
    $responseCode = 500;
}

$outputFormat = $requestVars->get('output');
switch ($outputFormat) {
    case 'xml':
        $t = new \SimpleSAML\XHTML\Template($globalConfig, 'monitor:monitor.xml.php');
        $protocol = $serverVars->get('HTTP_PROTOCOL');
        $t->data['protocol'] = is_null($protocol) ? 'HTTP/1.0' : $protocol;
        break;
    case 'json':
        JsonResponse::create(['overall' => $healthInfo[$state][0], 'results' => $results], $responseCode)->send();
        return;
    default:
        $t = new \SimpleSAML\XHTML\Template($globalConfig, 'monitor:monitor.php');
        break;
}

$t->data['header'] = 'Monitor';
$t->data['authsources'] = $results['authsources'];
$t->data['configuration'] = $results['configuration'];
$t->data['modules'] = $results['modules'];
$t->data['store'] = $results['store'];
$t->data['metadata'] = $results['metadata'];
$t->data['overall'] = $state;
$t->data['healthInfo'] = $healthInfo;
$t->data['responseCode'] = $responseCode;

unset($monitor, $results, $globalConfig, $healthInfo);
$t->show();

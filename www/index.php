<?php

require_once(dirname(dirname(__FILE__)).'/lib/_autoload.php');

use \SimpleSAML\Modules\Monitor\DependencyInjection as DependencyInjection;
use \SimpleSAML\Modules\Monitor\State as State;
use \SimpleSAML\Modules\Monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Modules\Monitor\Monitor as Monitor;
use \SimpleSAML\Configuration as ApplicationConfiguration;
use \Symfony\Component\HttpFoundation\JsonResponse;

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

$healthInfo = [
    State::SKIPPED => ['SKIPPED', 'yellow'],
    State::FATAL   => ['FATAL',   'purple'],
    State::ERROR   => ['NOK',     'red'   ],
    State::NOSTATE => ['NOSTATE', 'cyan'  ],
    State::WARNING => ['WARNING', 'orange'],
    State::OK      => ['OK',      'green' ]
];

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
        $GLOBALS['http_response_code'] = $responseCode;
        http_response_code($responseCode);
        header("Content-Type: text/xml");
        break;
    case 'json':
        JsonResponse::create(['overall' => $healthInfo[$state][0], 'results' => $results], $responseCode)->send();
        return;
    case 'text':
        $t = new \SimpleSAML\XHTML\Template($globalConfig, 'monitor:monitor.text.php');
        $GLOBALS['http_response_code'] = $responseCode;
        http_response_code($responseCode);
        if ($responseCode === 200) {
            $t->data['status'] = 'OK';
        } else if ($responseCode === 417) {
            $t->data['status'] = 'WARN';
        } else {
            $t->data['status'] = 'FAIL';
        }
        break;
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

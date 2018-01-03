<?php

use \SimpleSAML\Module\monitor\DependencyInjection as DependencyInjection;
use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\Monitor as Monitor;

//assert_options(ASSERT_ACTIVE, 1);
//assert_options(ASSERT_WARNING, 1);

$serverVars = new DependencyInjection($_SERVER);
$requestVars = new DependencyInjection($_REQUEST);

$monitor = new Monitor($serverVars, $requestVars);

$configuration = $monitor->getConfiguration();
$globalConfig = $configuration->getGlobalConfig();

assert(!is_null($globalConfig));

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

// TODO: make this more specific: ?output=xml
$xml = $requestVars->get('xml');
if (!is_null($xml)) {
    $t = new SimpleSAML_XHTML_Template($globalConfig, 'monitor:monitor.xml.php');
    $protocol = $serverVars->get('HTTP_PROTOCOL');
    $t->data['protocol'] = is_null($protocol) ? 'HTTP/1.0' : $protocol;
} else {
    $t = new SimpleSAML_XHTML_Template($globalConfig, 'monitor:monitor.php');
}

$t->data['header'] = 'Monitor';
$t->data['authsources'] = $results['authsources'];
$t->data['configuration'] = $results['configuration'];
$t->data['modules'] = array_map(function($i) {
    return $i[0];
}, array_merge($results['modules']));
$t->data['store'] = $results['store'];
$t->data['metadata'] = $results['metadata'];
$t->data['overall'] = $monitor->getState();
$t->data['healthInfo'] = $healthInfo;

unset($monitor, $results, $globalConfig, $healthInfo);
$t->show();

<?php
use sspmod_monitor_State as State;

//assert_options(ASSERT_ACTIVE, 1);
//assert_options(ASSERT_WARNING, 1);

$monitor = new sspmod_monitor_Monitor();

$global_config = $monitor->getGlobalConfig();

$monitor->invokeTestSuites();
$results = $monitor->getResults();

$health_info = array(
    State::SKIPPED => array('SKIPPED', 'yellow'),
    State::FATAL   => array('FATAL',   'purple'),
    State::ERROR   => array('NOK',     'red'   ),
    State::NOSTATE => array('NOSTATE', 'cyan'  ),
    State::WARNING => array('WARNING', 'orange'),
    State::OK      => array('OK',      'green' )
);

if (isSet($_REQUEST['xml'])) {
    $t = new SimpleSAML_XHTML_Template($global_config, 'monitor:monitor.xml.php');
} else {
    $t = new SimpleSAML_XHTML_Template($global_config, 'monitor:monitor.php');
}

$t->data['header'] = 'Monitor';
$t->data['authsources'] = $results['authsources'];
$t->data['configuration'] = $results['configuration'];
$t->data['modules'] = array_map(function($i) {
    return $i[0];
}, array_merge($results['modules']));
$t->data['store'] = $results['store'];
$t->data['overall'] = $monitor->getState();
$t->data['health_info'] = $health_info;

unset($monitor, $results, $global_config, $health_info);
$t->show();

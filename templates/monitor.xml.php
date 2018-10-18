<?php

use \SimpleSAML\Modules\Monitor\State as State;

$modules = $this->data['modules'];
$configuration = $this->data['configuration'];
$store = $this->data['store'];
$state = $this->data['overall'];
$authsources = $this->data['authsources'];
$metadata = $this->data['metadata'];
$healthInfo = $this->data['healthInfo'];
$protocol = $this->data['protocol'];
$responseCode = $this->data['responseCode'];

list($healthState, $healthColor) = $healthInfo[$state];

$overall = $healthState;

$GLOBALS['http_response_code'] = $responseCode;
if ($responseCode === 200) {
  header($protocol . ' 200 OK');
} else if ($responseCode === 417) {
  header($protocol . ' 417 Expectation failed');
} else {
  header($protocol . ' 500 Internal Server Error');
}

$output = '<?xml version="1.0" encoding="UTF-8"?>';
$output .= '<monitor>';
$output .= '<health>' . $overall . '</health>';
$output .= '<checks>';

foreach ($modules as $check) {
    $health = $check['state'];
    $category = $check['category'];
    $subject = $check['subject'];
    $summary = $check['message'];
    list($healthState, $healthColor) = $healthInfo[$health];

    $output .= '<check category="' . $category . '">';
    $output .= '<subject>' . $subject . '</subject>';
    $output .= '<health>' . $healthState . '</health>';
    $output .= '<summary>' . $summary . '</summary>';
    $output .= '</check>';
}

foreach ($configuration as $check) {
    $health = $check['state'];
    $category = $check['category'];
    $subject = $check['subject'];
    $summary = $check['message'];
    list($healthState, $healthColor) = $healthInfo[$health];

    $output .= '<check category="' . $category . '">';
    $output .= '<subject>' . $subject . '</subject>';
    $output .= '<health>' . $healthState . '</health>';
    $output .= '<summary>' . $summary . '</summary>';
    $output .= '</check>';
}

foreach ($store as $check) {
    $health = $check['state'];
    $category = $check['category'];
    $subject = $check['subject'];
    $summary = $check['message'];
    list($healthState, $healthColor) = $healthInfo[$health];

    $output .= '<check category="' . $category . '">';
    $output .= '<subject>' . $subject . '</subject>';
    $output .= '<health>' . $healthState . '</health>';
    $output .= '<summary>' . $summary . '</summary>';
    $output .= '</check>';
}

foreach ($authsources as $name => $authsource) {
    foreach ($authsource as $check) {
        $health = $check['state'];
        $category = $check['category'];
        $subject = $check['subject'];
        $summary = $check['message'];
        list($healthState, $healthColor) = $healthInfo[$health];

        $output .= '<check category="' . $category . '">';
        $output .= '<subject>' . $subject . '</subject>';
        $output .= '<health>' . $healthState . '</health>';
        $output .= '<summary>' . $summary . '</summary>';
        $output .= '</check>';
    }
}

foreach ($metadata as $entityId => $entityMetadata) {
    foreach ($entityMetadata as $check) {
        $health = $check['state'];
        $category = $check['category'];
        $subject = $check['subject'];
        $summary = $check['message'];
        list($healthState, $healthColor) = $healthInfo[$health];

        $output .= '<check category="' . $category . '">';
        $output .= '<subject>' . $subject . '</subject>';
        $output .= '<health>' . $healthState . '</health>';
        $output .= '<summary>' . $summary . '</summary>';
        $output .= '</check>';
    }
}

$output .= "</checks>";
$output .= "</monitor>";

header("Content-Type: text/xml");
echo $output;

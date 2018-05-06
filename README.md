# simplesamlphp-module-monitor
This module provides a framework for monitoring SimpleSAMLphp.

Using TestSuites and TestCases, virtually anything can be monitored.
TestSuites and TestSuites for the most common usecases ship with the module,
but you can easily add your own!

When you enable this module, a link to the monitoring-page will appear on the admin configuration-tab.
Add ?output=xml to the url to get an XML-representation of the monitoring-page, for use with your
3rd party monitoring system like SCOM / Nagios, or even load balancers that determine a node's health.
A JSON-formatted output is also possible using ?output=json in the url.

The XML-page will also set a HTTP reponse-code corresponding to the 'overall status':
- 200 - Everything is OK
- 417 - There is at least one warning
- 500 - There is at least one error

# Installation
- Run `composer.phar require tvdijen/simplesamlphp-module-monitor:dev-master`
- Copy `config-templates/module_monitor.php` to the SimpleSAML config-directory
- Enable the module by adding it to the `module.enable` directive in config.php

# 
[![Build Status](https://scrutinizer-ci.com/g/tvdijen/simplesamlphp-module-monitor/badges/build.png?b=master)](https://scrutinizer-ci.com/g/tvdijen/simplesamlphp-module-monitor/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tvdijen/simplesamlphp-module-monitor/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tvdijen/simplesamlphp-module-monitor/?branch=master)

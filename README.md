# simplesamlphp-module-monitor
This module provides a framework for monitoring SimpleSAMLphp.

Using TestSuites and TestCases, virtually anything can be monitored.
TestSuites and TestSuites for the most common usecases ship with the module,
but you can easily add your own!

When you enable this module, a link to the monitoring-page will appear on the admin configuration-tab.
Add ?xml to the url to get an XML-representation of the monitoring-page, for use with your
3rd party monitoring system like SCOM / Nagios, or even load balancers that determine a node's health.

The XML-page will also set a HTTP reponse-code corresponding to the 'overall status':
200 - Everything is OK
417 - The is at least one warning
500 - There is at least one error

# Installation
- Copy `config-templates/module_monitor.php` to the SimpleSAML config-directory
- Enable the module by adding it to the `module.enable` directive in config.php

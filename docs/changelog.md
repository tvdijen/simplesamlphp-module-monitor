Monitoring module changelog
=======================

<!-- {{TOC}} -->

This document lists the changes between versions of this donitoring module.
See the upgrade notes for specific information about upgrading.

## Version 2.5.4
Released 21-10-2018
- Fix a bug in the IDP's signing-certs check

## Version 2.5.3
Released 17-10-2018

Bugfixes:
- Fix namespaces for non-composer installs
- File permissions

Features:
- Added certificate-checks for the IDP's signing-certs

Non-functional:
- Changed namespace from \SimpleSAML\Module\monitor to \SimpleSAML\Modules\Monitor
- The 2.5 range will be the last one supporting SSP 1.15+1.16 and will only receive bugfixes.
   New releases starting from 2.6 will only support the future 1.17+ release

## Version 2.5.2
Released 4-10-2018

Bugfix: Remove a function-call that breaks the monitor

## Version 2.5.1
Released 4-10-2018

Bugfix: Fix erroneous test in LDAP TestSuite that caused bind/search tests to run only when connection failures had occured in an earlier test (instead of the other way around)

## Version 2.5.0
Released 2-10-2018

Bugfixes
- Sessions: Properly handle PHP sessions
- Certificates: Fall back to serialnumber for subject
- Fix missing quotes in TestSuite/Store.php
- Prevent tests from being marked as risky
- Silence LDAP-tests > debug = false

Changes
- Add testsuite for database-configuration
- Rename TestSuite\Configuration to TestSuite\CertificateConfiguration

## Version 2.4.2
Released 2-10-2018

Fix case where search.base is an array

## Version 2.4.1

Released 17-8-2018

### Bugfixes
  * Fixed a bug that caused negotiate-authsources to fail
  * Fixed the SQL storage testsuite

### Changes
  * Update composer.lock to address CVE-2018-14773
  * Added tests to syntax-check Twig-templates

## Version 2.4.0

Released 8-8-2018

### Features
  * Support for testing SQL session stores

### Changes
  * Leave JSON-formatted output to Symfony
  * Improve HTML-output readability
  * Renamed main script-file from monitor.php to index.php

### Bugfixes
  * Fixed a bug in calculating testsuite state, leading to incorrect overall state
  * Fixed Travis by explicitly requiring sudo
  * Fixed a bug that caused some certificates not to get parsed

## Version 2.3.0

Released 22-5.2018

### Changes
  * Lots of unit testing;  no functional changes.

## Version 2.2.0

Released 14-5-2018

### Features
  * More control over SSL connections on a per AuthSource-basis through the module configuration-file.

### Changes
  * Refactored some code to make unit testing possible or easier
  * Added lots of unit tests; coverage at +30% already!
  * Several minor bugfixes

Released 7-5-2018

### Changes
  * Fix JSON-formatted output; it didn't properly output the overall state

### New features
  * It's hardly a feature, but rudimentary PHP-syntax checking was added for unit testing purposes

## Version 2.1.0

Released 6-5-2018

### Changes
  * Changed the way `TestResult::arrayizeTestResult()` and `TestFactory::getArrayizeTestResults()` return results.
    It no longer includes the test's output by default.
  * Changed the way you can get formatted output from `?xml` to `?output=xml`.
  * Moved TestConfiguration to TestSuite. It cannot be used directly from within TestCases

### New features
  * Added support for JSON-formatted output.
  
## Version 2.0.1

Released 6-5-2018

### Changes
  * Fixed a bug where required/available modules were calculated incorrectly on CGI installs
  * Fixed a bug that caused metadata not the be checked at all
  * Fixed a bug where entityId would be shown as the subject for a metadata certificate
  * Fixed Negotiate TestCase as it never worked
  * Added this changelog

## Version 2.0.0

Released 4-5-2018

### Changes
  * Complete rewrite of the API; no functional changes.
  * Will no longer work on SimpleSAMLphp 1.14
  * Supports PHP 7

### Notes
  * There will be no more new releases of the 1.x branch after the 1.1.3 release; consider this branch deprecated

## Version 1.1.3

Released 5-5-2018

### Changes
  * Restore compatibility with SimpleSAMLphp 1.14

## Version 1.1.2

Released 3-5-2018

### Changes
  * Fixed a bug in the Negotiate testcase; it was never able to use the keytab-file.
  * Updated the license from `LGPL-3.0` to `LGPL-3.0-or-later`.

## Version 1.1.1

Released 28-11-2017

### Changes
  * Fixed a bug in `Monitor::setAvailableApacheModules()` where it would raise errors and call the `WARNING`-state
    when unable to determine the available modules. It will not error anymore and call the `SKIPPED`-state now.
  * Other minor bugfixes and code cleanup.
  
### New features
  * The LDAP-fallback will now be checked on `negotiate`-authsources.

## Version 1.1.0

Released 13-11-2017

### Changes
  * Errors in the `negotiate`-testsuite will not end up in the logs anymore to prevent log pollution.
  8 Other minor bugfixes and code cleanup
  
### New features
  * Added a new testsuite for monitoring metadata-configuration.


## Version 1.0.0

Released 24-10-2017

The first usable release

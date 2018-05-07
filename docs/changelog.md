Monitoring module changelog
=======================

<!-- {{TOC}} -->

This document lists the changes between versions of this donitoring module.
See the upgrade notes for specific information about upgrading.

## Version 2.1.1

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

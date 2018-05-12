<?php
/*
 * The configuration of SimpleSAMLphp monitor package
 */

$config = array (
    // Set to true to check all authsources, false to check none, or a list of authsources to check specific ones.
    'checkAuthSources' => true,

    // Specific configuration per authsource, like SSL contexts. List of authsourceId/options key/pair values or null to use default settings.
    'authSourceSpecifics' => null,

    // Set to true to check all metadata, false to check none, or a list of metadata-set/entityId key/pair values to check specific ones.
    'checkMetadata' => true,

    // Expiration warning is shown x days prior to expiration
    'certExpirationWarning' => 28,
);

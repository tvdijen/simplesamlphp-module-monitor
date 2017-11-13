<?php
/*
 * The configuration of SimpleSAMLphp monitor package
 */

$config = array (
    // Set to true to check all authsources, false to check none, or a list of authsources to check specific ones.
    'check_authsources' => true,

    // Set to true to check all metadata, false to check none, or a list of metadata-set/entityId key/pair values to check specific ones.
    'check_metadata' => true,

    // Expiration warning is shown x days prior to expiration
    'cert_expiration_warning' => 28,
);

<?php

namespace \SimpleSAML\Module\monitor\DependencyInjection;

final class Server extends \SimpleSAML\Module\monitor\DependencyInjectionFactory
{
    // The canonical list of acceptable server parameters
    /**
     * @var mixed
     */
    public $SERVER_NAME;
    public $SERVER_PORT;
    public $HTTP_AUTHORIZATION;
    public $SERVER_PROTOCOL;
}

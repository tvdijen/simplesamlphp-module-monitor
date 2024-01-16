<?php

declare(strict_types=1);

namespace SimpleSAML\Module\monitor\Controller;

use SimpleSAML\Configuration;
use SimpleSAML\Module\monitor\DependencyInjection;
use SimpleSAML\Module\monitor\Monitor;
use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestConfiguration;
use SimpleSAML\XHTML\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use function array_merge;

/**
 * Controller class for the monitor module.
 *
 * This class serves the different views available in the module.
 *
 * @package SimpleSAML\Module\monitor
 */
class MonitorController
{
    /** @var \SimpleSAML\Configuration */
    protected Configuration $config;

    /** @var \SimpleSAML\Configuration */
    protected Configuration $moduleConfig;

    /** @var \SimpleSAML\Configuration */
    protected Configuration $authsourceConfig;

    /** @var \SimpleSAML\Module\monitor\DependencyInjection */
    protected DependencyInjection $serverVars;

    /** @var \SimpleSAML\Module\monitor\DependencyInjection */
    protected DependencyInjection $requestVars;

    /** @var array */
    private array $healthInfo = [
        State::SKIPPED => ['SKIPPED', 'yellow'],
        State::FATAL   => ['FATAL',   'purple'],
        State::ERROR   => ['NOK',     'red'   ],
        State::NOSTATE => ['NOSTATE',   'cyan'],
        State::WARNING => ['WARNING', 'orange'],
        State::OK      => ['OK',      'green' ]
    ];

    /** @var \SimpleSAML\Module\monitor\TestConfiguration */
    protected TestConfiguration $testConfiguration;

    /** @var int */
    protected int $state;

    /** @var int */
    protected int $responseCode = 200;

    /** @var \SimpleSAML\Module\monitor\Monitor */
    protected Monitor $monitor;


    /**
     * Controller constructor.
     *
     * It initializes the global configuration and auth source configuration for the controllers implemented here.
     *
     * @param \SimpleSAML\Configuration              $config The configuration to use by the controllers.
     * @throws \Exception
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
        $this->moduleConfig = Configuration::getOptionalConfig('module_monitor.php');
        $this->authsourceConfig = Configuration::getOptionalConfig('authsources.php');

        $this->serverVars = new DependencyInjection($_SERVER);
        $this->requestVars = new DependencyInjection($_REQUEST);

        $this->testConfiguration = new TestConfiguration(
            $this->serverVars,
            $this->requestVars,
            $this->config,
            $this->authsourceConfig,
            $this->moduleConfig
        );
        $this->monitor = new Monitor($this->testConfiguration);
    }


    /**
     * Display the main monitoring page.
     *
     * @param string $format  Default is XHTML output
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function main(string $format): Response
    {
        $this->monitor->invokeTestSuites();

        $this->state = $this->monitor->getState();
        if ($this->state === State::OK) {
            $this->responseCode = 200;
        } elseif ($this->state === State::WARNING) {
            $this->responseCode = $this->moduleConfig->getOptionalInteger('warningStatusCode', 202);
        } else {
            $this->responseCode = $this->moduleConfig->getOptionalInteger('errorStatusCode', 500);
        }

        $results = $this->monitor->getResults();

        switch ($format) {
            case 'xml':
                $t = $this->processXml();
                break;
            case 'json':
                return $this->processJson($results);
            case 'text':
                $t = $this->processText();
                break;
            default:
                $t = new Template($this->config, 'monitor:monitor.twig');
                break;
        }

        $t->data['header'] = 'Monitor';
        $t->data = array_merge($t->data, $results);

        $t->data['overall'] = $this->state;
        $t->data['healthInfo'] = $this->healthInfo;
        $t->data['responseCode'] = $this->responseCode;

        return $t;
    }


    /**
     * @return \SimpleSAML\XHTML\Template
     */
    private function processXml(): Template
    {
        $t = new Template($this->config, 'monitor:monitor.xml.twig');
        $t->headers->set('Content-Type', 'text/xml');
        return $t;
    }


    /**
     * @param array $results
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    private function processJson(array $results): JsonResponse
    {
        return JsonResponse::create(
            ['overall' => $this->healthInfo[$this->state][0],
            'results' => $results],
            $this->responseCode
        );
    }


    /**
     * @return \SimpleSAML\XHTML\Template
     */
    private function processText(): Template
    {
        $t = new Template($this->config, 'monitor:monitor.text.twig');

        if ($this->state === State::OK) {
            $t->data['status'] = 'OK';
        } elseif ($this->state === State::WARNING) {
            $t->data['status'] = 'WARN';
        } else {
            $t->data['status'] = 'FAIL';
        }
        return $t;
    }
}

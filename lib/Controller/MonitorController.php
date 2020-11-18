<?php

namespace SimpleSAML\Module\monitor\Controller;

use SimpleSAML\Configuration;
use SimpleSAML\Module\monitor\DependencyInjection;
use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestConfiguration;
use SimpleSAML\Module\monitor\Monitor;
use SimpleSAML\XHTML\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller class for the monitor module.
 *
 * This class serves the different views available in the module.
 *
 * @package SimpleSAML\Module\Monitor
 */
class MonitorController
{
    /** @var \SimpleSAML\Configuration */
    protected $config;

    /** @var \SimpleSAML\Configuration */
    protected $moduleConfig;

    /** @var \SimpleSAML\Configuration */
    protected $authsourceConfig;

    /** @var \SimpleSAML\Module\monitor\DependencyInjection */
    protected $serverVars;

    /** @var \SimpleSAML\Module\monitor\DependencyInjection */
    protected $requestVars;

    /** @var array */
    private $healthInfo = [
        State::SKIPPED => ['SKIPPED', 'yellow'],
        State::FATAL   => ['FATAL',   'purple'],
        State::ERROR   => ['NOK',     'red'   ],
        State::NOSTATE => ['NOSTATE',   'cyan'],
        State::WARNING => ['WARNING', 'orange'],
        State::OK      => ['OK',      'green' ]
    ];

    /** @var \SimpleSAML\Module\monitor\TestConfiguration */
    protected $testConfiguration;

    /** @var int */
    protected $state;

    /** @var int */
    protected $responseCode = 200;

    /** @var \SimpleSAML\Module\monitor\Monitor */
    protected $monitor;


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
            $this->responseCode = $this->moduleConfig->getInteger('warningStatusCode', 202);
        } else {
            $this->responseCode = $this->moduleConfig->getInteger('errorStatusCode', 500);
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
                $t = new Template($this->config, 'Monitor:monitor.twig');
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
        $t = new Template($this->config, 'Monitor:monitor.xml.twig');
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
        $t = new Template($this->config, 'Monitor:monitor.text.twig');

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

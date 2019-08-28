<?php

namespace SimpleSAML\Module\monitor;

use \SimpleSAML\Modules\Monitor\DependencyInjection as DependencyInjection;
use \SimpleSAML\Modules\Monitor\State as State;
use \SimpleSAML\Modules\Monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Modules\Monitor\Monitor as Monitor;
use \SimpleSAML\Configuration as ApplicationConfiguration;
use \Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller class for the monitor module.
 *
 * This class serves the different views available in the module.
 *
 * @package SimpleSAML\Module\monitor
 */
class Controller
{
    /** @var \SimpleSAML\Configuration */
    protected $config;

    /** @var \SimpleSAML\Configuration */
    protected $moduleConfig;

    /** @var \SimpleSAML\Configuration */
    protected $authsourceConfig;

    /** @var \SimpleSAML\Modules\Monitor\DependencyInjection */
    protected $serverVars;

    /** @var \SimpleSAML\Modules\Monitor\DependencyInjection */
    protected $requestVars;

    /** @var array */
    static private $healthInfo = [
        State::SKIPPED => ['SKIPPED', 'yellow'],
        State::FATAL   => ['FATAL',   'purple'],
        State::ERROR   => ['NOK',     'red'   ],
        State::NOSTATE   => ['NOSTATE',   'cyan'  ],
        State::WARNING => ['WARNING', 'orange'],
        State::OK      => ['OK',      'green' ]
    ];

    /** @var \SimpleSAML\Module\Monitor\TestConfiguration */
    protected $testConfiguration;

    /** @var int */
    protected $state;

    /** @var int */
    protected $reponseCode = 200;

    /** @var \SimpleSAML\Module\Monitor\Monitor */
    protected $monitor;


    /**
     * Controller constructor.
     *
     * It initializes the global configuration and auth source configuration for the controllers implemented here.
     *
     * @param \SimpleSAML\Configuration              $config The configuration to use by the controllers.
     * @param \SimpleSAML\Monitor                    $monitor The monitor object to use by the controllers.
     *
     * @throws \Exception
     */
    public function __construct(
        Configuration $config
    ) {
        $this->config = $config;
        $this->moduleConfig = ApplicationConfiguration::getOptionalConfig('module_monitor.php');
        $this->authsourceConfig = ApplicationConfiguration::getOptionalConfig('authsources.php');

        $this->serverVars = new DependencyInjection($_SERVER);
        $this->requestVars = new DependencyInjection($_REQUEST);

        $this->testConfiguration = new TestConfiguration($this->serverVars, $this->requestVars, $this->config, $this->authsourceConfig, $this->moduleConfig);
        $this->monitor = new Monitor($this->testConfiguration);

        $this->state = $this->monitor->getState();
        if ($this->state === State::OK) {
            $this->responseCode = 200;
        } else if ($this->state === State::WARNING) {
            $this->responseCode = 417;
        } else {
            $this->responseCode = 500;
        }
    }


    /**
     * Display the main monitoring page.
     *
     * @param string $format  Default is XHTML output
     * @return \SimpleSAML\XHTML\Template
     */
    public function main($format)
    {
        $this->monitor->invokeTestSuites();
        $results = $this->monitor->getResults();

        switch ($this->requestVars->get('output')) {
            case 'xml':
                $t = $this->processXml();
                break;
            case 'json':
                return $this->processJson($results);
            case 'text':
                $t = $this->processText();
                break;
            default:
                $t = new \SimpleSAML\XHTML\Template($globalConfig, 'monitor:monitor.php');
                break;
        }

        $t->data['header'] = 'Monitor';
        $t->data = array_merge($t->data, $results);

        $t->data['overall'] = $this->state;
        $t->data['healthInfo'] = $this->healthInfo;
        $t->data['responseCode'] = $this->responseCode;

        $this->setStatusCode($this->responseCode);
        return $t;
    }


    /**
     * @return \SimpleSAML\XHTML\Template
     */
    private function processXml() {
        $t = new \SimpleSAML\XHTML\Template($this->config, 'monitor:monitor.xml.php');
        $t->headers->set('Content-Type', 'text/xml');
        return $t;
    }


    /**
     * @param array $results
     * @return \SimpleSAML\XHTML\Template
     */
    private function processJson(array $results) {
        return JsonResponse::create(['overall' => $this->healthInfo[$this->state][0], 'results' => $results], $this->responseCode);
    }


    /**
     * @return \SimpleSAML\XHTML\Template
     */
    private function processText() {
        $t = new \SimpleSAML\XHTML\Template($this->config, 'monitor:monitor.text.php');

        if ($this->state === State::OK) {
            $t->data['status'] = 'OK';
        } else if ($this->state === State::WARNING) {
            $t->data['status'] = 'WARN';
        } else {
            $t->data['status'] = 'FAIL';
        }
        return $t;
    }
}

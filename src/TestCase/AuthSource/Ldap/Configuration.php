<?php

declare(strict_types=1);

namespace SimpleSAML\Module\monitor\TestCase\AuthSource\Ldap;

use Exception;
use SimpleSAML\Module\ldap\Connector\Ldap as LdapConnector;
use SimpleSAML\Module\ldap\ConnectorInterface;
use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;

use function preg_match;
use function str_replace;

final class Configuration extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /** @var \SimpleSAML\Module\ConnectorInterface|null */
    private ?ConnectorInterface $connection = null;

    /** @var string */
    private string $hostname = '';

    /** @var string */
    private string $encryption = 'none';

    /** @var integer */
    private int $version = 3;

    /** @var integer */
    private int $timeout = 3;

    /** @var bool */
    private bool $referrals = false;

    /** @var bool */
    private bool $debug = false;

    /** @var array */
    private array $options = [];


    /**
     * @param \SimpleSAML\Module\monitor\TestData $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData): void
    {
        $authSourceData = $testData->getInputItem('authSourceData');
        $this->hostname = $authSourceData->getString('connection_string');
        $this->encryption = $authSourceData->getOptionalString('encryption', 'none');
        $this->version = $authSourceData->getOptionalInteger('version', 3);
        $this->timeout = $authSourceData->getOptionalInteger('timeout', 3);
        $this->referrals = $authSourceData->getOptionalBoolean('referrals', false);
        $this->debug = $authSourceData->getOptionalBoolean('debug', false);
        $this->options = $authSourceData->getOptionalArray(
            'opions',
            ['network_timeout' => $this->timeout, 'referrals' => $this->referrals],
        );

        $this->setSubject($this->hostname);

        parent::initialize($testData);
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        if (preg_match('/^(ldap[s]?:\/\/(.*))$/', $this->hostname, $matches)) {
            $connectString = $this->hostname;
        } else {
            $connectString = $this->hostname . ':' . $this->port;
        }

        $testResult = new TestResult('LDAP configuration', $connectString);

        try {
            $this->connection = new LdapConnector(
                $this->hostname,
                $this->encryption,
                $this->version,
                'ext_ldap',
                $this->debug,
                $this->options,
            );
            $state = State::OK;
        } catch (Exception $error) {
            $state = State::FATAL;
        }

        if (isset($error)) {
            // When you feed str_replace a string, outcome will be string too, but Psalm doesn't see it that way
            $msg = str_replace('Library - LDAP __construct(): ', '', $error->getMessage());
        } else {
            $msg = 'Configuration syntax OK';
            $testResult->addOutput($this->connection, 'connection');
        }

        $testResult->setState($state);
        $testResult->setMessage($msg);
        $this->setTestResult($testResult);
    }
}

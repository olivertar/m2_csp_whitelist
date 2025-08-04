<?php

namespace Orangecat\CspWhitelist\Plugin;

use Orangecat\CspWhitelist\Helper\Data;
use Magento\Csp\Api\Data\PolicyInterface;
use Magento\Csp\Model\Collector\CspWhitelistXmlCollector;
use Magento\Framework\Config\DataInterface as ConfigReader;
use Magento\Csp\Model\Policy\FetchPolicy;
use Magento\Csp\Model\Policy\FetchPolicyFactory;

class CspWhitelist
{
    /** @var ConfigReader */
    private $configReader;

    /** @var Data */
    private $helper;

    /** @var FetchPolicy */
    private $fetchPolicy;

    /**
     * Constructor
     *
     * @param ConfigReader $configReader
     * @param Data $helper
     * @param FetchPolicyFactory $fetchPolicy
     */
    public function __construct(
        ConfigReader       $configReader,
        Data               $helper,
        FetchPolicyFactory $fetchPolicy
    )
    {
        $this->configReader = $configReader;
        $this->helper = $helper;
        $this->fetchPolicy = $fetchPolicy;
    }

    /**
     * Around Collect
     *
     * @param CspWhitelistXmlCollector $subject
     * @param callable $proceed
     * @param PolicyInterface[] $defaultPolicies
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundCollect(
        CspWhitelistXmlCollector $subject,
        callable                 $proceed,
        array                    $defaultPolicies = []
    ): array
    {
        if (!$this->helper->isEnabled()) {
            return $proceed($defaultPolicies);
        }
        $customPolicies = $this->helper->getPolicies();
        $policies = $defaultPolicies;
        $config = $this->configReader->get(null);
        foreach ($config as $policyId => $values) {
            if (isset($customPolicies[$policyId])) {
                foreach ($customPolicies[$policyId] as $policy) {
                    $values['hosts'][] = $policy;
                }
            }

            $policies[] = $this->fetchPolicy->create([
                'id' => $policyId,
                'noneAllowed' => false,
                'hostSources' => $values['hosts'],
                'schemeSources' => [],
                'selfAllowed' => false,
                'inlineAllowed' => false,
                'evalAllowed' => false,
                'nonceValues' => [],
                'hashValues' => $values['hashes'],
                'dynamicAllowed' => false,
                'eventHandlersAllowed' => false
            ]);
        }

        return $policies;
    }
}

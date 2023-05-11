<?php

namespace Orangecat\CspWhitelist\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Helper\Context;

class Configuration extends AbstractHelper
{
    private const CONFIG_ENABLE = 'csp/general/enabled';

    private const CONFIG_POLICIES = 'csp/general/policies';

    /** @var Json */
    private $serialize;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Json $serialize
     */
    public function __construct(
        Context $context,
        Json $serialize
    ) {
        $this->serialize = $serialize;
        parent::__construct($context);
    }

    /**
     * Check to see if module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_ENABLE);
    }

    /**
     * Get a list of policies
     *
     * @return array
     */
    public function getPolicies(): array
    {
        $data = [];
        $policies = $this->scopeConfig->getValue(self::CONFIG_POLICIES);
        if (!$policies) {
            return $data;
        }
        foreach ($this->serialize->unserialize($policies) as $policy) {
            $data[$policy['policy']][] = $policy['value'];
        }
        return $data;
    }
}

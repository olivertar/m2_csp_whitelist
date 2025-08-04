<?php

declare(strict_types=1);

namespace Orangecat\CspWhitelist\Block\Adminhtml\Form\Field;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Csp\Api\PolicyCollectorInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Orangecat\CspWhitelist\Helper\Data;

class CspHeader extends Template implements RendererInterface
{
    private string $header = '';

    public function __construct(
        private readonly PolicyCollectorInterface $policyCollector,
        private readonly Data $helper,
        Context $context,
        array $data = []
    ) {
        $this->_template = 'Orangecat_CspWhitelist::cspHeader.phtml';
        parent::__construct($context, $data);
    }

    public function render(AbstractElement $element): string
    {
        $this->setData('html_id', $element->getHtmlId());
        $this->setData('label', $element->getData('label'));

        return $this->_toHtml();
    }

    public function getCurrentHeaderSize(): int
    {
        return strlen($this->getCspHeader());
    }

    public function getCspHeader(): string
    {
        if (empty($this->header)) {
            $cspHeader = '';
            $policies = $this->policyCollector->collect();

            foreach ($policies as $policy) {
                $value = $policy->getValue();
                $cspHeader .= $policy->getId().': '.$value.';'.PHP_EOL;
            }
            $this->header = $cspHeader;
        }
        return $this->header ;
    }

    public function isHeaderIsTooBig(): bool
    {
        $header = $this->getCspHeader();
        $currentHeaderSize = strlen($header);
        $maxHeaderSize = $this->helper->getMaxHeaderSize();

        $isHeaderSplittingEnabled = $this->helper->isHeaderSplittingEnabled();
        $headerIsTooBig = false;
        if ($isHeaderSplittingEnabled) {
            $headerParts = explode(PHP_EOL, $header);
            foreach ($headerParts as $headerPart) {
                $headerPartsSize = strlen($headerPart);
                if ($headerPartsSize > $maxHeaderSize) {
                    $headerIsTooBig = true;
                    break;
                }
            }
        } else {
            $headerIsTooBig = $currentHeaderSize > $maxHeaderSize;
        }
        return $headerIsTooBig;
    }

    public function getConfig()
    {
        return $this->helper;
    }

}

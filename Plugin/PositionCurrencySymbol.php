<?php declare(strict_types=1);

namespace EW\CurrencySymbolPosition\Plugin;

use Magento\Directory\Model\Currency;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Currency\Exception\CurrencyException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class PositionCurrencySymbol
{
    const XML_PATH_CURRENCY_SYMBOL_POSITION = 'currency/options/symbol_position';

    /**
     * @param CurrencyInterface $localeCurrency
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected CurrencyInterface $localeCurrency,
        protected ScopeConfigInterface $scopeConfig,
        protected StoreManagerInterface $storeManager,
        protected LoggerInterface $logger,
    ) {
    }

    /**
     * @param Currency $subject
     * @param string $result
     * @return string
     */
    public function afterFormatTxt(Currency $subject, string $result): string
    {
        try {
            $currencySymbol = $this->localeCurrency->getCurrency($subject->getCode())->getSymbol();

            // Get current store view id
            $currentStoreId = $this->storeManager->getStore()->getId();

            // Get currency symbol position config. value for the current store view
            $currencySymbolRight = $this->scopeConfig->getValue(
                self::XML_PATH_CURRENCY_SYMBOL_POSITION,
                ScopeInterface::SCOPE_STORE,
                $currentStoreId
            );

            // Only if currency symbol position is set to right, format price text
            if ($currencySymbolRight) {
                // The original $result string is already trimmed, so for the regex pattern to get a correct match
                // we need to provide the currency symbol without any white space, and not $currencySymbol as is.
                $trimmedCurrencySymbol = trim($currencySymbol);

                $formattedResult = preg_replace(
                    "/^$trimmedCurrencySymbol\s*(.+)$/",
                    "$1$currencySymbol",
                    $result
                );

                return $formattedResult ?: $result;
            }
        } catch (CurrencyException $e) {
            $this->logger->debug('Currency Error on symbol retrieval: ' . $e->getMessage());
        } catch (NoSuchEntityException $e) {
            $this->logger->debug('No Entity Found on store retrieval: ' . $e->getMessage());
        }

        return $result;
    }
}

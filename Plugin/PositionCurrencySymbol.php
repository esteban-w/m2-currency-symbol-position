<?php declare(strict_types=1);

namespace EW\CurrencySymbolPosition\Plugin;

use Magento\Directory\Model\Currency;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use EW\CurrencySymbolPosition\Model\Config\Source\CurrencySymbolPosition;
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
            $trimmedCurrencySymbol = trim($currencySymbol);

            // Check if $result indeed contains a currency symbol, otherwise there is nothing to format
            if (str_contains($result, $trimmedCurrencySymbol)) {
                // Get current store view id
                $currentStoreId = $this->storeManager->getStore()->getId();

                // Get currency symbol position config. value for the current store view
                $currencySymbolPosition = (int)$this->scopeConfig->getValue(
                    self::XML_PATH_CURRENCY_SYMBOL_POSITION,
                    ScopeInterface::SCOPE_STORE,
                    $currentStoreId
                );

                // Only if currency symbol position is set format price text
                if ($currencySymbolPosition) {
                    $pattern = sprintf(
                        '/^(?:\%s)*\s*([^\s%s]+)\s*(?:\%s)*$/',
                        $trimmedCurrencySymbol,
                        $trimmedCurrencySymbol,
                        $trimmedCurrencySymbol
                    );
                    $replacementFormat =
                        $currencySymbolPosition === CurrencySymbolPosition::CURRENCY_SYMBOL_POSITION_RIGHT
                        ? '$1%s'
                        : '%s$1';
                    $replacement = sprintf($replacementFormat, $currencySymbol);

                    $formattedResult = preg_replace(
                        $pattern,
                        $replacement,
                        $result
                    );

                    return $formattedResult ?: $result;
                }
            }
        } catch (CurrencyException $e) {
            $this->logger->debug('Currency Error on symbol retrieval: ' . $e->getMessage());
        } catch (NoSuchEntityException $e) {
            $this->logger->debug('No Entity Found on store retrieval: ' . $e->getMessage());
        }

        return $result;
    }
}

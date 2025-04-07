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
    const POSITION_LEFT = 'left';
    const POSITION_RIGHT = 'right';
    const POSITION_DEFAULT = 'default';

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
            $escapedSymbol = preg_quote(trim($currencySymbol), '/');
            
            // Get current store view id
            $currentStoreId = $this->storeManager->getStore()->getId();

            
            $position = $this->scopeConfig->getValue(
                self::XML_PATH_CURRENCY_SYMBOL_POSITION,
                ScopeInterface::SCOPE_STORE,
                $currentStoreId
            ) ?? self::POSITION_DEFAULT;

            return match ($position) {
                self::POSITION_RIGHT => $this->moveSymbolRightIfNeeded($result, $escapedSymbol, $currencySymbol),
                self::POSITION_LEFT => $this->moveSymbolLeftIfNeeded($result, $escapedSymbol, $currencySymbol),
                default => $result,
            };


        } catch (CurrencyException $e) {
            $this->logger->debug('Currency Error on symbol retrieval: ' . $e->getMessage());
        } catch (NoSuchEntityException $e) {
            $this->logger->debug('No Entity Found on store retrieval: ' . $e->getMessage());
        }

        return $result;
    }

    private function moveSymbolRightIfNeeded(string $result, string $escapedSymbol, string $rawSymbol): string
    {
        if (preg_match("/^{$escapedSymbol}\s?(.+)\$/", $result)) {
            $replaced = preg_replace(
                "/^{$escapedSymbol}\s?(.+)\$/",
                "\$1{$rawSymbol}",
                $result
            );
            return $replaced !== null && $replaced !== $result ? $replaced : $result;
        }
        return $result;
    }

    private function moveSymbolLeftIfNeeded(string $result, string $escapedSymbol, string $rawSymbol): string
    {
        if (preg_match("/^(.+?)\s?{$escapedSymbol}\$/", $result)) {
            $replaced = preg_replace(
                "/^(.+?)\s?{$escapedSymbol}\$/",
                "{$rawSymbol} \$1",
                $result
            );
            return $replaced !== null && $replaced !== $result ? $replaced : $result;
        }
        return $result;
    }
}

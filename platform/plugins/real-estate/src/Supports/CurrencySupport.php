<?php

namespace Botble\RealEstate\Supports;

use Botble\RealEstate\Models\Currency;
use Botble\RealEstate\Repositories\Interfaces\CurrencyInterface;
use Illuminate\Support\Collection;

class CurrencySupport
{
    /**
     * @var Currency
     */
    protected $currency;

    /**
     * @var Currency
     */
    protected $defaultCurrency = null;

    /**
     * @var Collection
     */
    protected $currencies = [];

    /**
     * @param Currency $currency
     */
    public function setApplicationCurrency(Currency $currency)
    {
        $this->currency = $currency;

        if (session('currency') == $currency->title) {
            return;
        }

        session(['currency' => $currency->title]);
    }

    /**
     * @return Currency
     */
    public function getApplicationCurrency()
    {
        $currency = $this->currency;

        if (empty($currency)) {
            if (session('currency')) {
                if ($this->currencies && $this->currencies instanceof Collection) {
                    $currency = $this->currencies->where('title', session('currency'))->first();
                } else {
                    $currency = app(CurrencyInterface::class)->getFirstBy(['title' => session('currency')]);
                }
            }

            if (!$currency) {
                $currency = $this->getDefaultCurrency();
            }

            $this->currency = $currency;
        }

        return $currency;
    }

    /**
     * @return Currency
     */
    public function getDefaultCurrency()
    {
        $currency = $this->defaultCurrency;

        if ($currency) {
            return $currency;
        }

        if ($this->currencies && $this->currencies instanceof Collection) {
            $currency = $this->currencies->where('is_default', 1)->first();
        }

        if (!$currency) {
            $currency = app(CurrencyInterface::class)->getFirstBy(['is_default' => 1]);
        }

        if (!$currency) {
            $currency = app(CurrencyInterface::class)->getFirstBy([]);
        }

        if (!$currency) {
            $currency = new Currency([
                'title'            => 'USD',
                'symbol'           => '$',
                'is_prefix_symbol' => true,
                'order'            => 0,
                'decimals'         => 2,
                'is_default'       => true,
                'exchange_rate'    => 1,
            ]);
        }

        $this->defaultCurrency = $currency;

        return $this->defaultCurrency;
    }

    /**
     * @return Collection
     */
    public function currencies(): Collection
    {
        if (!$this->currencies instanceof Collection) {
            $this->currencies = collect([]);
        }

        if ($this->currencies->count() == 0) {
            $this->currencies = app(CurrencyInterface::class)->getAllCurrencies();
        }

        return $this->currencies;
    }
}

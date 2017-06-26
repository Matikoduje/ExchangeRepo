<?php
namespace ExchangeBundle\Services;

use ExchangeBundle\Entity\Wallet;

class WalletService
{
    public function serializeData(Wallet $wallet)
    {
        return array(
            'GBP' => $wallet->getBritishPound(),
            'EUR' => $wallet->getEuro(),
            'USD' => $wallet->getUSDollar(),
            'CZK' => $wallet->getCzechKoruna(),
            'PLN' => $wallet->getPolishZloty(),
            'RUB' => $wallet->getRussianRuble(),
            'CHF' => $wallet->getSwissFranc(),
            'isActive' => $wallet->getIsActive()
        );
    }
}
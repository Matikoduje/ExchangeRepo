<?php
namespace ExchangeBundle\Services;

use Symfony\Component\HttpFoundation\Request;

class CheckApiService
{
    public function checkData($time, $currency, Request $request)
    {
        $value = array(
            'purchasePrice' => null,
            'sellPrice' => null
        );

        $session = $request->getSession();

        $data = $session->get('dataFromRequest');

        $data2 = array(
            'publicationDate' => null
        );

        if ($session->has('dataFromRequest2')) {
            $data2 = $session->get('dataFromRequest2');
        }

        if ($data['publicationDate'] == $time ) {
            foreach ($data['items'] as $item) {
                if ($item['code'] === $currency) {
                    $value['purchasePrice'] = round($item['purchasePrice'],2);
                    $value['sellPrice'] = round($item['sellPrice'],2);
                }
            }
        } else if ($data2['publicationDate'] == $time) {
            foreach ($data2['items'] as $item) {
                if ($item['code'] === $currency) {
                    $value['purchasePrice'] = round($item['purchasePrice'],2);
                    $value['sellPrice'] = round($item['sellPrice'],2);
                }
            }
        } else {
            return false;
        }

        if ($value['purchasePrice'] == null || $value['sellPrice'] == null) {
            return false;
        }

        return $value;
    }
}
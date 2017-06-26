<?php
namespace ExchangeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Security("is_granted('ROLE_USER')")
 */
class ApiController extends Controller
{
    /**
     * @Route("api/wallet", name="getWallet")
     * @Method("GET")
     */
    public function getWalletAction()
    {
        $wallet = $this->getUser()->getWallet();
        $data = $this->get('app.wallet_service')->serializeData($wallet);
        $response = new JsonResponse($data, Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("api/sell", name="sell")
     * @Method("POST")
     */
    public function sellCurrenciesAction(Request $request)
    {
        $data = array(
            "message" => null
        );

        try {
            $data = $this->checkPostData($request);
            $this->prepareAndSave($data, 'sell');
            $response = new JsonResponse($data, Response::HTTP_OK);
            return $response;
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage();
            $response = new JsonResponse($data, Response::HTTP_BAD_REQUEST);
            return $response;
        }
    }

    /**
     * @Route("api/buy", name="buy")
     * @Method("POST")
     */
    public function buyCurrenciesAction(Request $request)
    {
        $data = array(
            "message" => null
        );

        try {
            $data = $this->checkPostData($request);
            $this->prepareAndSave($data, 'buy');
            $response = new JsonResponse($data, Response::HTTP_OK);
            return $response;
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage();
            $response = new JsonResponse($data, Response::HTTP_BAD_REQUEST);
            return $response;
        }
    }

    public function checkPostData(Request $request)
    {
        if ($request->request->has('time')
            && $request->request->has('currency')
            &&  $request->request->has('amount')) {

            $time = $request->request->get('time');
            $currency = $request->request->get('currency');
            $amount = $request->request->get('amount');

            $checkAmount = false;

            if (preg_match('/^[0-9]+$/', $amount)) {
                $checkAmount = true;
            }

            if ($amount <= 0) {
                throw new Exception("Please enter a number greater than zero");
            }
            $value = $this->get('app.check_service')->checkData($time, $currency, $request);

            if ($value != false && $checkAmount == true) {
                return array(
                    'currency' => $currency,
                    'value' => $value,
                    'amount' => $amount
                );
            }

            throw new Exception("Error: Data don't pass validation");
        }

        throw new Exception("Error: Don\'t have all data in request");
    }

    public function prepareAndSave($data, $action)
    {
        $wallet = $this->getUser()->getWallet();
        $cantor = $this->getDoctrine()
            ->getRepository('ExchangeBundle:Cantor')->find(1);

        $currencyAmount = $wallet->getCurrency($data['currency']);
        $cantorCurrencyAmount = $cantor->getCurrency($data['currency']);

        if ($action == 'buy') {
            $plnValue = $wallet->getPolishZloty() - ($data['value']['sellPrice'] * $data['amount']);
            $newCurrencyAmount = $currencyAmount + $data['amount'];
            $newCantorCurrencyAmount = $cantorCurrencyAmount - $data['amount'];
            if ($plnValue < 0) {
                throw new Exception("You don't have enough money");
            }
            if ($newCantorCurrencyAmount < 0) {
                throw new Exception("Exchange do not have enough " . $data['currency']);
            }
        } else {
            $plnValue = $wallet->getPolishZloty() + ($data['value']['purchasePrice'] * $data['amount']);
            $newCurrencyAmount = $currencyAmount - $data['amount'];
            $newCantorCurrencyAmount = $currencyAmount + $data['amount'];
            if ($newCurrencyAmount < 0) {
                throw new Exception("You can't sell more " . $data['currency'] . " then You have");
            }
        }

        $em = $this->getDoctrine()->getManager();

        $conn = $this->getDoctrine()->getConnection();
        $conn->beginTransaction();
        try {
            switch ($data['currency']) {
                case "GBP":
                    $wallet->setBritishPound($newCurrencyAmount);
                    $cantor->setBritishPound($newCantorCurrencyAmount);
                    break;
                case "EUR":
                    $wallet->setEuro($newCurrencyAmount);
                    $cantor->setEuro($newCantorCurrencyAmount);
                    break;
                case "USD":
                    $wallet->setUSDollar($newCurrencyAmount);
                    $cantor->setUSDollar($newCantorCurrencyAmount);
                    break;
                case "CZK":
                    $wallet->setCzechKoruna($newCurrencyAmount);
                    $cantor->setCzechKoruna($newCantorCurrencyAmount);
                    break;
                case "RUB":
                    $wallet->setRussianRuble($newCurrencyAmount);
                    $cantor->setRussianRuble($newCantorCurrencyAmount);
                    break;
                case "CHF":
                    $wallet->setSwissFranc($newCurrencyAmount);
                    $cantor->setSwissFranc($newCantorCurrencyAmount);
                    break;
            }
            $wallet->setPolishZloty($plnValue);
            $em->persist($wallet);
            $em->persist($cantor);
            $em->flush();
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
            throw new Exception('Error: Problem with database.');
        }
    }

    /**
     * @Route("api/save", name="save")
     * @Method("GET")
     */
    public function saveData(Request $request)
    {
        $response = array(
            'msg' => 'OK'
        );

        $data = file_get_contents('http://webtask.future-processing.com:8068/currencies');
        $data = json_decode($data, true);

        $session = $request->getSession();

        if (!$session->has('counter')) {
            $session->set('counter', 1);
        } else {
            $x = $session->get('counter');
            $session->set('counter', $x++);
        }

        if ($session->get('counter') % 2 != 0) {
            $session->set('dataFromRequest', $data);
        } else {
            $session->set('dataFromRequest2', $data);
        }

        $response = new JsonResponse($response, Response::HTTP_OK);
        return $response;
    }
}

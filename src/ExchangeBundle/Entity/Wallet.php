<?php
namespace ExchangeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="ExchangeBundle\Repository\WalletRepository")
 * @ORM\Table(name="wallet")
 */
class Wallet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="GBP", type="integer")
     * @Assert\GreaterThanOrEqual(
     *     value = 0,
     *     message="This value should be greater than or equal to 0."
     * )
     * @Assert\NotBlank(message="This value should not be blank.")
     */
    private $britishPound;

    /**
     * @ORM\Column(name="EUR", type="integer")
     * @Assert\GreaterThanOrEqual(
     *     value = 0,
     *     message="This value should be greater than or equal to 0."
     * )
     * @Assert\NotBlank(message="This value should not be blank.")
     */
    private $euro;

    /**
     * @ORM\Column(name="USD", type="integer")
     * @Assert\GreaterThanOrEqual(
     *     value = 0,
     *     message="This value should be greater than or equal to 0."
     * )
     * @Assert\NotBlank(message="This value should not be blank.")
     */
    private $uSDollar;

    /**
     * @ORM\Column(name="CZK", type="integer")
     * @Assert\GreaterThanOrEqual(
     *     value = 0,
     *     message="This value should be greater than or equal to 0."
     * )
     * @Assert\NotBlank(message="This value should not be blank.")
     */
    private $czechKoruna;

    /**
     * @ORM\Column(name="PLN", type="decimal", precision=11, scale=2)
     * @Assert\GreaterThanOrEqual(
     *     value = 0,
     *     message="This value should be greater than or equal to 0."
     * )
     * @Assert\NotBlank(message="This value should not be blank.")
     */
    private $polishZloty;

    /**
     * @ORM\Column(name="CHF", type="integer")
     * @Assert\GreaterThanOrEqual(
     *     value = 0,
     *     message="This value should be greater than or equal to 0."
     * )
     * @Assert\NotBlank(message="This value should not be blank.")
     */
    private $swissFranc;

    /**
     * @ORM\Column(name="RUB", type="integer")
     * @Assert\GreaterThanOrEqual(
     *     value = 0,
     *     message="This value should be greater than or equal to 0."
     * )
     * @Assert\NotBlank(message="This value should not be blank.")
     */
    private $russianRuble;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    public function __construct()
    {
        $this->britishPound = 0;
        $this->euro = 0;
        $this->uSDollar = 0;
        $this->czechKoruna = 0;
        $this->polishZloty = 0;
        $this->russianRuble = 0;
        $this->swissFranc = 0;
        $this->isActive = false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBritishPound()
    {
        return $this->britishPound;
    }

    public function setBritishPound($britishPound)
    {
        $this->britishPound = $britishPound;
    }

    public function getEuro()
    {
        return $this->euro;
    }

    public function setEuro($euro)
    {
        $this->euro = $euro;
    }

    public function getUSDollar()
    {
        return $this->uSDollar;
    }

    public function setUSDollar($uSDollar)
    {
        $this->uSDollar = $uSDollar;
    }

    public function getCzechKoruna()
    {
        return $this->czechKoruna;
    }

    public function setCzechKoruna($czechKoruna)
    {
        $this->czechKoruna = $czechKoruna;
    }

    public function getPolishZloty()
    {
        return $this->polishZloty;
    }

    public function setPolishZloty($polishZloty)
    {
        $this->polishZloty = $polishZloty;
    }

    public function getSwissFranc()
    {
        return $this->swissFranc;
    }

    public function setSwissFranc($swissFranc)
    {
        $this->swissFranc = $swissFranc;
    }

    public function getRussianRuble()
    {
        return $this->russianRuble;
    }

    public function setRussianRuble($russianRuble)
    {
        $this->russianRuble = $russianRuble;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function checkIsActive()
    {
        if ($this->sum() == 0) {
            $this->isActive = false;
        } else {
            $this->isActive = true;
        }
    }

    private function sum()
    {
        $sum = $this->polishZloty + $this->czechKoruna + $this->euro
            + $this->britishPound + $this->uSDollar + $this->swissFranc
            + $this->russianRuble;
        return $sum;
    }

    public function getCurrency($currency)
    {
        $cur = null;

        if ($currency == "GBP") {
            $cur = $this->getBritishPound();
        } else if ($currency == 'EUR') {
            $cur = $this->getEuro();
        } else if ($currency == 'USD') {
            $cur = $this->getUSDollar();
        } else if ($currency == 'CZK') {
            $cur = $this->getCzechKoruna();
        } else if ($currency == 'RUB') {
            $cur = $this->getRussianRuble();
        } else if ($currency == 'CHF') {
            $cur = $this->getSwissFranc();
        }

        return $cur;
    }
}

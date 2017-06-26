<?php
namespace ExchangeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ExchangeBundle\Repository\CantorRepository")
 * @ORM\Table(name="cantor")
 */
class Cantor
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="GBP", type="integer")
     */
    private $britishPound;

    /**
     * @ORM\Column(name="EUR", type="integer")
     */
    private $euro;

    /**
     * @ORM\Column(name="USD", type="integer")
     */
    private $uSDollar;

    /**
     * @ORM\Column(name="CZK", type="integer")
     */
    private $czechKoruna;

    /**
     * @ORM\Column(name="CHF", type="integer")
     */
    private $swissFranc;

    /**
     * @ORM\Column(name="RUB", type="integer")
     */
    private $russianRuble;

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

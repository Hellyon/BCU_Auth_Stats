<?php
/**
 * Created by PhpStorm.
 * User: ilbenjel
 * Date: 23/05/18
 * Time: 16:18
 */

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Recherche
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("\DateTime")
     */
    protected $debut;
    /**
     * @Assert\NotBlank()
     * @Assert\Type("\DateTime")
     */
    protected $fin;
    /**
     * @return \DateTime
     */
    public function getDebut(): \DateTime
    {
        return $this->debut;
    }

    /**
     * @param mixed $debut
     */
    public function setDebut($debut): void
    {
        $this->debut = $debut;
    }

    /**
     * @return \DateTime
     */
    public function getFin(): \DateTime
    {
        return $this->fin;
    }

    /**
     * @param mixed $fin
     */
    public function setFin($fin): void
    {
        $this->fin = $fin;
    }

}
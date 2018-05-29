<?php
/**
 * Created by PhpStorm.
 * User: ilbenjel
 * Date: 23/05/18
 * Time: 16:18
 */

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class Recherche
 * @package App\Entity
 * @Assert\Callback("validate")
 */
class Recherche
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("\DateTime")
     * @Assert\LessThanOrEqual("today UTC")
     */
    protected $debut;
    /**
     * @Assert\Type("\DateTime")
     * @Assert\LessThanOrEqual("today UTC")
     */
    protected $fin;
    /**
     * @return \DateTime
     */
    public function getDebut(): ?\DateTime
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
    public function getFin(): ?\DateTime
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

    public function validate(ExecutionContextInterface $context, $payload){
        if($this->getFin()){
            if($this->getDebut()->getTimestamp() > $this->getFin()->getTimestamp()){
                $context->buildViolation('La date de début doit être inférieure à la date de fin')
                    ->addViolation();
            }
        }
    }
}
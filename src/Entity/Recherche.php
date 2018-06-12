<?php
/**
 * Created by PhpStorm.
 * User: ilbenjel
 * Date: 23/05/18
 * Time: 16:18.
 */

namespace App\Entity;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Recherche.
 *
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
     * @Assert\Type("App\Entity\Poste")
     */
    protected $poste;
    /**
     * @Assert\Type("App\Entity\Site")
     */
    protected $site;
    /**
     * @Assert\Type("String")
     */
    protected $type;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * type Setter.
     *
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * Poste Getter.
     *
     * @return null|Poste
     */
    public function getPoste(): ?Poste
    {
        return $this->poste;
    }

    /**
     * poste Setter.
     *
     * @param Poste $poste
     */
    public function setPoste(Poste $poste): void
    {
        $this->poste = $poste;
    }

    /**
     * site Getter.
     *
     * @return null|Site
     */
    public function getSite(): ?Site
    {
        return $this->site;
    }

    /**
     * site Setter.
     *
     * @param Site $site
     */
    public function setSite(Site $site): void
    {
        $this->site = $site;
    }

    /**
     * validates the data sent from the form.
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->getFin() && $this->getDebut()) {
            if ($this->getDebut()->getTimestamp() > $this->getFin()->getTimestamp()) {
                $context->buildViolation('La date de début doit être inférieure à la date de fin')
                    ->addViolation();
            }
        }
        if ('poste' == $this->getType() && !$this->getPoste()) {
            $context->buildViolation('Aucun poste sélectionné !')
                ->addViolation();
        }
        if ('site' == $this->getType() && !$this->getSite()) {
            $context->buildViolation('Aucun site sélectionné !')
                ->addViolation();
        }
    }

    /**
     * fin Getter.
     *
     * @return null|\DateTime
     */
    public function getFin(): ?\DateTime
    {
        return $this->fin;
    }

    /**
     * fin Setter.
     *
     * @param null|\DateTime $fin
     */
    public function setFin(?\DateTime $fin): void
    {
        $this->fin = $fin;
    }

    /**
     * debut Getter.
     *
     * @return \DateTime
     */
    public function getDebut(): \DateTime
    {
        return $this->debut;
    }

    /**
     * debut Setter.
     *
     * @param null|\DateTime $debut
     */
    public function setDebut(\DateTime $debut): void
    {
        $this->debut = $debut;
    }
}

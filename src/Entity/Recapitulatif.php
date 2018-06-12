<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Recapitulatif.
 *
 * @ORM\Table(name="Recapitulatif", indexes={@ORM\Index(name="code_poste", columns={"code_poste"})})
 * @ORM\Entity(repositoryClass="App\Repository\RecapitulatifRepository")
 */
class Recapitulatif
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_recap", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idRecap;

    /**
     * @var int
     *
     * @ORM\Column(name="duree_cumul", type="integer", nullable=false)
     */
    private $dureeCumul = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="nb_connexions", type="integer", nullable=false)
     */
    private $nbConnexions = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="duree_ouverture", type="integer", nullable=false)
     */
    private $dureeOuverture;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var Poste
     *
     * @ORM\ManyToOne(targetEntity="Poste")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="code_poste", referencedColumnName="code_poste")
     * })
     */
    private $codePoste;

    /**
     * idRecap Getter.
     *
     * @return int
     */
    public function getIdRecap(): int
    {
        return $this->idRecap;
    }

    /**
     * dureeCumul Getter.
     *
     * @return int
     */
    public function getDureeCumul(): int
    {
        return $this->dureeCumul;
    }

    /**
     * nbConnexions Getter.
     *
     * @return int
     */
    public function getNbConnexions(): int
    {
        return $this->nbConnexions;
    }

    /**
     * dureeOuverture Getter.
     *
     * @return int
     */
    public function getDureeOuverture(): int
    {
        return $this->dureeOuverture;
    }

    /**
     * date Getter.
     *
     * @return \DateTimeInterface
     */
    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    /**
     * codePoste Getter.
     *
     * @return Poste
     */
    public function getCodePoste(): Poste
    {
        return $this->codePoste;
    }
}

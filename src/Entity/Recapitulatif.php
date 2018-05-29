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

    public function getIdRecap(): ?int
    {
        return $this->idRecap;
    }

    public function getDureeCumul(): ?int
    {
        return $this->dureeCumul;
    }

    public function getNbConnexions(): ?int
    {
        return $this->nbConnexions;
    }

    public function getDureeOuverture(): ?int
    {
        return $this->dureeOuverture;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function getCodePoste(): ?Poste
    {
        return $this->codePoste;
    }
}

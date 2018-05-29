<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Session.
 *
 * @ORM\Table(name="Session", indexes={@ORM\Index(name="code_poste", columns={"code_poste"})})
 * @ORM\Entity
 */
class Session
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_session", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idSession;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_debut", type="time", nullable=false)
     */
    private $heureDebut;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="heure_fin", type="time", nullable=true, options={"default"="NULL"})
     */
    private $heureFin = 'NULL';

    /**
     * @var string
     *
     * @ORM\Column(name="utilisateur", type="string", length=12, nullable=false, options={"fixed"=true})
     */
    private $utilisateur;

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
}

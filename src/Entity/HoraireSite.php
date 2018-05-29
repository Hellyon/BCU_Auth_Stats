<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HoraireSite.
 *
 * @ORM\Table(name="Horaire_Site", indexes={@ORM\Index(name="id_site", columns={"id_site"}), @ORM\Index(name="IDX_892E607A655594C6", columns={"id_horaire"})})
 * @ORM\Entity
 */
class HoraireSite
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $date;

    /**
     * @var Horaire
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Horaire")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_horaire", referencedColumnName="id_horaire")
     * })
     */
    private $idHoraire;

    /**
     * @var Site
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Site")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_site", referencedColumnName="id_site")
     * })
     */
    private $idSite;
}

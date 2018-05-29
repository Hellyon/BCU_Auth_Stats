<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Horaire.
 *
 * @ORM\Table(name="Horaire")
 * @ORM\Entity
 */
class Horaire
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_horaire", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idHoraire;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="heure_ouverture", type="time", nullable=true, options={"default"="NULL"})
     */
    private $heureOuverture = 'NULL';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="heure_fermeture", type="time", nullable=true, options={"default"="NULL"})
     */
    private $heureFermeture = 'NULL';
}

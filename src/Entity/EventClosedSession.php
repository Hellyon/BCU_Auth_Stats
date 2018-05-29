<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventClosedSession
 *
 * @ORM\Table(name="Event_Closed_Session", indexes={@ORM\Index(name="IDX_B846247BED97CA4", columns={"id_session"})})
 * @ORM\Entity
 */
class EventClosedSession
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false, options={"default"="current_timestamp()"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $date = 'current_timestamp()';

    /**
     * @var int
     *
     * @ORM\Column(name="semaines_revues", type="integer", nullable=false)
     */
    private $semainesRevues;

    /**
     * @var int
     *
     * @ORM\Column(name="temps_moyen", type="integer", nullable=false)
     */
    private $tempsMoyen;

    /**
     * @var Session
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Session")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_session", referencedColumnName="id_session")
     * })
     */
    private $idSession;
}

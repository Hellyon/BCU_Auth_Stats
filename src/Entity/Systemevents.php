<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Systemevents
 *
 * @ORM\Table(name="SystemEvents")
 * @ORM\Entity
 */
class Systemevents
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="DeviceReportedTime", type="datetime", nullable=true, options={"default"="NULL"})
     */
    private $devicereportedtime = 'NULL';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ReceivedAt", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $receivedat = 'current_timestamp()';

    /**
     * @var string|null
     *
     * @ORM\Column(name="FromHost", type="string", length=8, nullable=true, options={"default"="NULL","fixed"=true})
     */
    private $fromhost = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="FromHost_IP", type="string", length=15, nullable=true, options={"default"="NULL","fixed"=true})
     */
    private $fromhostIp = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="Message", type="text", length=65535, nullable=true, options={"default"="NULL"})
     */
    private $message = 'NULL';

    /**
     * @var bool
     *
     * @ORM\Column(name="Type", type="boolean", nullable=false)
     */
    private $type = '0';
}

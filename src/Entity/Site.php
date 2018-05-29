<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Site.
 *
 * @ORM\Table(name="Site", uniqueConstraints={@ORM\UniqueConstraint(name="code_site", columns={"code_site"})})
 * @ORM\Entity
 */
class Site
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_site", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idSite;
    /**
     * @var string
     *
     * @ORM\Column(name="code_site", type="string", length=3, nullable=false, options={"fixed"=true})
     */
    private $codeSite;
    /**
     * @var string|null
     *
     * @ORM\Column(name="nom_site", type="string", length=30, nullable=true, options={"default"="NULL"})
     */
    private $nomSite = 'NULL';

    /**
     * @return int
     */
    public function getIdSite(): int
    {
        return $this->idSite;
    }

    /**
     * @return null|string
     */
    public function getNomSite(): ?string
    {
        return $this->nomSite;
    }
}

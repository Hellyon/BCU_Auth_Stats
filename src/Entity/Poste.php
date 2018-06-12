<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Poste.
 *
 * @ORM\Table(name="Poste", uniqueConstraints={@ORM\UniqueConstraint(name="code_poste", columns={"code_poste"}), @ORM\UniqueConstraint(name="ip", columns={"ip"})}, indexes={@ORM\Index(name="id_site", columns={"id_site"})})
 * @ORM\Entity(repositoryClass="App\Repository\PosteRepository") */
class Poste
{
    /**
     * @var string
     *
     * @ORM\Column(name="code_poste", type="string", length=8, nullable=false, options={"fixed"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $codePoste;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=15, nullable=false, options={"fixed"=true})
     */
    private $ip;

    /**
     * @var Site
     *
     * @ORM\ManyToOne(targetEntity="Site")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_site", referencedColumnName="id_site")
     * })
     */
    private $idSite;

    /**
     * codePoste Getter.
     *
     * @return string
     */
    public function getCodePoste(): string
    {
        return $this->codePoste;
    }

    /**
     * ip Getter.
     *
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * idSite Getter.
     *
     * @return Site
     */
    public function getIdSite(): Site
    {
        return $this->idSite;
    }
}

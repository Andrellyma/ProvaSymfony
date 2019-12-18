<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProfessorRepository")
 */
class Professor implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $nome;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Projeto", mappedBy="professor")
     */
    private $projetos;

    public function __construct()
    {
        $this->projetos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * @return Collection|Projeto[]
     */
    public function getProjetos(): Collection
    {
        return $this->projetos;
    }

    public function addProjeto(Projeto $projeto): self
    {
        if (!$this->projetos->contains($projeto)) {
            $this->projetos[] = $projeto;
            $projeto->setIdProfessor($this);
        }

        return $this;
    }

    public function removeProjeto(Projeto $projeto): self
    {
        if ($this->projetos->contains($projeto)) {
            $this->projetos->removeElement($projeto);
            // set the owning side to null (unless already changed)
            if ($projeto->getIdProfessor() === $this) {
                $projeto->setIdProfessor(null);
            }
        }

        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return ["id"=>$this->getId()
            ,"nome"=>$this->getNome()
            ,"projetos"=>$this->getProjetos()
        ];
    }
}

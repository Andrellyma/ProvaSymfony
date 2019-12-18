<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjetoRepository")
 */
class Projeto implements \JsonSerializable
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
     * @ORM\Column(type="boolean")
     */
    public $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Aluno", mappedBy="projeto")
     */
    public $aluno;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Professor", inversedBy="projetos")
     */
    public $professor;

    public function __construct()
    {
        $this->aluno = new ArrayCollection();
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

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Aluno[]
     */
    public function getAluno(): Collection
    {
        return $this->aluno;
    }

    public function addIdAluno(Aluno $Aluno): self
    {
        if (!$this->aluno->contains($Aluno)) {
            $this->aluno[] = $Aluno;
            $Aluno->setProjeto($this);
        }

        return $this;
    }

    public function removeIdAluno(Aluno $Aluno): self
    {
        if ($this->aluno->contains($Aluno)) {
            $this->aluno->removeElement($Aluno);
            // set the owning side to null (unless already changed)
            if ($Aluno->getProjeto() === $this) {
                $Aluno->setProjeto(null);
            }
        }

        return $this;
    }

    public function getProfessor(): ?Professor
    {
        return $this->professor;
    }

    public function setProfessor(?Professor $professor): self
    {
        $this->professor = $professor;

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
        return["id"=>$this->getId()
            ,"nome"=>$this->getNome()
            ,"status"=>$this->getStatus()
            ,"alunos"=>$this->getAluno()
            ,"professor"=>$this->getProfessor()
        ];
    }
}

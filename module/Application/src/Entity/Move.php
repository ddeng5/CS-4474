<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="move")
 */
class Move
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer",name="id")
     */
    protected $id;

    /**
     * @ORM\Column(name="url",type="text")
     */
    protected $url;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Entity\Move", inversedBy="move")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     */
    protected $move;

    /**
     * @ORM\Column(type="float")
     */
    protected $whiteOdds;

    /**
     * @ORM\Column(type="float")
     */
    protected $blackOdds;

    /**
     * @ORM\Column(type="float")
     */
    protected $drawOdds;

    /**
     * @ORM\Column(type="float")
     */
    protected $gameCount;
}
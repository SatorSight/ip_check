<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Operator
 *
 * @ORM\Table(name="operator")
 * @ORM\Entity()
 */

class Operator
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $tech;

    /**
     * @ORM\OneToMany(targetEntity="IPMask", mappedBy="operator")
     */
    private $ip_masks;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ip_masks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Operator
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add ipMask
     *
     * @param \AppBundle\Entity\IPMask $ipMask
     *
     * @return Operator
     */
    public function addIpMask(\AppBundle\Entity\IPMask $ipMask)
    {
        $this->ip_masks[] = $ipMask;

        return $this;
    }

    /**
     * Remove ipMask
     *
     * @param \AppBundle\Entity\IPMask $ipMask
     */
    public function removeIpMask(\AppBundle\Entity\IPMask $ipMask)
    {
        $this->ip_masks->removeElement($ipMask);
    }

    /**
     * Get ipMasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIpMasks()
    {
        return $this->ip_masks;
    }

    public function __toString()
    {
        return $this->name;
    }


    /**
     * Set tech
     *
     * @param string $tech
     *
     * @return Operator
     */
    public function setTech($tech)
    {
        $this->tech = $tech;

        return $this;
    }

    /**
     * Get tech
     *
     * @return string
     */
    public function getTech()
    {
        return $this->tech;
    }
}

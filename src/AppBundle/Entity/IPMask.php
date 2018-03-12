<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IPMask
 *
 * @ORM\Table(name="ip_mask")
 * @ORM\Entity()
 */

class IPMask
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
    private $mask;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $macro_region;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $region;

    /**
     * @ORM\ManyToOne(targetEntity="Operator", inversedBy="ip_masks")
     * @ORM\JoinColumn(name="operator_id", referencedColumnName="id")
     */
    private $operator;


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
     * Set mask
     *
     * @param string $mask
     *
     * @return IPMask
     */
    public function setMask($mask)
    {
        $this->mask = $mask;

        return $this;
    }

    /**
     * Get mask
     *
     * @return string
     */
    public function getMask()
    {
        return $this->mask;
    }

    /**
     * Set operator
     *
     * @param \AppBundle\Entity\Operator $operator
     *
     * @return IPMask
     */
    public function setOperator(\AppBundle\Entity\Operator $operator = null)
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * Get operator
     *
     * @return \AppBundle\Entity\Operator
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Set macroRegion
     *
     * @param string $macroRegion
     *
     * @return IPMask
     */
    public function setMacroRegion($macroRegion)
    {
        $this->macro_region = $macroRegion;

        return $this;
    }

    /**
     * Get macroRegion
     *
     * @return string
     */
    public function getMacroRegion()
    {
        return $this->macro_region;
    }

    /**
     * Set region
     *
     * @param string $region
     *
     * @return IPMask
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }
}

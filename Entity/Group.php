<?php 
namespace Vivait\AuthBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="Groups")
 * @ORM\Entity()
 */
class Group implements RoleInterface, \Serializable
{
    /**
		 * @ORM\Column(name="id", type="guid")
		 * @ORM\Id
		 * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=30)
     */
    private $name;

    /**
     * @ORM\Column(name="role", type="string", length=20, unique=true)
     */
    private $role;

    /**
     * @ORM\ManyToMany(targetEntity="Vivait\AuthBundle\Entity\User", mappedBy="groups")
     */
    private $users;

		/**
		 * @ORM\ManyToMany(targetEntity="Vivait\AuthBundle\Entity\Tenant", mappedBy="groups")
		 */
		private $tenants;

		/**
		 * @ORM\Column(name="tenant_group", type="boolean")
		 */
		private $tenant_group;

		public function __construct() {
			$this->users   = new ArrayCollection();
			$this->tenants = new ArrayCollection();
		}

    // ... getters and setters for each property

    /**
     * @see RoleInterface
     */
    public function getRole()
    {
        return $this->role;
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
     * @return Group
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
     * Set role
     *
     * @param string $role
     * @return Group
     */
    public function setRole($role)
    {
        $this->role = $role;
    
        return $this;
    }

    /**
     * Add users
     *
     * @param User $users
     * @return Group
     */
    public function addUser(User $users)
    {
        $this->users[] = $users;
		$users->addGroup($this);														#REQUIRED WITH BY_REFERENCE IN CONTROLLER FORM TO UPDATE ON INVERSE SIDE
        return $this;
    }

    /**
     * Remove users
     *
     * @param User $users
     */
    public function removeUser(User $users)
    {
        $this->users->removeElement($users);
			$users->removeGroup($this);														#REQUIRED WITH BY_REFERENCE IN CONTROLLER FORM TO UPDATE ON INVERSE SIDE
    }

    /**
     * Get users
     *
     * @return ArrayCollection|User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize() {
        return serialize(array(
            $this->id,
            $this->name,
            $this->role
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized) {
        list (
            $this->id,
            $this->name,
            $this->role
            ) = unserialize($serialized);
    }
    
    /**
		 * Set tenant_group
		 * @param boolean $tenantGroup
		 * @return Group
		 */
		public function setTenantGroup($tenantGroup) {
			$this->tenant_group = $tenantGroup;

			return $this;
		}

		/**
		 * Get tenant_group
		 * @return boolean
		 */
		public function getTenantGroup() {
			return $this->tenant_group;
		}

		/**
		 * Add tenants
		 * @param Tenant $tenants
		 * @return Group
		 */
		public function addTenant(Tenant $tenants) {
			$this->tenants[] = $tenants;
            $tenants->addGroup($this);
			return $this;
		}

		/**
		 * Remove tenants
		 * @param Tenant $tenants
		 */
		public function removeTenant(Tenant $tenants) {
			$this->tenants->removeElement($tenants);
			$tenants->removeGroup($this);
		}

		/**
		 * Get tenants
		 * @return Tenant[]|ArrayCollection
		 */
		public function getTenants() {
			return $this->tenants;
		}
}
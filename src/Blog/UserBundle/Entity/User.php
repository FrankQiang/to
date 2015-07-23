<?php

namespace Blog\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;

use Doctrine\ORM\Mapping as ORM;


/**
 * User
 */
class User extends BaseUser
{

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    private $photo;

    /**
     * @var string
     */
    private $weibo_id;


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
     * Set photo
     *
     * @param string $photo
     * @return User
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string 
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set weibo_id
     *
     * @param string $weiboId
     * @return User
     */
    public function setWeiboId($weiboId)
    {
        $this->weibo_id = $weiboId;

        return $this;
    }

    /**
     * Get weibo_id
     *
     * @return string 
     */
    public function getWeiboId()
    {
        return $this->weibo_id;
    }
    /**
     * @var string
     */
    private $token;


    /**
     * Set token
     *
     * @param string $token
     * @return User
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @ORM\PrePersist
     */
    public function setTokenValue()
    {
            if(!$this->getToken()) {
            $this->token = sha1($this->getEmail().rand(11111, 99999));
        }
    }
}

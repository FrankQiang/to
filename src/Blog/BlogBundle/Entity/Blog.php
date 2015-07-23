<?php

namespace Blog\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Blog
 */
class Blog
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $user_id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $blog;

    /**
     * @var string
     */
    private $tag;

    /**
     * @var integer
     */
    private $click;

    /**
     * @var string
     */
    private $token;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \DateTime
     */
    private $updated_at;


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
     * Set user_id
     *
     * @param integer $userId
     * @return Blog
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Blog
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set blog
     *
     * @param string $blog
     * @return Blog
     */
    public function setBlog($blog)
    {
        $this->blog = $blog;

        return $this;
    }

    /**
     * Get blog
     *
     * @return string 
     */
    public function getBlog()
    {
        return $this->blog;
    }

    /**
     * Set tag
     *
     * @param string $tag
     * @return Blog
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string 
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set click
     *
     * @param integer $click
     * @return Blog
     */
    public function setClick($click)
    {
        $this->click = $click;

        return $this;
    }

    /**
     * Get click
     *
     * @return integer 
     */
    public function getClick()
    {
        return $this->click;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return Blog
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Blog
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Blog
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }
    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        if(!$this->getCreatedAt()) {
           $this->created_at = new \DateTime();
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue()
    {
        $this->updated_at = new \DateTime();
    }


    static public function getLuceneIndex()
    {
        if (file_exists($index = self::getLuceneIndexFile())) {
            return \Zend_Search_Lucene::open($index);
        }

        return \Zend_Search_Lucene::create($index);
    }

    static public function getLuceneIndexFile()
    {
        return __DIR__.'/../../../../web/data/blog.index';
    }


    /**
     * @ORM\PostPersist
     */
    public function updateLuceneIndex()
    {
        $index = self::getLuceneIndex();

        // remove existing entries
        foreach ($index->find('pk:'.$this->getId()) as $hit)
        {
          $index->delete($hit->id);
        }


        $doc = new \Zend_Search_Lucene_Document();

        // store activity primary key to identify it in the search results
        $doc->addField(\Zend_Search_Lucene_Field::Keyword('pk', $this->getId()));

        // index activity fields
        $doc->addField(\Zend_Search_Lucene_Field::UnStored('title', $this->getTitle(), 'utf-8'));
        $doc->addField(\Zend_Search_Lucene_Field::UnStored('tag', $this->getTag(), 'utf-8'));

        // add activity to the index
        $index->addDocument($doc);
        $index->commit();
    }

    /**
     * @ORM\PostRemove
     */
    public function deleteLuceneIndex()
    {
        $index = self::getLuceneIndex();

        foreach ($index->find('pk:'.$this->getId()) as $hit) {
            $index->delete($hit->id);
        }
    }
}

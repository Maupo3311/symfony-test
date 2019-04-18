<?php

namespace AppBundle\Entity\Image;

use AppBundle\Enum\ImageType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="image")
 * @ORM\HasLifecycleCallbacks
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="integer")
 * @ORM\DiscriminatorMap({
 *      ImageType::FEEDBACK = "AppBundle\Entity\Image\FeedbackImage",
 * })
 */
abstract class AbstractImage
{
    /**
     * Image id
     *
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var UploadedFile
     */
    protected $imageTemp;

    /**
     * @var UploadedFile
     * @Assert\File(
     *     maxSize = "5M",
     *     maxSizeMessage = "The maxmimum allowed image size is 5MB.",
     *     mimeTypesMessage = "Only the imagetypes image are allowed."
     * )
     */
    protected $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="image_path")
     */
    protected $imagePath;

    /**
     * Date of creation
     *
     * @var \DateTime
     * @Assert\Date
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $createdAt;

    /***********************************************
     *                Virtual fields
     ***********************************************/

    /**
     * AbstractImage constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Group
     *
     * @return null|int
     */
    abstract public function getType();

    /***********************************************/

    /**
     * @return string|null
     */
    public function getImageAbsolutePath()
    {
        return $this->imagePath ? $this->getImageUploadRootDir() . '/' . $this->imagePath : null;
    }

    /**
     * Image Web path
     *
     * @return null|string
     */
    public function getImageWebPath()
    {
        if ($this->imagePath && is_file($this->getImageAbsolutePath())) {
            return $this->getImageUploadDir() . '/' . $this->imagePath;
        }

        return null;
    }

    /**
     * @return string
     */
    public function getImageUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getImageUploadDir();
    }

    /**
     * @return string
     */
    protected function getImageUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/images';
    }

    /**
     * @param UploadedFile|null $image
     * @return $this
     * @throws \Exception
     */
    public function setFile(UploadedFile $image = null)
    {
        $this->image = $image;

        // check if we have an old image path
        if (is_file($this->getImageAbsolutePath())) {
            // store the old name to delete after the update
            $this->imageTemp = $this->getImageAbsolutePath();
        }

        $this->preUploadImage();

        return $this;
    }

    /**
     * Get image.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->image;
    }

    /**
     * @param File $image
     * @return string
     * @throws \Exception
     */
    public static function generateFilename(File $image)
    {
        return sprintf(
            '%s.%s',
            md5($image->getFilename() . microtime() . random_bytes(10)),
            $image->guessExtension()
        );
    }

    /**
     * @throws \Exception
     */
    public function preUploadImage()
    {
        if (null !== $this->getFile()) {
            $this->imagePath = self::generateFilename($this->getFile());
        }
    }

    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     *
     * @return $this|void
     * @throws \Exception
     */
    public function uploadImage()
    {
        if (null === $this->getFile()) {
            return;
        }

        /** check if we have an old image */
        if (isset($this->imageTemp)) {
            /** delete the old image */
            unlink($this->imageTemp);
            /** clear the temp image path */
            $this->imageTemp = null;
        }

        /** you must throw an exception here if the image cannot be moved
           so that the entity is not persisted to the database
           which the UploadedFile move() method does */
        $this->getFile()->move(
            $this->getImageUploadRootDir(),
            $this->imagePath
        );

        $this->setFile(null);

        return $this;
    }

    /**
     * @ORM\PreRemove
     */
    public function storeImageFilenameForRemove()
    {
        $this->imageTemp = $this->getImageAbsolutePath();
    }

    /**
     * @ORM\PostRemove
     */
    public function removeImageUpload()
    {
        if (isset($this->imageTemp) && file_exists($this->imageTemp)) {
            unlink($this->imageTemp);
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return UploadedFile
     */
    public function getFileTemp(): UploadedFile
    {
        return $this->imageTemp;
    }

    /**
     * @return mixed
     */
    public function getFilePath()
    {
        return $this->imagePath;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @param UploadedFile $imageTemp
     */
    public function setFileTemp(UploadedFile $imageTemp)
    {
        $this->imageTemp = $imageTemp;
    }

    /**
     * @param mixed $imagePath
     */
    public function setFilePath($imagePath)
    {
        $this->imagePath = $imagePath;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }
}
<?php

namespace AppBundle\DataFixtures;

use EntityBundle\Entity\Category;
use EntityBundle\Entity\Comment;
use EntityBundle\Entity\Image\FeedbackImage;
use EntityBundle\Entity\Product;
use EntityBundle\Entity\Feedback;
use EntityBundle\Entity\Shop;
use EntityBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use DateTime;

/**
 * Class BaseFixtures
 * @package AppBundle\DataFixtures
 */
class BaseFixtures extends Fixture
{
    const IMAGES_CONSUMABLES_PATH = __DIR__ . '/../../../src/AppBundle/DataFixtures/images';

    const IMAGES_VALID_PATH = __DIR__ . '/../../../web/uploads/images';

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var array
     */
    private $files;

    /**
     * BaseFixtures constructor.
     */
    public function __construct()
    {
        $this->files = $this->getFiles();
    }

    /**
     * @return array
     */
    private function getFiles()
    {
        $files = (file_exists($this::IMAGES_CONSUMABLES_PATH)) ? array_slice(scandir($this::IMAGES_CONSUMABLES_PATH ), 2) : [];

        return $files;
    }

    /**
     * @param array $array
     * @return mixed
     */
    private function getRandom(array $array)
    {
        return $array[rand(0, count($array) - 1)];
    }

    /**
     * @return mixed
     */
    private function getRandomFile()
    {
        return $this->files[rand(0, count($this->files))];
    }

    /**
     * @return array
     */
    private function generateUsers()
    {
        $users = [];

        $user = new User();

        $user->setFirstName('Admin')
            ->setLastName('Admin')
            ->setUsername('Admin')
            ->addRole('ROLE_ADMIN')
            ->setEmail('maupo3311@mail.ru')
            ->setPassword(password_hash("password1234", PASSWORD_DEFAULT))
            ->setEnabled(true);
        $users[] = $user;
        $this->manager->persist($user);

        for ($i = 0; $i < 20; $i++) {
            $user = new User();

            $user->setFirstName('name' . mt_rand(1, 99))
                ->setLastName('surname' . mt_rand(1, 99))
                ->setUsername(rand(1, 100000))
                ->addRole('ROLE_USER')
                ->setEmail(rand(10000, 600000) . '@mail.ru')
                ->setPassword(md5($i))
                ->setEnabled(true);

            $users[] = $user;
            $this->manager->persist($user);
        }

        return $users;
    }

    /**
     * @param array $users
     * @return array
     * @throws \Exception
     */
    private function generateFeedbacks(array $users)
    {
        $feedbacks = [];

        foreach ($users as $user) {
            for ($i = 0; $i < 2; $i++) {
                $feedback = new Feedback();

                $feedback->setMessage(md5($i))
                    ->setUser($user);

                $feedbacks[] = $feedback;
                $this->manager->persist($feedback);
            }
        }

        return $feedbacks;
    }

    /**
     * @return array
     */
    private function generateShops()
    {
        $shops = [];
        for ($i = 0; $i < 5; ++$i) {
            $shop = new Shop();

            $shop->setName('shop ' . rand(100, 100000))
                ->setDescription(md5(rand(0, 100)))
                ->setPhoneNumber('7' . rand(100000000, 999999999))
                ->setLon(rand(30, 55) . '.' . rand(1000000, 9999999))
                ->setLat(rand(30, 55) . '.' . rand(1000000, 9999999));

            $this->manager->persist($shop);
            $shops[] = $shop;
        }

        return $shops;
    }

    /**
     * @param array $categories
     * @return array
     */
    private function generateProducts(array $categories)
    {
        $products = [];

        foreach ($categories as $category) {
            for ($i = 0; $i < 3; $i++) {
                $product = new Product();

                $product->setTitle('product' . mt_rand(1, 3000) . mt_rand(1, 5000))
                    ->setPrice(mt_rand(1, 250))
                    ->setCategory($category)
                    ->setRating(floatval(mt_rand(0, 9) . '.' . mt_rand(1, 99)))
                    ->setDescription(md5($i))
                    ->setNumber(rand(1, 32));

                $this->manager->persist($product);
                $products[] = $product;
            }
        }

        return $products;
    }

    /**
     * @param array $shops
     * @return array
     */
    private function generateCategories(array $shops)
    {
        $categories = [];

        foreach ($shops as $shop) {
            for ($i = 0; $i < 5; $i++) {
                $category = new Category();

                $category->setName('Category -' . mt_rand(1, 30000) . mt_rand(1, 500))
                    ->setShop($shop);

                $this->manager->persist($category);
                $categories[] = $category;
            }
        }

        return $categories;
    }

    /**
     * @param array $users
     * @param array $products
     * @return array
     * @throws \Exception
     */
    private function generateComments(array $users, array $products)
    {
        $comments = [];

        for($i = 0; $i < 1; ++$i) {
            $comment = new Comment();

            $comment->setUser($this->getRandom($users))
                ->setMessage('message' . md5(rand(0, 55555)))
                ->setProduct($this->getRandom($products));

            $this->manager->persist($comment);
            $comments[] = $comment;
        }

//        foreach ($products as $product) {
//            for($i = 0; $i < 2; ++$i) {
//                $comment = new Comment();
//
//                $comment->setUser($this->getRandom($users))
//                    ->setMessage('message' . $product->getTitle() . md5(rand(0, 55555)))
//                    ->setProduct($product);
//
//                $this->manager->persist($comment);
//                $comments[] = $comment;
//            }
//        }

        return $comments;
    }

    /**
     * @param array $feedbacks
     * @return array
     * @throws \Exception
     */
    private function generateFeedbackImages(array $feedbacks)
    {
        $feedbackImages = [];

        foreach ($feedbacks as $feedback) {
            $file = $this->getRandomFile();
            $type = array_pop(explode(".", $file));
            $copyPath = $this::IMAGES_CONSUMABLES_PATH . '/processedImage.' . $type;
            copy($this::IMAGES_CONSUMABLES_PATH . '/' . $file, $copyPath);

            $uploadImage = new UploadedFile($copyPath, md5(rand(0, 99999999)) . '.' . $type);

            $feedbackImage = new FeedbackImage();

            $feedbackImage->setFeedback($feedback)
                ->setFile($uploadImage);

            $feedbackImages[] = $feedbackImage;

            $this->manager->persist($feedbackImage);
        }

        return $feedbackImages;
    }

    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $users      = $this->generateUsers();
        $feedbacks  = $this->generateFeedbacks($users);
        $shops      = $this->generateShops();
        $categories = $this->generateCategories($shops);
        $products   = $this->generateProducts($categories);
        $comments   = $this->generateComments($users, $products);
//
//        $feedbackImages = [];
//
//        for ($i = 0; $i < count($files); $i++) {
//            $file          = $files[$i];
//            $object        = new UploadedFile($path . '/' . $file, rand(0, 50000) . '.jpeg', 'image/jpeg', null, null, true);
//            $feedbackImage = new FeedbackImage();
//            $feedbackImage
//                ->setFile($object)
//                ->setFilePath($path . '/' . $file)
//                ->setCreatedAt(new DateTime())
//                ->setFeedback($feedbacks[0]);
//            $manager->persist($feedbackImage);
//            $feedbackImages[] = $feedbackImage;
//        }

        $this->manager->flush();
    }
}
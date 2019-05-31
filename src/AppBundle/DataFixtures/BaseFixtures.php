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
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $users = [];

        $user = new User();
        $user->setFirstName('Admin');
        $user->setLastName('Admin');
        $user->setUsername('Admin');
        $user->addRole('ROLE_ADMIN');
        $user->setEmail('maupo3311@mail.ru');
        $user->setPassword(password_hash("password1234", PASSWORD_DEFAULT));
        $user->setEnabled(true);
        $users[] = $user;
        $manager->persist($user);

        for ($i = 0; $i < 20; $i++) {
            $username = rand(1, 100000);
            $user     = new User();
            $user->setFirstName('name' . mt_rand(1, 99));
            $user->setLastName('surname' . mt_rand(1, 99));
            $user->setUsername($username);
            $user->addRole('ROLE_USER');
            $user->setEmail(rand(10000, 600000) . '@mail.ru');
            $user->setPassword(md5($i));
            $user->setEnabled(true);
            $users[] = $user;
            $manager->persist($user);
        }

        $feedbacks = [];
        foreach ($users as $user) {
            for ($i = 0; $i < 2; $i++) {
                $feedback = new Feedback();
                $feedback->setName('name' . mt_rand(1, 50000))
                    ->setEmail('email' . mt_rand(1, 50000) . '@mail.ru')
                    ->setMessage(md5($i))
                    ->setUser($user);
                $feedbacks[] = $feedback;
                $manager->persist($feedback);
            }
        }

        $path           = __DIR__ . '/../../../web/test';
        $files          = (file_exists($path)) ? array_slice(scandir($path), 2) : [];
        $feedbackImages = [];

        for ($i = 0; $i < count($files); $i++) {
            $file          = $files[$i];
            $object        = new UploadedFile($path . '/' . $file, rand(0, 50000) . '.jpeg', 'image/jpeg', null, null, true);
            $feedbackImage = new FeedbackImage();
            $feedbackImage
                ->setFile($object)
                ->setFilePath($path . '/' . $file)
                ->setCreatedAt(new DateTime())
                ->setFeedback($feedbacks[0]);
            $manager->persist($feedbackImage);
            $feedbackImages[] = $feedbackImage;
        }

        $shops = [];
        for ($i = 0; $i < 5; ++$i) {
            $shop = new Shop();
            $shop->setName('shop ' . rand(100, 100000))
                ->setDescription(md5(rand(0, 100)))
                ->setPhoneNumber('7' . rand(100000000, 999999999))
                ->setLon(rand(30, 55).'.'.rand(1000000, 9999999))
                ->setLat(rand(30, 55).'.'.rand(1000000, 9999999));

            $manager->persist($shop);
            $shops[] = $shop;
        }

        $categories = [];

        foreach ($shops as $shop) {
            for ($i = 0; $i < 5; $i++) {
                $category = new Category();
                $category
                    ->setName('Category -' . mt_rand(1, 30000) . mt_rand(1, 500))
                    ->setShop($shop);

                $categories[] = $category;
                $manager->persist($category);
            }
        }

        $products = [];
        foreach ($categories as $category) {
            for ($i = 0; $i < 3; $i++) {
                $product = new Product();
                $product->setTitle('product' . mt_rand(1, 3000) . mt_rand(1, 5000));
                $product->setPrice(mt_rand(1, 250));
                $product->setCategory($category);
                $product->setRating(floatval(mt_rand(0, 9) . '.' . mt_rand(1, 99)));
                $product->setDescription(md5($i));
                $product->setNumber(rand(1, 32));
                $manager->persist($product);
                $products[] = $product;
            }
        }

//        foreach ($products as $product){
//            $user = $users[rand(0, count($users - 1))];
//            $comment = new Comment();
//            $comment
//                ->setProduct($product)
//                ->setMessage(md5(rand(0, 1000)))
//                ->setUser($user);
//
//            $manager->persist($comment);
//        }

        $manager->flush();
    }
}
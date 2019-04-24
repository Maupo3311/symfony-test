<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Basket;
use AppBundle\Entity\Category;
use AppBundle\Entity\Image\FeedbackImage;
use AppBundle\Entity\Product;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\User;
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
            $user = new User();
            $user->setFirstName('name' . mt_rand(1, 99));
            $user->setLastName('surname' . mt_rand(1, 99));
            $user->setUsername(rand(1, 100000));
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

        $categories = [];

        for ($i = 0; $i < 20; $i++) {
            $category = new Category();
            $category->setName('Category -' . mt_rand(1, 30000) . mt_rand(1, 500));

            $categories[] = $category;
            $manager->persist($category);
        }

        $products = [];
        foreach ($categories as $category) {
            for ($i = 0; $i < 20; $i++) {
                $product = new Product();
                $product->setTitle('product' . mt_rand(1, 3000) . mt_rand(1, 5000));
                $product->setPrice(mt_rand(10, 10000) . '.' . mt_rand(1, 99));
                $product->setCategory($category);
                $product->setRating(floatval(mt_rand(0, 10) . '.' . mt_rand(1, 99)));
                $product->setDescription(md5($i));
                $manager->persist($product);
                $products[] = $product;
            }
        }

        foreach ($products as $product) {
            $basket = new Basket();
            $basket->setUser($users[rand(0, count($users) - 1)]);
            $basket->setBasketProduct($product);
            $manager->persist($basket);
        }

        $manager->flush();
    }
}
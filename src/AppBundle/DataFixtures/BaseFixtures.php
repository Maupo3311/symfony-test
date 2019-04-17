<?php


namespace AppBundle\DataFixtures;


use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class BaseFixtures extends Fixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setFirstName('Admin');
        $user->setLastName('Admin');
        $user->setUsername('Admin');
        $user->addRole('ROLE_ADMIN');
        $user->setEmail('maupo3311@mail.ru');
        $user->setPassword(password_hash("password1234", PASSWORD_DEFAULT));
        $user->setEnabled(true);
        $manager->persist($user);

        $users = [];
        for($i = 0; $i < 20; $i++){
            $user = new User();
            $user->setFirstName('name' . mt_rand(1, 99));
            $user->setLastName('surname' . mt_rand(1, 99));
            $user->setUsername(rand(1, 1000));
            $user->addRole('ROLE_USER');
            $user->setEmail(rand(10000, 600000) . '@mail.ru');
            $user->setPassword(md5($i));
            $user->setEnabled(true);
            $users[] = $user;
            $manager->persist($user);
        }

//        foreach($users as $user){
//            for($i = 0; $i < 2; $i++){
//                $feedback = new Feedback();
//                $feedback->setName('name' . mt_rand(1, 500));
//                $feedback->setEmail('email' . mt_rand(1, 500) . '@mail.ru');
//                $feedback->setMessage(md5($i));
//                $feedback->setUser($user);
//                $feedback->setBrochure('');
//                $manager->persist($feedback);
//            }
//        }

        $categories = [];

        for ($i = 0; $i < 20; $i++) {
            $category = new Category();
            $category->setName('Category -' . mt_rand(1, 300) . mt_rand(1, 500));

            $categories[] = $category;
            $manager->persist($category);
        }

        foreach ($categories as $category) {
            for ($i = 0; $i < 20; $i++) {
                $product = new Product();
                $product->setTitle('product' . mt_rand(1, 300) . mt_rand(1, 500));
                $product->setPrice(mt_rand(10, 1000) . '.' . mt_rand(1, 99));
                $product->setCategory($category);
                $product->setRating(floatval(mt_rand(0, 10) . '.' . mt_rand(1, 99)));
                $product->setDescription(md5($i));
                $manager->persist($product);
            }
        }

        $manager->flush();
    }
}
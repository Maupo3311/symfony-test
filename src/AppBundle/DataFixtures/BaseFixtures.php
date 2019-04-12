<?php


namespace AppBundle\DataFixtures;


use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\Feedback;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class BaseFixtures extends Fixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        for($i = 0; $i < 38; $i++){
            $feedback = new Feedback();
            $feedback->setName('name' . mt_rand(1, 500));
            $feedback->setEmail('email' . mt_rand(1, 500) . '@mail.ru');
            $feedback->setMessage(md5($i));
            $manager->persist($feedback);
        }

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
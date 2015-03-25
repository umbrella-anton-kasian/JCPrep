<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Post;

class LoadPostData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $userAdmin = new Post();
        $userAdmin->setTitle('Fixture post');
        $userAdmin->setContent('Fixture post content');

        $manager->persist($userAdmin);
        $manager->flush();
    }
}
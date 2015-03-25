<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Category;
use AppBundle\Entity\CategoryMP;
use AppBundle\Entity\CategoryC;
use AppBundle\Entity\Post;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{
    /**
     * @Route("/app/example", name="homepage")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        /* NESTED TREE */

//        $food = new Category();
//        $food->setTitle('Food');
//
//        $fruits = new Category();
//        $fruits->setTitle('Fruits');
//        $fruits->setParent($food);
//
//        $vegetables = new Category();
//        $vegetables->setTitle('Vegetables');
//        $vegetables->setParent($food);
//
//        $carrots = new Category();
//        $carrots->setTitle('Carrots');
//        $carrots->setParent($vegetables);
//
//        $berry = new Category();
//        $berry->setTitle('Berry');
//        $berry->setParent($vegetables);
//
//        $smtg = new Category();
//        $smtg->setTitle('Something');
//        $smtg->setParent($vegetables);
//

//        $em->persist($food);
//        $em->persist($fruits);
//        $em->persist($vegetables);
//        $em->persist($carrots);
//        $em->persist($berry);
//        $em->persist($smtg);
//        $em->flush();

//        $repo = $em->getRepository('AppBundle\Entity\Category');

//        $food = $repo->findOneByTitle('Food');
//        $echo = $repo->childCount($food); //5
//
//        $echo = $repo->childCount($food, true /*direct*/ ); //2

//        $arrayTree = $repo->childrenHierarchy();

//        $htmlTree = $repo->childrenHierarchy(
//            null, /* starting from root nodes */
//            false, /* true: load all children, false: only direct */
//            array(
//                'decorate' => true,
//                'representationField' => 'slug',
//                'html' => true
//            )
//        );

        /* MATERIALIZED PATH */
//        $food = new CategoryMP();
//        $food->setTitle('Food');
//
//        $fruits = new CategoryMP();
//        $fruits->setTitle('Fruits');
//        $fruits->setParent($food);
//
//        $em->persist($food);
//        $em->persist($fruits);
//        $em->flush();

        /* CLOSURE */
//        $food = new CategoryC();
//        $food->setTitle('Food');
//
//        $fruits = new CategoryC();
//        $fruits->setTitle('Fruits');
//        $fruits->setParent($food);
//
//        $em->persist($food);
//        $em->persist($fruits);
//        $em->flush();

        return new Response('ok');
    }

    /**
     * @Route("/app/example2", name="example_page_2")
     */
    public function exampleAction()
    {
        $em = $this->getDoctrine()->getManager();

        $post = new Post();
        $post->setTitle('Просто какой-то пост');
        $post->setContent('Some post body');

        $em->persist($post);
        $em->flush();

        return new Response($post->getCreatedAt()->format('H:i:s') . '<br>' . $post->getSlug() );
    }
}

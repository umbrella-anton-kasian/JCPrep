<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Comment;
use AppBundle\Entity\CommentMP;

class TreeTestController extends Controller
{
    //Populate nested tree
    // /tree/nested/populate
    public function nestedTreePopulateAction()
    {
        $result = null;

        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('AppBundle\Entity\Post')->findOneById(25);

        $firstComment = new Comment();
        $firstComment->setContent('First Comment');
        $firstComment->setPostId($post);

        $secondComment = new Comment();
        $secondComment->setContent('Second Comment');
        $secondComment->setParent($firstComment);
        $secondComment->setPostId($post);

        $em->persist($firstComment);
        $em->persist($secondComment);
        $em->flush();

        $result = 'flushed!';

        return $this->render('AppBundle:TreeTest:results.html.twig', array('result' => $result));
    }

    // get all children
    // /tree/nested/children
    public function nestedTreeChildrenAction()
    {
        $result = null;

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle\Entity\Comment');

        $firstComment = $repo->findOneById(31);

        $children = $repo->children($firstComment);

        $result = 'flushed!';

        return $this->render('AppBundle:TreeTest:results.html.twig', array('result' => $result));
    }

    // get all children
    // /tree/nested/count
    public function nestedTreeChildCountAction()
    {
        $result = null;

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle\Entity\Comment');

        $firstComment = $repo->findOneById(31);

        $repo->childCount($firstComment, true);

        $result = 'flushed!';

        return $this->render('AppBundle:TreeTest:results.html.twig', array('result' => $result));
    }

    //Populate materialized path tree
    // /tree/materialized-path/populate
    public function materializedPathTreePopulateAction()
    {
        $result = null;

        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('AppBundle\Entity\Post')->findOneById(25);

        $firstComment = new CommentMP();
        $firstComment->setContent('First Comment');
        $firstComment->setPostId($post);

        $secondComment = new CommentMP();
        $secondComment->setContent('Second Comment');
        $secondComment->setParent($firstComment);
        $secondComment->setPostId($post);

        $em->persist($firstComment);
        $em->persist($secondComment);
        $em->flush();

        $result = 'flushed!';

        return $this->render('AppBundle:TreeTest:results.html.twig', array('result' => $result));
    }

    // get all children
    // /tree/materialized-path/children
    public function matpathTreeChildrenAction()
    {
        $result = null;

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle\Entity\Comment');

        $firstComment = $repo->findOneById(3);

        $children = $repo->children($firstComment);

        $result = 'flushed!';

        return $this->render('AppBundle:TreeTest:results.html.twig', array('result' => $result));
    }

    // get all children
    // /tree/matherialized/count
    public function materializedTreeChildCountAction()
    {
        $result = null;

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle\Entity\CommentMP');

        $firstComment = $repo->findOneById(3);

        $repo->childCount($firstComment, true);

        $result = 'flushed!';

        return $this->render('AppBundle:TreeTest:results.html.twig', array('result' => $result));
    }
}
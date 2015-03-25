<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Entity\Post;

class PostRestController extends FOSRestController
{
    /**
     * @ApiDoc(
     *     section="post manage api",
     *     resource=true,
     *     description="Returns a collection of posts",
     *     statusCodes={
     *         200="Returned when successful"
     *     }
     * )
     */
    public function getListAction(){
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('AppBundle\Entity\Post')->findAll();

        $statusCode = 200;

        $view = $this->view($posts, $statusCode);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *     section="post manage api",
     *     resource=true,
     *     description="Returns a collection of posts",
     *     statusCodes={
     *         200="Returned when successful",
     *         404="Returned when post not found"
     *     }
     * )
     */
    public function getAction($id){
        $em = $this->getDoctrine()->getManager();

        if((int)$id){
            $post = $em->getRepository('AppBundle\Entity\Post')->findOneById((int)$id);
        }

        if(isset($post)){
            $statusCode = 200;
            $result = array(
                'meta' => $statusCode,
                'message' => array(
                    'id' => $post->getId(),
                    'title' => $post->getTitle(),
                    'content' => $post->getContent(),
                    'createdAt' => $post->getCreatedAt()
                )
            );
        } else {
            $statusCode = Response::HTTP_NOT_FOUND;
            $result = array('meta' => $statusCode, 'error' => 'Post not found');
        }


        $view = $this->view($result, $statusCode);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *     section="post manage api",
     *     resource=true,
     *     description="Create a post",
     *     statusCodes={
     *         201="Returned when successful",
     *         400="Returned on validation error"
     *     }
     * )
     */
    public function createAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $post = new Post();
        $post->setTitle($request->request->get('title'));
        $post->setContent($request->request->get('content'));

        $validator = $this->get('validator');
        $errors = $validator->validate($post);

        if (count($errors) > 0) {
            $statusCode = Response::HTTP_BAD_REQUEST;
            $result = array('meta' => $statusCode, 'error' => $errors[0]->getMessage());
        } else {
            $em->persist($post);
            $em->flush();

            $statusCode = Response::HTTP_CREATED;
            $result = array(
                'meta' => $statusCode,
                'message' => array(
                    'id' => $post->getId(),
                    'title' => $post->getTitle(),
                    'content' => $post->getContent(),
                    'createdAt' => $post->getCreatedAt()
                )
            );
        }

        $view = $this->view($result, $statusCode);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *     section="post manage api",
     *     resource=true,
     *     description="Update an existing post",
     *     statusCodes={
     *         200="Returned when successful",
     *         400="Returned on validation error",
     *         404="Returned when post is not found"
     *     }
     * )
     */
    public function updateAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();

        if((int)$id){
            $post = $em->getRepository('AppBundle\Entity\Post')->findOneById((int)$id);
        }

        if(isset($post)) {

            $post->setTitle($request->request->get('title'));
            $post->setContent($request->get('content'));

            $validator = $this->get('validator');
            $errors = $validator->validate($post);

            if (count($errors) > 0) {
                $statusCode = Response::HTTP_BAD_REQUEST;
                $result = array('meta' => $statusCode, 'error' => $errors[0]->getMessage());
            } else {
                $em->persist($post);
                $em->flush();

                $statusCode = Response::HTTP_OK;
                $result = array(
                    'meta' => $statusCode,
                    'message' => array(
                        'id' => $post->getId(),
                        'title' => $post->getTitle(),
                        'content' => $post->getContent(),
                        'createdAt' => $post->getCreatedAt()
                    )
                );
            }
        } else {
            $statusCode = Response::HTTP_NOT_FOUND;
            $result = array('meta' => $statusCode, 'error' => 'Post not found');
        }

        $view = $this->view($result, $statusCode);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *     section="post manage api",
     *     description="Delete an existing post",
     *     statusCodes={
     *         204="Returned when successful",
     *         404="Returned when post is not found"
     *     }
     * )
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();

        $post = $em->getRepository('AppBundle\Entity\Post')->findOneById($id);

        if(isset($post)) {

            $em->remove($post);
            $em->flush();

            $statusCode = Response::HTTP_NO_CONTENT;
            $result = array('meta' => $statusCode, 'message' => 'Post was deleted');

        } else {
            $statusCode = Response::HTTP_NOT_FOUND;
            $result = array('meta' => $statusCode, 'error' => 'Post not found');
        }

        $view = $this->view($result, $statusCode);
        return $this->handleView($view);
    }
}
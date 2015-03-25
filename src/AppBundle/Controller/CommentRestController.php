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
use AppBundle\Entity\Comment;

class CommentRestController extends FOSRestController
{
    /**
     * @ApiDoc(
     *     section="comment manage api",
     *     resource=true,
     *     description="Returns a collection of comments",
     *     statusCodes={
     *         200="Returned when successful"
     *     }
     * )
     */
    public function getListAction(){
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('AppBundle\Entity\Comment')->findAll();

        $statusCode = 200;

        $view = $this->view($result, $statusCode);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *     section="comment manage api",
     *     resource=true,
     *     description="Returns a  comment",
     *     statusCodes={
     *         200="Returned when successful",
     *         404="Returned when post not found"
     *     }
     * )
     */
    public function getAction($id){
        $em = $this->getDoctrine()->getManager();

        if((int)$id){
            $comment = $em->getRepository('AppBundle\Entity\Comment')->findOneById((int)$id);
        }

        if(isset($comment)){
            $statusCode = 200;
            $result = array(
                'meta' => $statusCode,
                'message' => array(
                    'id' => $comment->getId(),
                    'post_id' => $comment->getPostId()->getId(),
                    'email' => $comment->getEmail(),
                    'content' => $comment->getContent(),
                    'createdAt' => $comment->getCreatedAt()
                )
            );
        } else {
            $statusCode = Response::HTTP_NOT_FOUND;
            $result = array('meta' => $statusCode, 'error' => 'Comment not found');
        }


        $view = $this->view($result, $statusCode);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *     section="comment manage api",
     *     resource=true,
     *     description="Create a comment",
     *     statusCodes={
     *         201="Returned when successful",
     *         400="Returned on validation error"
     *     },
     *     parameters={
     *         {"name"="postId", "dataType"="integer", "required"=true, "description"="post id"},
     *         {"name"="content", "dataType"="string", "required"=true, "description"="body of the comment"},
     *         {"name"="commentId", "dataType"="integer", "required"=false, "description"="parent of this comment"}
     *     }
     * )
     *
     * @todo( make a query where comment belongs to current post )
     */
    public function createAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $postId = $request->request->get('postId');

        if((int)$postId){
            $post = $em->getRepository('AppBundle\Entity\Post')->findOneById((int)$postId);
        }

        if(isset($post)){
            $comment = new Comment();
            $comment->setContent($request->request->get('content'));
            $comment->setEmail('dummy@email.com');
            $comment->setPostId($post);
            if($request->request->get('commentId')){
                //THIS TO DO PLACE
                $parentComment = $em->getRepository('AppBundle\Entity\Comment')->findOneById((int)$request->request->get('commentId'));
                $comment->setParent($parentComment);
            }

            $validator = $this->get('validator');
            $errors = $validator->validate($comment);
            if (count($errors) > 0) {
                $statusCode = Response::HTTP_BAD_REQUEST;
                $result = array('meta' => $statusCode, 'error' => $errors[0]->getMessage());
            } else {
                $em->persist($comment);
                $em->flush();

                $statusCode = Response::HTTP_CREATED;
                $result = array(
                    'meta' => $statusCode,
                    'message' => array(
                        'id' => $comment->getId(),
                        'post_id' => $comment->getPostId()->getId(),
                        'email' => $comment->getEmail(),
                        'content' => $comment->getContent(),
                        'createdAt' => $comment->getCreatedAt()
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
     *     section="comment manage api",
     *     resource=true,
     *     description="Update an existing comment",
     *     statusCodes={
     *         200="Returned when successful",
     *         400="Returned on validation error",
     *         404="Returned when post is not found"
     *     },
     *     parameters={
     *         {"name"="content", "dataType"="string", "required"=true, "description"="body of the comment"}
     *     }
     * )
     */
    public function updateAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();

        if((int)$id){
            $comment = $em->getRepository('AppBundle\Entity\Comment')->findOneById((int)$id);
        }

        if(isset($comment)){

            $comment->setContent($request->request->get('content'));

            $validator = $this->get('validator');
            $errors = $validator->validate($comment);
            if (count($errors) > 0) {
                $statusCode = Response::HTTP_BAD_REQUEST;
                $result = array('meta' => $statusCode, 'error' => $errors[0]->getMessage());
            } else {
                $em->persist($comment);
                $em->flush();

                $statusCode = Response::HTTP_OK;
                $result = array(
                    'meta' => $statusCode,
                    'message' => array(
                        'id' => $comment->getId(),
                        'post_id' => $comment->getPostId()->getId(),
                        'email' => $comment->getEmail(),
                        'content' => $comment->getContent(),
                        'createdAt' => $comment->getCreatedAt()
                    )
                );
            }

        } else {
            $statusCode = Response::HTTP_NOT_FOUND;
            $result = array('meta' => $statusCode, 'error' => 'Comment not found');
        }

        $view = $this->view($result, $statusCode);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *     section="comment manage api",
     *     description="Delete an existing comment",
     *     statusCodes={
     *         204="Returned when successful",
     *         404="Returned when post is not found"
     *     }
     * )
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();

        $comment = $em->getRepository('AppBundle\Entity\Comment')->findOneById($id);

        if(isset($comment)) {

            $em->remove($comment);
            $em->flush();

            $statusCode = Response::HTTP_NO_CONTENT;
            $result = array('meta' => $statusCode, 'message' => 'Comment was deleted');

        } else {
            $statusCode = Response::HTTP_NOT_FOUND;
            $result = array('meta' => $statusCode, 'error' => 'Comment not found');
        }

        $view = $this->view($result, $statusCode);
        return $this->handleView($view);
    }
}
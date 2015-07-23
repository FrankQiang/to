<?php

namespace Blog\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Blogger\BlogBundle\Entity\BlogImage;
use Blogger\BlogBundle\Form\BlogImageType;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * BlogImage controller.
 *
 */
class BlogImageController extends Controller
{


    public function uploadAction()
    {
        $user = $this->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        $user_id=$user->getId();

       $year = date('Y',time());
       $month = date('m',time());
       $day = date('d',time());
       $mode = 0777;
       $headdir =  __DIR__.'/../../../../web/uploads/users/blog/';

       //创建每天上传的图片所在文件夹
       if(!is_dir($headdir.$year.'/'.$month.'/'.$day))
       {
                mkdir($headdir.$year.'/'.$month.'/'.$day,$mode,true);
       }
       
       //上传路径
       $relativedir = $headdir.$year.'/'.$month.'/'.$day.'/';
       //md5后的文件名  带用户标签
       $name = md5($_FILES['file']['name'].time()).'_'.$user_id;
       //确保上传为图片
       $type = exif_imagetype($_FILES['file']["tmp_name"]);
       switch ($type) {
        case IMAGETYPE_JPEG :
            $extend = ".jpg";
            break;
        case IMAGETYPE_PNG :
            $extend = ".png";
            break;
       }
       
       //如果上传成功同时符合图片要求 返回图片地址并返回成功码 否则返回失败
       if ((!empty($extend))&&move_uploaded_file ( $_FILES['file']["tmp_name"], $relativedir. $name.$extend )) {
         
            $response = new Response('http://www.wocycle.com/uploads/users/blog/'.$year.'/'.$month.'/'.$day.'/'.$name.$extend,Response::HTTP_OK);

            return $response;
       }else{
            $response = new Response(400);

            return $response;
       }
         

    }


}

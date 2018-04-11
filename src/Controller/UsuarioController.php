<?php

namespace App\Controller;

use App\Entity\Usuario;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use json;
class UsuarioController extends Controller
{
    /**
     * @Route("/usuario", name="usuario")
     */

    /*
     * Crea el fomulario y recoje los datos
     * llama a la funcion login para realizar el chequeo
     * */
    public function index(Request $request)
    {
        $defaultData = array('message' => 'Type your message here');

        $formFactory = Forms::createFormFactory();
        $form = $this->createFormBuilder($defaultData)
            ->add('usuario', TextType::class)
            ->add('password', PasswordType::class)
            ->add('confirmar', SubmitType::class, array('label' => 'Login '))
            ->getForm();


        $form->handleRequest($request);



        if($form->isSubmitted())
        {

                $mensaje=$this->login($form->getData());
                return $this->render('usuario/index.html.twig', [
                    'controller_name' => 'UsuarioController','type'=>$mensaje['flash'],'mensaje'=>$mensaje['mensaje'],
                    'form' => $form->createView()
            ]);
        }else {
                 return $this->render('usuario/index.html.twig', [
                'controller_name' => 'UsuarioController',
                'form' => $form->createView()
            ]);
        }
    }




    /*
     * Realiza el chequeo y devuelve el mensaje y el tipo de mensaje
     * para cada caso
     * 
     * */
    public function login($usuario){

        $user =$this->getDoctrine()->getRepository(Usuario::class)->findOneBy(array('user'=>$usuario['usuario']));
        if(count($user) >0){
            if($user->getPassword()  === $usuario['password']){
                $mensaje=array("flash"=>"warning","mensaje"=>"El usuario ya existe");
            }

            else{
                $mensaje=array("flash"=>"danger ","mensaje"=>"Contraseña Incorrecta");
                 }
        }
        else{
            $user = new Usuario();
            $user->setUser($usuario['usuario']);
            $user->setPassword($usuario['password']);
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
            $mensaje=array("flash"=>"success","mensaje"=>"Usuario creado con éxito");
        }
        return $mensaje ;
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Swagger\Annotations as SWG;

class UserController extends AbstractController
{

    /**
     * @param EntityManagerInterface $em
     * @param ReunionManager $rm
     * @param JWTTokenManagerInterface $jwtM
     * @param LoggerInterface $logger
     */
    public function __construct(JWTTokenManagerInterface $jwtM){
        $this->jwtM=$jwtM;
    }

    /**
     * @Route("/api/login_check", name="login")
     *  @SWG\Post(
        *path="/api/login_check",
        *consumes={"multipart/form-data"},
        *parameters={
            *@SWG\Parameter(name="username", in="formData", description="username", type="string"),
            *@SWG\Parameter(name="password", in="formData", description="paswword", type="string"),
        *},
        *@SWG\Response(
        *   response="200",
        *   description="Authentification",
        *   schema=@SWG\Schema(type="object",ref="#/definitions/default")
        *))
     * @param JWTEncoderInterface $JWTEncoder
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException
     */
    public function login(Request $request, JWTEncoderInterface  $JWTEncoder,UserPasswordEncoderInterface $passwordEncoder)
    {
       $values = $request->request->all();
        $username   = $values["username"]; 
        $password   = $values["password"]; 
            $repo = $this->getDoctrine()->getRepository(User::class);
            $user = $repo-> findOneBy(['username' => $username]);
            if(!$user){
                $data = [
                    'code' => 501,
                    'status'=>false,
                    'message' => 'Username incorrect'
                ];
                return new JsonResponse($data);
            }
            $isValid = $passwordEncoder->isPasswordValid($user, $password);
            if(!$isValid){
                $data = [
                    'code' => 502,
                    'status'=>false,
                    'message' => 'Mot de passe incorect',
                ];
                return new JsonResponse($data);
            }
            $token = $JWTEncoder->encode([
                'username' => $user->getUsername(),
                'roles'=>$user->getRoles(),
                'exp' => time() + 86400 // 1 day expiration
            ]);
            return $this->json([
                'token' => $token
            ]);
    }


    
    /**
     * @Route("/api/register", name="register")
      * @SWG\Post(
        *path="/api/register",
        *security={{"Bearer":{}}},
        *consumes={"multipart/form-data"},
        *parameters={
            *@SWG\Parameter(name="username", in="formData", description="Email", type="string"),
        *},
        *@SWG\Response(
        *   response="200",
        *   description="Ajout utilisateur",
        *   schema=@SWG\Schema(type="object",ref="#/definitions/default")
        *))
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager, 
                             SerializerInterface $serializer, ValidatorInterface $validator) {
        $user=new User();
        $values=$request->request->all();
        if($values["username"]==null){
            $data = [
                'status' => 500,
                'message' => 'Vous devez renseigner le username et le password'
            ];
            return new JsonResponse($data, 500);
           }
           $form = $this->createForm(UserType::class, $user);
           $form->handleRequest($request);
           $form->submit($values);
        $errorsAssert = $validator->validate($user);
       if(count($errorsAssert)>0) {
          $err = $serializer->serialize($errorsAssert, 'json');
           return new JsonResponse($err, 500);
       }
       if($form->isSubmitted()) {
	    $password="password";
            $user->setPassword($passwordEncoder->encodePassword($user, $password));
            $profile=strtolower($values["profile"]);
            if($profile=="user"){
                $user->setRoles(["ROLE_USER"]);
            }  
            elseif($profile=="admin"){
            $user->setRoles(["ROLE_ADMIN"]);
            } 
            elseif($profile=="client"){
                $user->setRoles(["ROLE_CLIENT"]);
            }
            $entityManager->persist($user);
            $entityManager->flush();
            $data = [
                'status' => true,
                'code' => 201,
                'message' => 'L\'utilisateur a été crée'
            ];
         return new JsonResponse($data, 201);
        }
    }
}

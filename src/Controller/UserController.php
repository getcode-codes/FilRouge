<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api")
 */
class UserController extends AbstractController
{
    


    private $encoder;
 
    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }
    /**
     * @Route("/users", name="add_user", methods={"POST"})
     */

    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {

        
    
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $this->denyAccessUnlessGranted('POST', $user);
        $errors = $validator->validate($user);
        if(count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        
        if ($user->getPassword() && $user->getRoles([])) {
            $user->setPassword(
                $this->encoder->encodePassword($user, $user->getPassword())
            );
            $user->setRoles(["ROLE_".strtoupper($user->getProfil()->getLibelle())]);
            $user->eraseCredentials();
        }
        $entityManager->persist($user);
        $entityManager->flush();
        $data = [
            'status' => 201,
            'message' => 'Utilisateur Insere avec succes'
        ];
        return new JsonResponse($data, 201);
    }

   

}

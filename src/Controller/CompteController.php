<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Depot;
use App\Entity\Compte;
use App\Entity\Partenaire;
use App\Generateur\GenererNumCompte;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api")
 */
class CompteController extends AbstractController
{
    private $encoder;
    private $num;
    
   
 
    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }
    

    /**
     * @Route("/compte", name="add_compte", methods={"POST"})
     */

    public function new(Request $request,GenererNumCompte $generer, SerializerInterface $serializer,CompteRepository $CompteRepository, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        
        $users = $this->getUser();
        
        // $values=json_decode($request->getContent());
        $date=new \DateTime('now');
        // $partenaire=new Partenaire;
        // $compte=new Compte;
        // $depot=new Depot;
        // $user=new User;


         $compte = $serializer->deserialize($request->getContent(), Compte::class, 'json');
         $user = $serializer->deserialize($request->getContent(), User::class, 'json');

         $depot = $serializer->deserialize($request->getContent(), Depot::class, 'json');
         $partenaire = $serializer->deserialize($request->getContent(), Partenaire::class, 'json');

        $errors = $validator->validate($compte);
        if(count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        // if (isset($values->ninea)) {

        //Insertion Partenaire 
        
        $entityManager->persist($partenaire);
        $entityManager->flush();
       
        //Insertion Partenaire Utilisateur

        $user->setPassword($this->encoder->encodePassword($user,$user->getPassword()));
        $user->setRoles(["ROLE_".$user->getProfil()->getLibelle()]);
        $user->setParteners($partenaire);
        $entityManager->persist($user);
        $entityManager->flush();

            

            //Generer compte
           
            
        //Cree Compte Partenaire
        $compte->setNumero($generer->generer());
        $compte->setDatecreate($date);
        $compte->setUsercreate($users);
        $compte->setPartenaire($partenaire);
        $entityManager->persist($compte);
        $entityManager->flush();

            
        //Faire Un Depot
        $depot->setDatedepot($date);
        $depot->setMontant($compte->getSolde());
        $depot->setComptes($compte);
        $depot->setUser($users);
        $entityManager->persist($depot);
        $entityManager->flush();
        
        $data = [
            'status' => 201,
            'message' => 'compte ajoute'
        ];
        return new JsonResponse($data, 201);
    
}
}

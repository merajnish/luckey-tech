<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Countries;
use App\Entity\States;
use App\Entity\Teacher;

#[Route('/admin')]
final class TeacherController extends AbstractController
{
    private EntityManagerInterface $em;
    private SerializerInterface $serializer;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer, UserPasswordHasherInterface $passwordHasher)
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/teacher', name: 'app_teacher')]
    public function index(): Response
    {
        $teacher = $this->em->getRepository(Teacher::class)->findAll([], ['id' => 'DESC']);
        return $this->render('admin/teacher/teacher-list.html.twig', [
            'teachers' => $teacher,
        ]);
    }

    #[Route('/teacher/edit/{id}', name: 'app_teacher_edit')]
    public function editteacher($id, Request $request): Response
    {
        $teacher = $this->em->getRepository(Teacher::class)->findOneById($id);
        if($teacher){
            $countries = $this->em->getRepository(Countries::class)->findAll();
            $states = $this->em->getRepository(States::class)->findByCountry($teacher->getCountry());
            if($request->isMethod('post')){
                // dd("here");
                $params = $request->request->all();
                $countries = $this->em->getRepository(Countries::class)->findOneById($params['country']);
                $states = $this->em->getRepository(States::class)->findOneById($params['state']);
                $plaintextPassword = $params['first_name'] . "#2025";
                $teacher_obj = $teacher;
                $teacher_obj->setEmail($params['email']);
                // $teacher_obj->setRoles(['ROLE_teacher']);
                $teacher_obj->setFirstName($params['first_name']);
                $teacher_obj->setLastName($params['last_name']);
                $teacher_obj->setGender($params['gender']);
                $teacher_obj->setDob(\DateTime::createFromFormat('Y-m-d', $params['dob']));
                $teacher_obj->setDateOfAdmission(\DateTime::createFromFormat('Y-m-d', $params['doa']));
                $teacher_obj->setMobile($params['mobile']);
                $teacher_obj->setQualification($params['qualification']);
                $teacher_obj->setCourse($params['course']);
                $teacher_obj->setAddress1($params['address1']);
                $teacher_obj->setAddress2($params['address2']);
                $teacher_obj->setCity($params['city']);
                $teacher_obj->setCountry($countries);
                $teacher_obj->setState($states);
                $teacher_obj->setZip($params['zip']);
                $teacher_obj->setAadhaar($params['aadhaar']);
                $teacher_obj->setUpdatedAt(new \DateTime());
                $this->em->persist($teacher_obj);
                // if($teacher_obj){
                //     $hashedPassword = $this->passwordHasher->hashPassword($teacher_obj,$plaintextPassword);
                //     $teacher_obj->setPassword($hashedPassword);
                //     $this->em->persist($teacher_obj);
                // }
                $this->em->flush();
                // Redirect to a named route 'app_new_path'
                return $this->redirectToRoute('app_teacher_edit', ['id'=>$id]);
                // dd($params, $teacher_obj, "here");
            }
            // dd($countries, $States);
            return $this->render('admin/teacher/add-teacher.html.twig', [
                'countries' => $countries,
                'teacher'   => $teacher,
                'states'    => $states,
            ]);
        }else{
            // 
        }
    }

    #[Route('/teacher/add', name: 'app_teacher_add')]
    public function teacherAdd(Request $request): Response
    {
        // dd($request->request->all());
        if($request->isMethod('post')){
            $params = $request->request->all();
            // teacher unique registration no
            $prefix = 'TEACH';
            $timestamp = (new \DateTime())->format('Y'); // e.g., "2025"
            $random = str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT); // "000123"
            $registrationNumber = $prefix . $timestamp . $random; // e.g., STU2025000123
            // End teacher unique registration no
            $countries = $this->em->getRepository(Countries::class)->findOneById($params['country']);
            $states = $this->em->getRepository(States::class)->findOneById($params['state']);
            $plaintextPassword = $params['password'];
            $teacher_obj = new teacher();
            $teacher_obj->setEmail($params['email']);
            $teacher_obj->setRoles(['ROLE_TEACHER']);
            $teacher_obj->setFirstName($params['first_name']);
            $teacher_obj->setLastName($params['last_name']);
            $teacher_obj->setGender($params['gender']);
            $teacher_obj->setDob(\DateTime::createFromFormat('Y-m-d', $params['dob']));
            $teacher_obj->setDateOfAdmission(\DateTime::createFromFormat('Y-m-d', $params['doa']));
            $teacher_obj->setMobile($params['mobile']);
            $teacher_obj->setQualification($params['qualification']);
            $teacher_obj->setCourse($params['course']);
            $teacher_obj->setAddress1($params['address1']);
            $teacher_obj->setAddress2($params['address2']);
            $teacher_obj->setCity($params['city']);
            $teacher_obj->setCountry($countries);
            $teacher_obj->setState($states);
            $teacher_obj->setZip($params['zip']);
            $teacher_obj->setAadhaar($params['aadhaar']);
            $teacher_obj->setRegistrationNumber($registrationNumber);
            $teacher_obj->setCreatedAt(new \DateTime());
            $this->em->persist($teacher_obj);
            if($teacher_obj){
                $hashedPassword = $this->passwordHasher->hashPassword($teacher_obj,$plaintextPassword);
                $teacher_obj->setPassword($hashedPassword);
                $this->em->persist($teacher_obj);
            }
            $this->em->flush();
            // Redirect to a named route 'app_new_path'
            return $this->redirectToRoute('app_teacher');
            // dd($params, $teacher_obj, "here");
        }
        $countries = $this->em->getRepository(Countries::class)->findAll();
        return $this->render('admin/teacher/add-teacher.html.twig', [
            'countries' => $countries,
        ]);
    }

    /*#[Route('/get-states/{countryId}', name: 'app_states')]
    public function getStates($countryId): Response
    {
        $states = [];
        $countries = $this->em->getRepository(Countries::class)->findById($countryId);
        $states = $this->em->getRepository(States::class)->findByCountry($countries);
        // $states = $countries?->getStates();
        // dd($states);
        $states = $this->serializer->normalize($states, 'json', ['groups' => 'state:list']);
        // dd($states);
        // if ($countryId == 1) {
        //     $states = ['DL' => 'Delhi', 'MH' => 'Maharashtra'];
        // }

        return new JsonResponse($states);
    }*/
}

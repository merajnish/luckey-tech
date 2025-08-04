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
use App\Entity\Student;

#[Route('/admin')]
final class StudentController extends AbstractController
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

    #[Route('/student', name: 'app_student')]
    public function index(): Response
    {
        $students = $this->em->getRepository(Student::class)->findAll([], ['id' => 'DESC']);
        return $this->render('admin/student/student-list.html.twig', [
            'students' => $students,
        ]);
    }

    #[Route('/student/edit/{id}', name: 'app_student_edit')]
    public function editStudent($id, Request $request): Response
    {
        $student = $this->em->getRepository(Student::class)->findOneById($id);
        if($student){
            $countries = $this->em->getRepository(Countries::class)->findAll();
            $states = $this->em->getRepository(States::class)->findByCountry($student->getCountry());
            if($request->isMethod('post')){
                // dd("here");
                $params = $request->request->all();
                $countries = $this->em->getRepository(Countries::class)->findOneById($params['country']);
                $states = $this->em->getRepository(States::class)->findOneById($params['state']);
                $plaintextPassword = $params['first_name'] . "#2025";
                $student_obj = $student;
                $student_obj->setEmail($params['email']);
                // $student_obj->setRoles(['ROLE_STUDENT']);
                $student_obj->setFirstName($params['first_name']);
                $student_obj->setLastName($params['last_name']);
                $student_obj->setGender($params['gender']);
                $student_obj->setDob(\DateTime::createFromFormat('Y-m-d', $params['dob']));
                $student_obj->setDateOfAdmission(\DateTime::createFromFormat('Y-m-d', $params['doa']));
                $student_obj->setMobile($params['mobile']);
                $student_obj->setQualification($params['qualification']);
                $student_obj->setCourse($params['course']);
                $student_obj->setAddress1($params['address1']);
                $student_obj->setAddress2($params['address2']);
                $student_obj->setCity($params['city']);
                $student_obj->setCountry($countries);
                $student_obj->setState($states);
                $student_obj->setZip($params['zip']);
                $student_obj->setAadhaar($params['aadhaar']);
                $student_obj->setUpdatedAt(new \DateTime());
                $this->em->persist($student_obj);
                // if($student_obj){
                //     $hashedPassword = $this->passwordHasher->hashPassword($student_obj,$plaintextPassword);
                //     $student_obj->setPassword($hashedPassword);
                //     $this->em->persist($student_obj);
                // }
                $this->em->flush();
                // Redirect to a named route 'app_new_path'
                return $this->redirectToRoute('app_student_edit', ['id'=>$id]);
                // dd($params, $student_obj, "here");
            }
            // dd($countries, $States);
            return $this->render('admin/student/add-student.html.twig', [
                'countries' => $countries,
                'student'   => $student,
                'states'    => $states,
            ]);
        }else{
            // 
        }
    }

    #[Route('/student/add', name: 'app_student_add')]
    public function studentAdd(Request $request): Response
    {
        // dd($request->request->all());
        if($request->isMethod('post')){
            $params = $request->request->all();
            // Student unique registration no
            $prefix = 'STU';
            $timestamp = (new \DateTime())->format('Y'); // e.g., "2025"
            $random = str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT); // "000123"
            $registrationNumber = $prefix . $timestamp . $random; // e.g., STU2025000123
            // End Student unique registration no
            $countries = $this->em->getRepository(Countries::class)->findOneById($params['country']);
            $states = $this->em->getRepository(States::class)->findOneById($params['state']);
            $plaintextPassword = $params['first_name'] . "#2025";
            $student_obj = new Student();
            $student_obj->setEmail($params['email']);
            $student_obj->setRoles(['ROLE_STUDENT']);
            $student_obj->setFirstName($params['first_name']);
            $student_obj->setLastName($params['last_name']);
            $student_obj->setGender($params['gender']);
            $student_obj->setDob(\DateTime::createFromFormat('Y-m-d', $params['dob']));
            $student_obj->setDateOfAdmission(\DateTime::createFromFormat('Y-m-d', $params['doa']));
            $student_obj->setMobile($params['mobile']);
            $student_obj->setQualification($params['qualification']);
            $student_obj->setCourse($params['course']);
            $student_obj->setAddress1($params['address1']);
            $student_obj->setAddress2($params['address2']);
            $student_obj->setCity($params['city']);
            $student_obj->setCountry($countries);
            $student_obj->setState($states);
            $student_obj->setZip($params['zip']);
            $student_obj->setAadhaar($params['aadhaar']);
            $student_obj->setRegistrationNumber($registrationNumber);
            $student_obj->setCreatedAt(new \DateTime());
            $this->em->persist($student_obj);
            if($student_obj){
                $hashedPassword = $this->passwordHasher->hashPassword($student_obj,$plaintextPassword);
                $student_obj->setPassword($hashedPassword);
                $this->em->persist($student_obj);
            }
            $this->em->flush();
            // Redirect to a named route 'app_new_path'
            return $this->redirectToRoute('app_student');
            // dd($params, $student_obj, "here");
        }
        $countries = $this->em->getRepository(Countries::class)->findAll();
        return $this->render('admin/student/add-student.html.twig', [
            'countries' => $countries,
        ]);
    }

    #[Route('/get-states/{countryId}', name: 'app_states')]
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
    }
}

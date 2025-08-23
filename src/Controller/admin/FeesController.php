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
use App\Entity\Course;
use App\Entity\Fees;
use App\Entity\Student;

#[Route('/admin')]
final class FeesController extends AbstractController
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

    #[Route('/fees', name: 'app_fees')]
    public function index(): Response
    {
        $fees = $this->em->getRepository(Fees::class)->findAll([], ['id' => 'DESC']);
        return $this->render('admin/fees/fees-list.html.twig', [
            'fees' => $fees,
        ]);
    }

    #[Route('/fees/edit/{id}', name: 'app_fees_edit')]
    public function editfees($id, Request $request): Response
    {
        $fees = $this->em->getRepository(Fees::class)->findOneById($id);
        if($fees){
            $course = $this->em->getRepository(Course::class)->findAll();
            $students = $this->em->getRepository(Student::class)->findAll();
            // dd($fees,$course,$students);
            if($request->isMethod('post')){
                // dd("here");
                $params = $request->request->all();
                $post_course = $this->em->getRepository(Course::class)->findOneById($params['course']);
                $post_students = $this->em->getRepository(Student::class)->findOneById($params['student']);
                $fees_obj = $fees;
                $fees_obj->setStudent($post_students);
                $fees_obj->setCourse($post_course);
                $fees_obj->setAmount($params['amount']);
                $fees_obj->setPaymentMode($params['paymentMode']);
                $fees_obj->setUpdatedAt(new \DateTimeImmutable());
                $this->em->persist($fees_obj);
                $this->em->flush();
                // Redirect to a named route 'app_new_path'
                return $this->redirectToRoute('app_fees_edit', ['id'=>$id]);
                // dd($params, $fees_obj, "here");
            }
            // dd($countries, $States);
            return $this->render('admin/fees/add-fees.html.twig', [
                'fees'   => $fees,
                "courses"    => $course,
                "students"  => $students,
            ]);
        }else{
            // 
        }
    }

    #[Route('/fees/add', name: 'app_fees_add')]
    public function feesAdd(Request $request): Response
    {
        // dd($request->request->all());
        $course = $this->em->getRepository(Course::class)->findAll();
        $students = $this->em->getRepository(Student::class)->findAll();
        if($request->isMethod('post')){
            $params = $request->request->all();
            $post_course = $this->em->getRepository(Course::class)->findOneById($params['course']);
            $post_students = $this->em->getRepository(Student::class)->findOneById($params['student']);
            $fees_obj = new Fees();
            $fees_obj->setStudent($post_students);
            $fees_obj->setCourse($post_course);
            $fees_obj->setReciptNo($this->generatePaymentSerial());
            $fees_obj->setAmount($params['amount']);
            $fees_obj->setPaymentMode($params['paymentMode']);
            $fees_obj->setCreatedAt(new \DateTimeImmutable());
            $this->em->persist($fees_obj);
            // dd($fees_obj);
            $this->em->flush();
            // Redirect to a named route 'app_new_path'
            return $this->redirectToRoute('app_fees');
            // dd($params, $fees_obj, "here");
        }
        return $this->render('admin/fees/add-fees.html.twig',[
            "courses"    => $course,
            "students"  => $students,
        ]);
    }

    #[Route('/fees/delete/{id}', name: 'app_fees_delete')]
    public function feesDelete(Request $request, $id): Response
    {
        $fees = $this->em->getRepository(Fees::class)->findOneById($id);
        if($fees){
            // Remove it
            $this->em->remove($fees);
            $this->em->flush();

            return $this->redirectToRoute('app_fees');
        }
    }

    private function generatePaymentSerial($prefix = 'PAY', $length = 8) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'.rand(10, 99);
        $serial = '';
        for ($i = 0; $i < $length; $i++) {
            $serial .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        return $prefix . $serial;
    }
}

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

#[Route('/admin')]
final class CourseController extends AbstractController
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

    #[Route('/course', name: 'app_course')]
    public function index(): Response
    {
        $course = $this->em->getRepository(Course::class)->findAll([], ['id' => 'DESC']);
        return $this->render('admin/course/course-list.html.twig', [
            'courses' => $course,
        ]);
    }

    #[Route('/course/edit/{id}', name: 'app_course_edit')]
    public function editcourse($id, Request $request): Response
    {
        $course = $this->em->getRepository(Course::class)->findOneById($id);
        if($course){
            if($request->isMethod('post')){
                // dd("here");
                $params = $request->request->all();
                $course_obj = $course;
                $course_obj->setCourseName($params['course_name']);
                $course_obj->setCourseType($params['course_type']);
                $this->em->persist($course_obj);
                $this->em->flush();
                // Redirect to a named route 'app_new_path'
                return $this->redirectToRoute('app_course_edit', ['id'=>$id]);
            }
            return $this->render('admin/course/add-course.html.twig', [
                'course'   => $course,
            ]);
        }else{
            // 
        }
    }

    #[Route('/course/add', name: 'app_course_add')]
    public function courseAdd(Request $request): Response
    {
        if($request->isMethod('post')){
            $params = $request->request->all();
            $course_obj = new Course();
            $course_obj->setCourseName($params['course_name']);
            $course_obj->setCourseType($params['course_type']);
            $this->em->persist($course_obj);
            $this->em->flush();
            // Redirect to a named route 'app_new_path'
            return $this->redirectToRoute('app_course');
        }
        return $this->render('admin/course/add-course.html.twig');
    }

    #[Route('/course/delete/{id}', name: 'app_course_delete')]
    public function courseDelete(Request $request, $id): Response
    {
        $course = $this->em->getRepository(Course::class)->findOneById($id);
        if($course){
            // Remove it
            $this->em->remove($course);
            $this->em->flush();

            return $this->redirectToRoute('app_course'); // Redirect to list page
        }
    }
}

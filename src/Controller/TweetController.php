<?php

namespace App\Controller;

use App\Entity\Tweet;
use App\Entity\User;
use App\Form\TweetType;
use App\Repository\TweetRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class TweetController extends AbstractController
{
    protected $managerRegistry;
    protected $security;

    public function __construct(ManagerRegistry $managerRegistry, Security $security)
    {
        $this->managerRegistry = $managerRegistry;
        $this->security = $security;
    }

    /**
     * @Route("/", name="app_tweet")
     */
    public function index(TweetRepository $repository): Response
    {
        $tweets = $repository->findAllOrderedByDate();

        return $this->render('tweet/index.html.twig', [
            'controller_name' => 'TweetController',
            'tweets' => $tweets
        ]);
    }

    /**
     * @Route("/tweet/{id}", name="app_tweet_show")
     */
    public function show(Tweet $tweet): Response
    {
        return $this->render('tweet/show.html.twig', [
            'tweet' => $tweet
        ]);
    }

    /**
     * @Route("/add", name="create_tweet")
     */
    public function create(Request $request): Response
    {
        $manager = $this->managerRegistry->getManager();
        $tweet = new Tweet();

        $form = $this->createForm(TweetType::class, $tweet);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $tweet->setAuthor($this->security->getUser());
            $tweet->setCreatedAt(new \DateTime());
            $manager->persist($tweet);
            $manager->flush();
            return $this->redirectToRoute('app_tweet');
        }

        return $this->render('tweet/add.html.twig', [
            'controller_name' => 'TweetController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/like/{id}", name="tweet_like")
     */
    public function like(Tweet $tweet, Security $security): Response
    {
        $user = $security->getUser();
        //cast to User
        $tweet->addLike($user);
        $this->managerRegistry->getManager()->flush();
        return $this->redirectToRoute('app_tweet');
    }
}

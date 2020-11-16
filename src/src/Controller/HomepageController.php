<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TagRepository;
use App\Repository\ConfigRepository;
use App\Repository\TweetRepository;
use App\Form\TagType;
use App\Form\TwitterCredentialsType;
use App\Entity\Tag;
use App\Entity\Config;
use App\Service\TwitterApi;
use Symfony\Component\Serializer\SerializerInterface;
use Exception;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(Request $request, TagRepository $tagRepository, ConfigRepository $configRepository): Response
    {
        $tags = $tagRepository->findAll();
        
        $tag = new Tag();
        $tagForm = $this->createForm(TagType::class, $tag);
        $tagForm->handleRequest($request);
        if ($tagForm->isSubmitted() && $tagForm->isValid()) {
            $tag = $tagForm->getData();
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            $this->addFlash(
                'notice',
                'Tag has been added!'
            );

            return $this->redirectToRoute('homepage');
        }

        $twitterCredentialsForm = $this->createForm(
            TwitterCredentialsType::class,
            [
                "consumer_key" => $configRepository->getConfig("consumer_key"),
                "consumer_secret" => $configRepository->getConfig("consumer_secret"),
                "access_token" => $configRepository->getConfig("access_token"),
                "access_token_secret" => $configRepository->getConfig("access_token_secret"),
            ]
        );
        $twitterCredentialsForm->handleRequest($request);
        if ($twitterCredentialsForm->isSubmitted() && $twitterCredentialsForm->isValid()) {
            $twitterCredentials = $twitterCredentialsForm->getData();
            
            foreach ($twitterCredentials as $credentialName => $credentialValue) {
                $config = $configRepository->getConfigEntity($credentialName);
                $config->setValue($credentialValue);
                $config->setCode($credentialName);

                $em = $this->getDoctrine()->getManager();
                $em->persist($config);
                $em->flush();
            }

            $this->addFlash(
                'success',
                'Credentials have been setted!'
            );

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'homepage/index.html.twig', [
                'tags' => $tags,
                'tagForm' => $tagForm->createView(),
                'twitterCredentialsForm' => $twitterCredentialsForm->createView()
            ]
        );
    }

    /**
     * @Route("/delete/tag/{tagId}", name="delete_tag")
     */
    public function deleteTag(int $tagId, TagRepository $tagRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $tag = $tagRepository->find($tagId);
        $em->remove($tag);
        $em->flush();

        $this->addFlash(
            'notice',
            'Tag has been deleted!'
        );

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/scrape/tweets", name="scrape_tweets")
     */
    public function scrapeTweets(
        TwitterApi $twitterApi,
        ConfigRepository $configRepository,
        TweetRepository $tweetRepository,
        TagRepository $tagRepository,
        SerializerInterface $serializer
    ): Response {
        $entityManager = $this->getDoctrine()->getManager();
        
        try {
            $twitterApi->scrapeTweets(
                $configRepository,
                $tweetRepository,
                $tagRepository,
                $entityManager,
                $serializer
            );
        } Catch(Exception $e) {
            $this->addFlash(
                'warning',
                $e->getMessage()
            );

            return $this->redirectToRoute('homepage');
        }
            
        $this->addFlash(
            'success',
            'Tweets has been scraped!'
        );

        return $this->redirectToRoute('homepage');
    }
}

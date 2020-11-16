<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TweetRepository;

class TweetsTableController extends AbstractController
{
    /**
     * @Route("/tweets/table", name="tweets_table")
     */
    public function index(TweetRepository $tweetRepository): Response
    {
        $tweets = $tweetRepository->findBy([], ["created_at" => "DESC"], 100, null);

        return $this->render(
            "tweets_table/index.html.twig", [
            "tweets" => $tweets
            ]
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Service;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Repository\ConfigRepository;
use App\Repository\TweetRepository;
use App\Repository\TagRepository;
use App\Entity\Tweet;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;

class TwitterApi
{
    const LIMIT = 100;

    public function scrapeTweets(
        ConfigRepository $config,
        TweetRepository $tweetRepository,
        TagRepository $tagRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ): bool {
        $names = $this->getTagNames($tagRepository);
        if ($names === "") {
            throw new \Exception("Please add some tags first!");
        }

        if ($config->getConfig("consumer_key") === null 
            || $config->getConfig("consumer_secret") === null
            || $config->getConfig("access_token") === null
            || $config->getConfig("access_token_secret") === null
        ) {
            throw new \Exception("Configure credentials first!");
        }

        $connection = new TwitterOAuth(
            $config->getConfig("consumer_key"),
            $config->getConfig("consumer_secret"),
            $config->getConfig("access_token"),
            $config->getConfig("access_token_secret")
        );

        $auth = $connection->get("account/verify_credentials"); 
        if (isset($auth->errors)) {
            throw new \Exception("Bad credentials!");
        }     
        $tweets = $connection->get("search/tweets", ["count" => self::LIMIT, "q" => $names]);
        
        foreach ($tweets->statuses as $tweet) {
            $tweetJson = $serializer->serialize($tweet, 'json');

            if ($tweetRepository->findByTweetId($tweet->id) === null) {
                $tweetEntity = new Tweet();
                $tweetEntity
                    ->setTweetId((string) $tweet->id)
                    ->setCreatedAt(new \DateTime($tweet->created_at))
                    ->setText($tweet->text)
                    ->setSource($tweet->source)
                    ->setJson($tweetJson)
                    ->setUser($tweet->user->name);
                
                $entityManager->persist($tweetEntity);
                $entityManager->flush();
            }   
        }
       
        return true;
    }

    public function getTagNames(TagRepository $tagRepository): string
    {
        $tags = $tagRepository->findAll();
        $tagNames = [];

        foreach ($tags as $tag) {
            $tagNames[] = $tag->getName();
        }  

        return implode(" OR ", $tagNames);
    }
}
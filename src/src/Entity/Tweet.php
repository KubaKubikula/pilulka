<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TweetRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TweetRepository::class)
 */
class Tweet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $tweetId;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $created_at;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $text;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $source;

    /**
     * @ORM\Column(type="text")
     */
    private string $json;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTweetId(): ?string
    {
        return $this->tweetId;
    }

    public function setTweetId(string $tweetId): self
    {
        $this->tweetId = $tweetId;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function getCreatedAtString(): ?string
    {
        return $this->created_at === null ? null : $this->created_at->format('Y-m-d H:i:s');
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getJson(): ?string
    {
        return $this->json;
    }

    public function setJson(string $json): self
    {
        $this->json = $json;

        return $this;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }
}

<?php

namespace MauticPlugin\MauticFacebookBusinessBundle\Model\Webhooks;

class PostComment
{
    private $pageId;
    private $informTimestamp;
    private $commenterId;
    private $commenterName;
    private $postId;
    private $message;
    private $isHidden;

    public function getPageId()
    {
        return $this->pageId;
    }

    public function getInformTimestamp()
    {
        return $this->informTimestamp;
    }

    public function getCommenterId()
    {
        return $this->commenterId;
    }

    public function getCommenterName()
    {
        return $this->commenterName;
    }

    public function getPostId()
    {
        return $this->postId;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getIsHidden()
    {
        return $this->isHidden;
    }

    public function __construct($pageId, $informTimestamp, $commenterId, $commenterName, $postId, $message, $isHidden)
    {
        $this->pageId          = $pageId;
        $this->informTimestamp = $informTimestamp;
        $this->commenterId     = $commenterId;
        $this->commenterName   = $commenterName;
        $this->postId          = $postId;
        $this->message         = $message;
        $this->isHidden        = $isHidden;
    }

    public static function parseToObjFrom($jsonStr)
    {
        $parsedJson      = json_decode($jsonStr);
        $pageId          = $parsedJson['id'];
        $informTimestamp = $parsedJson['time'];
        $commenterId     = $parsedJson['changes'][0]['value']['from']['id'];
        $commenterName   = $parsedJson['changes'][0]['value']['from']['name'];
        $postId          = $parsedJson['changes'][0]['value']['post_id'];
        $message         = $parsedJson['changes'][0]['value']['message'];
        $isHidden        = $parsedJson['changes'][0]['value']['is_hidden'];

        return new PostComment($pageId, $informTimestamp, $commenterId, $commenterName, $postId, $message, $isHidden);
    }
}

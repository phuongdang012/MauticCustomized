<?php

namespace MauticPlugin\MauticFacebookBusinessBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;

class FacebookBusinessHomeController extends CommonController
{
    public function goToHomeAction()
    {
        return $this->render('./Views/home.html.php');
    }

    public function loginCallbackAction()
    {
    }
}

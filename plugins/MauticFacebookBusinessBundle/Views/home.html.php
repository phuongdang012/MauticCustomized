<?php

$view->extend('MauticCoreBundle:Default:content.html.php');

$view['slots']->set('headerTitle', $view['translator']->trans('mautic.plugin.facebook_business'));

?>

<div class="panel panel-default">

    <fb:login-button scope="pages_show_list,email,leads_retrieval,business_management" , onlogin="checkLoginStatus();">
    </fb:login-button>
</div>
<script src="../Assets/js/facebook_login.js"></script>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>
<?php

$view->extend('MauticCoreBundle:Default:content.html.php');

$view['slots']->set('headerTitle', $view['translator']->trans('mautic.plugin.facebook_business'));

?>
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v8.0&appId=346384536515478&autoLogAppEvents=1" nonce="tGoi1lzf"></script>
<script type="text/javascript">
    window.fbAsyncInit = function() {
        FB.init({
            appId: '',
            cookie: false,
            xfbml: true,
            version: '7.0'
        });
    };

    function checkLoginStatus() {
        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });
    }

    function statusChangeCallback(response) {
        if (response.status === 'connected') {
            saveLoginToken(response.authResponse.accessToken);
        }
    }

    function saveLoginToken(token) {
        var url = Routing.generate(
            'mautic_facebook_business_auth_callback', {
                'access_token': token,
            }
        );
        const request = new Request(url, {
            method: 'POST'
        });

        fetch(request)
            .then(response => {
                console.log(response);
            })
            .catch(error => {
                console.log(error);
            })
    }
</script>

<div class="fb-login-button" data-size="large" data-button-type="continue_with" data-layout="rounded" data-auto-logout-link="true" data-use-continue-as="true" data-width="" data-onlogin="checkLoginStatus();" data-scope="email,pages_show_list,leads_retrieval,pages_read_engagement,pages_messaging"></div>
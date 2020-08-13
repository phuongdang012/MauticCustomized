<!-- <?php

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

<div class="fb-login-button" data-size="large" data-button-type="continue_with" data-layout="rounded" data-auto-logout-link="true" data-use-continue-as="true" data-width="" data-onlogin="checkLoginStatus();" data-scope="email,pages_show_list,leads_retrieval,pages_read_engagement,pages_messaging"></div> -->

<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$defaultInputClass = 'button';
$containerType     = 'div-wrapper';

include __DIR__.'/../../../../app/bundles/FormBundle/Views/Field/field_helper.php';

$action   = $app->getRequest()->get('objectAction');
$settings = $field['properties'];

$integrations = (isset($settings['integrations']) and !empty($settings['integrations'])) ? explode(',', substr($settings['integrations'], 0, -1))
    : [];

$formName    = str_replace('_', '', $formName);
$formButtons = (!empty($inForm)) ? $view->render(
    'MauticFormBundle:Builder:actions.html.php',
    [
        'deleted'        => false,
        'id'             => $id,
        'formId'         => $formId,
        'formName'       => $formName,
        'disallowDelete' => false,
    ]
) : '';

$label = (!$field['showLabel'])
    ? ''
    : <<<HTML
<label $labelAttr>{$view->escape($field['label'])}</label>
HTML;

$script = '<script src="'.$view['router']->url('$', ['formName' => $formName], true)
    .'" type="text/javascript" charset="utf-8" async="async"></script>';

$html = <<<HTML
	<div $containerAttr>{$formButtons}{$label}
HTML;
?>
<?php echo $script; ?>

<?php
echo $html;
foreach ($integrations as $integration) {
    if (isset($settings['buttonImageUrl'])) {
        echo '<a href="#" onclick="openOAuthWindow(\''.$settings['authUrl_'.$integration].'\')"><img src="'.$settings['buttonImageUrl'].'btn_'
            .$integration.'.png"></a>';
    }
}

?>
</div>
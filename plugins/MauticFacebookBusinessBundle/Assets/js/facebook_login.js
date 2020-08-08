$(document).ready(function () {
  $.ajaxSetup({ cache: true });
  $.getScript("./fb_sdk.js", function () {
    FB.init({
      appId: "",
      version: "v2.7",
    });
    $("#loginbutton,#feedbutton").removeAttr("disabled");
  });
});

function checkLoginStatus() {
  FB.getLoginStatus(function (response) {
    if (response.status === "connected") {
      var uid = response.authResponse.userID;
      var accessToken = response.authResponse.accessToken;
    } else {
      this.login();
    }
  });
}

function login() {
  FB.login(
    function (response) {
      if (response.authResponse) {
      }
    },
    {
      auth_type: "rerequest",
      scope:
        "pages_show_list,email,leads_retrieval,business_management,pages_messaging,pages_read_user_content",
      enable_profile_selector: true,
    }
  );
}

[![Build Status](https://travis-ci.org/e0ipso/simple_oauth.svg?branch=8.x-1.x)](https://travis-ci.org/e0ipso/simple_oauth)

Simple OAuth is an implementation of the OAuth 2.0 Authorization Framework: Bearer Token specification. Using OAuth 2.0 Bearer Token is very easy. See how you can get the basics working in **less than a minute**! This project is focused in simplicity of use and flexibility. When deciding which project to use, also consider other projects like [OAuth](https://www.drupal.org/project/oauth), an OAuth 1 implementation that doesn't rely on you having https in your production server.

### Quick demo

The following animation shows the steps you need to do in order to authorize a REST request with a token.

1.  Install the Simple Oauth module.
2.  Go to [REST UI](https://drupal.org/project/restui) and enable the _token bearer_ authentication in your resource.
3.  (Not shown) Permissions are set to only allow to view nodes via REST with the authenticated user.
4.  Go to your user's profile and create a token:

1.  Select the user you want to identify.
2.  Select the token expiration date.
3.  Select the resource. You can create your own token resources or use the default _Global_. A token resource is a collection of permissions. The user authenticated via a bearer token will have access denied for any permission outside of the token resource. For permissions inside of the token resource, regular Drupal permission checks will apply.

6.  Request a node via REST without authentication and watch it fail.
7.  Request a node via REST with the header `Authorization: Bearer {YOUR_TOKEN}` and watch it succeed.

![Simple OAuth animation](https://www.drupal.org/files/project-images/simple_oauth.gif)

### Video tutorials

[![](https://www.drupal.org/files/2015-12-10%2009-04-11.png)](https://youtu.be/kohs5MXESXc) Watch a detailed explanation on how to use this module in the video tutorials:

1.  [Basic configuration.](https://youtu.be/kohs5MXESXc)
2.  [Refresh your tokens.](https://youtu.be/E-wUKkQa1OM)
3.  [Add extra security with resources.](https://youtu.be/PR0oBCCSxgE)

### My token has expired!

First, that is a good thing. Tokens are like cash, if you have it you can use it. You don't need to prove that token belongs to you, so don't let anyone steal your token. In order to lower the risk tokens should expire fairly quickly. If your token expires in 120s then it will be only usable during that window.

#### What do I do if my token was expired?

Along with your access token, an authentication token is created. It's called the _refresh token_ . It's a longer lived token, that it's associated to an access token and can be used to create a replica of your expired access token. You can then use that new access token normally. To use your refresh token you will need to make a request against `/simple-oauth/refresh` providing the header `Authorization: Bearer {YOUR REFRESH TOKEN}`. That will return a JSON document with the new token. That URL can only be accessed with your refresh token, even if your access token is still valid.

#### What do I do if my refresh token was also expired?

Then you will need to log into Drupal and go to your profile page to generate a new token from scratch. You can avoid this by refreshing your access token before your refresh token expires. This way you avoid the need to require the user to go to Drupal to create a new token. Another way to mitigate this is to use longer expiration times in your tokens. This will work, but the the recommendation is to refresh your token in time.

### Recommendation

Check the official documentation on the [Bearer Token Usage](http://tools.ietf.org/html/rfc6750). And **turn on SSL!**.

### Issues and contributions

Issues and development happens in the [GitHub repository](https://github.com/e0ipso/simple_oauth).

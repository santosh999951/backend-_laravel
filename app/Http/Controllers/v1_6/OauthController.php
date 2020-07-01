<?php
/**
 * Oauth Controller containing swagger documentation for oauth apis
 */

namespace App\Http\Controllers\v1_6;

/**
 * Class OauthController
 */
class OauthController extends Controller
{
        //phpcs:disable
        /**
         * User login
         *
         * @return \Illuminate\Http\JsonResponse
         *
         * @SWG\Post(
         *     path="/oauth/token",
         *     tags={"Oauth"},
         *     description="User login - generate tokens",
         *     operationId="oauth.post.token",
         *     consumes={"application/x-www-form-urlencoded"},
         *     produces={"application/json"},
         *     @SWG\Parameter(ref="#/parameters/client_id_in_form"),
         *     @SWG\Parameter(ref="#/parameters/client_secret_in_form"),
         *     @SWG\Parameter(ref="#/parameters/grant_type_in_form"),
         *     @SWG\Parameter(ref="#/parameters/access_scopes_in_form"),
         *     @SWG\Parameter(ref="#/parameters/username_in_form"),
         *     @SWG\Parameter(ref="#/parameters/base64encode_password_in_form"),
         *     @SWG\Response(
         *         response=200,
         *         description="Generates access token and refresh token"
         *     ),
         *     @SWG\Response(
         *         response=400,
         *         description="Parameter missing"
         *     ),
         *     @SWG\Response(
         *         response=401,
         *         description="Invalid request params"
         *     )
         * )
         */


        /**
         * Refresh access token using refresh token
         *
         * @return \Illuminate\Http\JsonResponse
         *
         * @SWG\Post(
         *     path="/oauth/token#refresh",
         *     tags={"Oauth"},
         *     description="Refresh access token",
         *     operationId="oauth.post.token.refresh",
         *     consumes={"application/x-www-form-urlencoded"},
         *     produces={"application/json"},
         *     @SWG\Parameter(ref="#/parameters/client_id_in_form"),
         *     @SWG\Parameter(ref="#/parameters/client_secret_in_form"),
         *     @SWG\Parameter(ref="#/parameters/refresh_grant_type_in_form"),
         *     @SWG\Parameter(ref="#/parameters/access_scopes_in_form"),
         *     @SWG\Parameter(ref="#/parameters/refresh_token_in_form"),
         *     @SWG\Response(
         *         response=200,
         *         description="Refreshes access token and refresh token"
         *     ),
         *     @SWG\Response(
         *         response=400,
         *         description="Parameter missing"
         *     ),
         *     @SWG\Response(
         *         response=401,
         *         description="Invalid request params"
         *     )
         * )
         */

}//end class

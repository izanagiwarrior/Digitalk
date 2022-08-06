<?php


/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// API route group
$router->group(['prefix' => 'api'], function () use ($router) {

    // ==============================================================
    //  ========================== API ==============================
    //  =============================================================

    // Matches /api/register
    // Function : Register
    $router->post('register', 'AuthController@register');

    // Matches /api/login
    // Function : Login
    $router->post('login', 'AuthController@login');
    //  =============================================================
    //  ========================= AUTH ==============================
    //  =============================================================

    $router->group(["prefix" => 'messages'], function () use ($router) {

        // Matches /api/messages
        // Function : Get All Messages
        $router->get('/', 'AuthController@messages');

    });

    $router->group(['middleware' => 'auth'], function () use ($router) {

        //  ====================== PROFILE ============================

        $router->group(["prefix" => 'profile'], function () use ($router) {

            // Matches /api/profile
            // Function : Get User Profile
            $router->get('/', 'AuthController@profile');

        });

        //  ====================== MESSAGES ============================

        $router->group(["prefix" => 'messages'], function () use ($router) {
            // Matches /api/messages
            // Function : Send Messages
            $router->post('/', 'AuthController@send_messages');

        });

        //  ====================== LOGOUT ============================

        // Matches /api/logout
        // Function : Logout
        $router->get('logout', 'AuthController@logout');
    });

    //  =============================================================
    //  ======================= END AUTH ============================
    //  =============================================================

});
    // ==============================================================
    //  ======================== END API ============================
    //  =============================================================

// =====================================================================================================================================================================================================
// =====================================================================================================================================================================================================
// =====================================================================================================================================================================================================

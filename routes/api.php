<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:api']], function () {
    // Permissions
    Route::apiResource('permissions', 'PermissionsApiController');

    // Roles
    Route::apiResource('roles', 'RolesApiController');

    // Users
    Route::apiResource('users', 'UsersApiController');

    //Tamhub
    Route::apiResource('tamhubs', 'TamhubApiController');
    
    //Tamhub Resource Category
    Route::apiResource('resourcecategorys', 'ResourceCategoryApiController');

   //Library 
    Route::apiResource('librarys', 'LibraryApiController');

    //Library Categorys
     Route::apiResource('librarycategorys', 'LibraryCategoryApiController');

   //Counselor Assignments
    Route::apiResource('counselorassignments', 'CounselorAssignmentApiController');

    //Counselor Categorys
    Route::apiResource('counselorcategorys', 'CounselorCategoryApiController');

    //State 
    Route::apiResource('states', 'StateApiController');

    //get state api 
    Route::get('get-state', 'StateApiController@getstate')->name('get-state.getstate');

    //Profile details
    Route::get('users-profiles', 'UserAuthApiController@profile');

    //Profile update
    Route::post('users-profiles-update', 'UserAuthApiController@editProfile');

    //Profile update
     Route::post('state-by-resource-category', 'TamhubApiController@stateResourceCategorys');

    //Profile update
     Route::post('privacy-policy', 'PrivacyPolicyApiController@privacyPolicy');

    //Asnyc
    Route::post('counselor-async-user', 'CounselorAssignmentApiController@async');
    Route::post('async-chat', 'CounselorAssignmentApiController@asyncChat');

    //user Asnyc chat
    Route::post('async-get-chats', 'CounselorAssignmentApiController@getChat');

    //user Live chat
     Route::post('user-live-chat-assign-to-counsellor', 'CounselorLiveChatApiController@liveChatAssign');


    Route::post('current-live-chat-data-get', 'CounselorLiveChatApiController@liveChatAssignGet');

    //user Live chat
    Route::post('user-live-chat', 'CounselorLiveChatApiController@liveChat');

    //user Live chat list
    Route::post('user-live-chat-list', 'CounselorLiveChatApiController@getLiveChat');


    // Filter data 
    Route::post('user-get-chat-history-filter', 'CounselorLiveChatApiController@getChatHistory');

    Route::post('chat-details', 'CounselorLiveChatApiController@chatDetails');

    // user-live-queue-remove
    Route::post('user-live-queue-remove', 'CounselorLiveChatApiController@liveQueueRemove');

    Route::post('current-queue-remove-user', 'CounselorLiveChatApiController@liveCurrentQueueRemove');

    //user Live chat close feedback
    Route::post('user-live-chat-feedback', 'CounselorLiveChatApiController@liveChatFeedback');


    Route::post('user-resume-chat', 'CounselorLiveChatApiController@user_resume_chat');

    Route::post('fcm-token-get', 'CounselorLiveChatApiController@fcmTokenGet');
    Route::post('fcm-Token-Get-Second', 'CounselorLiveChatApiController@fcmTokenGetSecond');

    Route::get('user-working-time', 'CounselorLiveChatApiController@workingTime');

    //user chat filter 
    Route::post('user-chat-filter', 'CounselorAssignmentApiController@chatFilter');

    //Api push notification 
    Route::get('/push-notificaiton', 'NotificationApiController@index')->name('push-notificaiton');
    Route::post('/store-token', 'NotificationApiController@storeToken')->name('store.token');
    Route::post('/send-notification', 'NotificationApiController@sendNotification')->name('send.notification');

});

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin'], function () {

    // //customer routes
    // Route::get('users', 'UsersApiController@index');
    Route::post('customers-store', 'CustomersApiController@store');

    Route::post('/login', 'UserAuthApiController@login')->name('login.api');
    Route::post('/register','UserAuthApiController@register')->name('register.api');
    Route::post('/logout', 'UserAuthApiController@logout')->name('logout.api');
     Route::post('/logoutUser', 'UserAuthApiController@logoutUser')->name('logoutUser.api');

    //get_feq
    Route::get('/feq-get', 'FeqApiController@feq_get')->name('feq-get');

    //questionsend
    Route::post('/send-question', 'QuestionApiController@send')->name('send-question');

});





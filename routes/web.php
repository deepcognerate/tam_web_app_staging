<?php


use App\Http\Controllers\Admin\Admin_to_user_notificationController;
use App\Http\Controllers\Admin\FeqController;
use App\Http\Controllers\Admin\Troopers_togtherController;
use App\Http\Middleware\AuthGates;

Route::redirect('/', '/login');

Route::get('admin/user-assign-to-admin', '\App\Http\Controllers\Auth\LoginController@userAssignToAdmin_cronJob');

Route::get('clear-cache', function() {
    Artisan::call('cache:clear');
    echo "success";
});

Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }
    return redirect()->route('admin.home');
});

Route::redirect('/', '/login');
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Auth::routes(['register' => false]);

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Book Appointment
    Route::delete('bookappointments/destroy', 'BookAppointmentController@massDestroy')->name('bookappointments.massDestroy');
    Route::post('bookappointments/media', 'BookAppointmentController@storeMedia')->name('bookappointments.storeMedia');
    Route::resource('bookappointments', 'BookAppointmentController');

    // Tamhub
    Route::delete('tamhubs/destroy', 'TamHubController@massDestroy')->name('tamhubs.massDestroy');
    Route::post('tamhubs/media', 'TamHubController@storeMedia')->name('tamhubs.storeMedia');
    Route::resource('tamhubs', 'TamHubController');

     //Library 
     Route::delete('librarys/destroy', 'LibraryController@massDestroy')->name('librarys.massDestroy');
     Route::post('librarys/media', 'LibraryController@storeMedia')->name('librarys.storeMedia');
     Route::resource('librarys', 'LibraryController');
    
     
    // Resource Category
    Route::delete('librarycategorys/destroy', 'LibraryCategoryController@massDestroy')->name('librarycategorys.massDestroy');
    Route::post('librarycategorys/media', 'LibraryCategoryController@storeMedia')->name('librarycategorys.storeMedia');
    Route::resource('librarycategorys', 'LibraryCategoryController');
    
    // Resource Category
    Route::delete('resourcecategorys/destroy', 'ResourceCategoryController@massDestroy')->name('resourcecategorys.massDestroy');
    Route::post('resourcecategorys/media', 'ResourceCategoryController@storeMedia')->name('resourcecategorys.storeMedia');
    Route::resource('resourcecategorys', 'ResourceCategoryController');

    //Category 
    Route::delete('categorys/destroy', 'CategoryController@massDestroy')->name('categorys.massDestroy');
    Route::post('categorys/media', 'CategoryController@storeMedia')->name('categorys.storeMedia');
    Route::resource('categorys', 'CategoryController');


    // Feature 
    Route::delete('features/destroy', 'FeatureController@massDestroy')->name('features.massDestroy');

    Route::resource('features', 'FeatureController');
    // Counselor 
    Route::delete('counselors/destroy', 'CounselorController@massDestroy')->name('counselors.massDestroy');
    Route::resource('counselors', 'CounselorController');
    Route::get('mychat/{id}', 'CounselorController@mychat')->name('counselors.mychat');
    Route::get('counselor-availability/{status}', 'CounselorController@counselorAvailability');
    Route::get('mychatAdmin', 'CounselorController@mychatAdmin')->name('counselors.mychatAdmin');

     // Counselor Assignments
     Route::delete('counselorassignments/destroy', 'CounselorAssignmentController@massDestroy')->name('counselorassignments.massDestroy');
     Route::resource('counselorassignments', 'CounselorAssignmentController');
     Route::get('counselor-assignment/{counselorId}/{userId}', 'CounselorAssignmentController@counselorAssignUser');
    
     // admin user assign to counselor 
     Route::get('admin-user-assign-to-counselors', 'CounselorAssignmentController@adminUserAssignToCounselor');

     Route::get('admin-escalated-Assign-To', 'CounselorAssignmentController@escalatedAssignTo');

     

     // Waiting assign to 

    Route::get('admin-waiting-Assign-To', 'CounselorAssignmentController@waitingAssignTo');
   
    // time left user assign to admin
    Route::get('time-left-user-assign-admin', 'CounselorAssignmentController@userAssignToAdmin');

    // Past chat report 

    // Counselor Current cases
    Route::delete('counselorcurrentcases/destroy', 'CounselorCurrentCasesController@massDestroy')->name('counselor_current_cases.massDestroy');
    Route::resource('counselorcurrentcases', 'CounselorCurrentCasesController');
    Route::get('counselor-current-cases', 'CounselorCurrentCasesController@currentCounselor')->name('counselor-current-cases.currentCounselor');
    Route::get('counselor-assign-user/{userId}', 'CounselorCurrentCasesController@counselorAssignUser')->name('counselor-assign-user.counselorAssignUser');

    Route::get('counselor-assign-user-admin/{userId}', 'CounselorCurrentCasesController@counselorAssignUserAdmin')->name('counselor-assign-user-admin.counselorAssignUserAdmin');

    Route::get('user-assign-admin', 'CounselorCurrentCasesController@userAssignAdmin')->name('user-assign-admin.userAssignAdmin');

    Route::get('user-assign-admin-live', 'CounselorCurrentCasesController@userAssignAdminLive')->name('user-assign-admin-live.userAssignAdminLive');

    Route::get('ajax-table-refresh-Live-async', 'CounselorCurrentCasesController@ajaxTableRefreshLiveAsync')->name('ajax-table-refresh-Live-async.ajax_table_refresh_live_async');

    Route::get('close-chat-async', 'CounselorCurrentCasesController@closeChat')->name('close-chat-async.closeChat');

     Route::get('chat-async-notification', 'CounselorCurrentCasesController@notificationChat')->name('chat-async-notification.notificationChat');

    Route::get('close-chat-async-admin', 'CounselorCurrentCasesController@closeChatAdmin')->name('close-chat-async-admin.closeChatAdmin');

    Route::post('close-chat-live/{userId}', 'CounselorCurrentCasesController@closeChatLive')->name('chat-closed-live.closeChatLive');

    Route::post('close-chat-live-admin/{userId}', 'CounselorCurrentCasesController@closeChatLiveByAdmin')->name('chat-closed-live-admin.closeChatLiveByAdmin');


    //counselor and user chat
    Route::get('counselor-assign-user-chat/{userId}/{categoryId}', 'CounselorCurrentCasesController@counselorUserChat')->name('counselor-assign-user-chat.counselorUserChat');

    Route::post('counselor-chat', 'CounselorCurrentCasesController@chat')->name('counselor-chat.chat');

    Route::post('counselor-chat-live', 'CounselorCurrentCasesController@liveChat')->name('counselor-chat-live.liveChat');

    Route::get('counselor-chat-update-chat/{userId}', 'CounselorCurrentCasesController@update_chat_ajax')->name('counselor-chat-update-chat.update_chat_ajax');

    Route::get('counselor-chat-update-chat-live/{userId}', 'CounselorCurrentCasesController@update_chat_live_ajax')->name('counselor-chat-update-chat-live.update_chat_live_ajax');

    Route::get('counselor-start-chat-live/{userId}', 'CounselorCurrentCasesController@start_chat_live_ajax')->name('counselor-start-chat-live.start_chat_live_ajax');

    Route::get('counselor-chat-resume-admin', 'CounselorCurrentCasesController@chat_resume_admin')->name('counselor-chat-resume-admin.chat_resume_admin');

    Route::get('live-chat-typing-start', 'CounselorCurrentCasesController@live_chat_typing_start')->name('live-chat-typing-start.live_chat_typing_start');

    Route::get('live-chat-typing-stop', 'CounselorCurrentCasesController@live_chat_typing_stop')->name('live-chat-typing-stop.live_chat_typing_stop');

    Route::get('chat-close-notification', 'CounselorCurrentCasesController@live_chat_close_notification')->name('lichat-close-notification.live_chat_close_notification');

    Route::get('counsellor-timer-time-send', 'CounselorCurrentCasesController@counsellor_timer_time_send')->name('counsellor-timer-time-send.counsellor_timer_time_send');

    Route::get('admin-user-check-chat-resume', 'CounselorCurrentCasesController@adminUserCheckChatResume')->name('admin-user-check-chat-resume.adminUserCheckChatResume');


    //Live chat 
    Route::get('counselor-live-chat/{userId}', 'CounselorCurrentCasesController@counselorLiveChat')->name('counselor-live-chat.counselorLiveChat');

    Route::get('live-chat-view-admin/{counselor_assignment_id}', 'CounselorCurrentCasesController@chat_view_show')->name('live-chat-view-admin.chat_view_show');


    // Counselor Past Cases
    Route::delete('counselor-past-cases/destroy', 'CounselorPastCasesController@massDestroy')->name('counselor-past-cases.massDestroy');
    
    Route::get('past-chat-history/{pastChatId}', 'CounselorPastCasesController@show')->name('past-chat-history.show');

    Route::get('past-chat-history-async/{pastChatId}', 'CounselorPastCasesController@showAsync')->name('past-chat-history-async.showAsync');



    

    Route::resource('counselor-past-cases', 'CounselorPastCasesController');

     // my chat report 
     Route::get('my-chat-admin-report', 'ReportController@myChatAdminReport')->name('my-chat-admin-report.myChatAdminReport');
    Route::get('my-chat-admin-report-live', 'ReportController@myChatAdminReportLive')->name('my-chat-admin-report-live.myChatAdminReportLive');

    Route::get('live-Waiting-Escalated-Chats', 'ReportController@liveWaitingEscalatedChats')->name('live-Waiting-Escalated-Chats.liveWaitingEscalatedChats');

     Route::get('my-chat-admin-report-async', 'ReportController@myChatAdminReportAsync')->name('my-chat-admin-report-async.myChatAdminReportAsync');

    Route::get('my-chat-admin-user-waiting', 'ReportController@myChatAdminReportUserWaiting')->name('my-chat-admin-user-waiting.myChatAdminReportUserWaiting');

    Route::get('my-chat-admin-user-waiting-check-db', 'ReportController@myChatAdminReportUserWaitingCheckBd')->name('my-chat-admin-user-waiting-check-db.myChatAdminReportUserWaitingCheckBd');

    Route::get('counselor-Live-chat-list', 'ReportController@counselorLiveChatList')->name('counselor-Live-chat-list.counselorLiveChatList');

    Route::get('counselor-async-chat-list', 'ReportController@counselorAsyncChatList')->name('counselor-Live-chat-list.counselorAsyncChatList');

    Route::get('counselor-live-chat-progress-list', 'ReportController@counselorLiveChatPorgressList')->name('counselor-live-chat-progress-list.counselorLiveChatPorgressList');

    Route::get('users-waiting-list', 'ReportController@usersWaitingList')->name('users-waiting-list.usersWaitingList');
    
    Route::get('users-waiting-list-count', 'ReportController@usersWaitingListCountCheck')->name('users-waiting-list-count.usersWaitingListCountCheck');

    Route::get('live-curent-chat-list-admin', 'ReportController@liveCurentChatListAdmin')->name('live-curent-chat-list-admin.liveCurentChatListAdmin');

    Route::get('live-curent-chat-list-admin-check-db', 'ReportController@liveCurentChatListAdminCheckDb')->name('live-curent-chat-list-admin-check-db.liveCurentChatListAdminCheckDb');



     // past chat report 
     Route::get('past-chat-report', 'ReportController@pastChatAdminReport')->name('past-chat-report.pastChatAdminReport');
 
    // past chat report 
    Route::get('past-chat-counselor-report', 'ReportController@pastChatCounselorReport')->name('past-chat-report.pastChatCounselorReport');

    Route::get('past-chat-counselor-report-async', 'ReportController@pastChatCounselorReportAsync')->name('past-chat-report.pastChatCounselorReportAsync');

    Route::get('live-Chat-History-List', 'ReportController@liveChatHistoryList')->name('live-Chat-History-List.liveChatHistoryList');

    Route::get('live-Escalated-close', 'ReportController@liveEscalatedclose')->name('live-Escalated-close.liveEscalatedclose');
   
    //privacy policy
    Route::delete('privacypolicys/destroy', 'PrivacyPolicyController@massDestroy')->name('privacypolicys.massDestroy');
    Route::post('privacypolicys/media', 'PrivacyPolicyController@storeMedia')->name('privacypolicys.storeMedia');
    Route::resource('privacypolicys', 'PrivacyPolicyController');

     // push notification 
     Route::get('/push-notificaiton', 'NotificationController@index')->name('push-notificaiton');
     Route::post('/store-token', 'NotificationController@storeToken')->name('store.token');
     Route::post('/send-notification', 'NotificationController@sendNotification')->name('send.notification');

});
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
// Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
    }
});

Route::middleware([AuthGates::class])->group(function(){
    //feq
    Route::post('feq_store',[FeqController::class,'add_feq'])->name('feq_store');
    Route::put('feq_update/{id}',[FeqController::class,'update'])->name('feq_update');
    Route::get('feq',[FeqController::class,'index'])->name('feq');
    Route::get('feq_delete/{id}',[FeqController::class,'delete'])->name('feq_delete');
    
    //admin_to_user_notofication
    Route::get('/admin-to-user',[Admin_to_user_notificationController::class,'index'])->name('admin-to-user');
    Route::post('add-notification',[Admin_to_user_notificationController::class,'store'])->name('add-notification');

    //troopers_togtherController
    Route::get('troopers_togther_index',[Troopers_togtherController::class,'index'])->name('troopers_togther_index');
    Route::get('troopers_togther_view',[Troopers_togtherController::class,'view'])->name('troopers_togther_view');
    Route::post('store',[Troopers_togtherController::class,'store'])->name('store');
    Route::get('edit/{id}',[Troopers_togtherController::class,'edit'])->name('edit');
    Route::post('update/{id}',[Troopers_togtherController::class,'update'])->name('update');
    Route::get('delete/{id}',[Troopers_togtherController::class,'delete'])->name('delete');
    Route::get('delete/{id}',[Troopers_togtherController::class,'delete'])->name('delete');
    Route::get('history',[Troopers_togtherController::class,'history'])->name('history');
    
    });


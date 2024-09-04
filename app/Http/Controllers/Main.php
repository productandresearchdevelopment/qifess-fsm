<?php
/* https://www.phpencode.org */
namespace App\Http\Controllers;
use Hash;
use Validator;
use App\Libraries\FileUpload;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\SystemModels\Auth\Module;
use App\SystemModels\Globals\Upload;
use Symfony\Component\Process\Process;

class Main extends Controller
{
    public function index(Request $request){
        $user  = $request->user();
        $tpl = config(isMobile() ? 'site.mobile-template' : 'site.template');
        $var = [
            'user' => $user,
            'tpl' => $tpl,
            'treeMenu' => Module::tree($user->role_id)
        ];
        return view($tpl.'.index', $var);
    }

    public function getProfile(Request $request){
        $user = $request->user();
        return $user;
    }

    public function profileEdit(Request $request){
        $user  = $request->user();
        $input = ['name' => $request->name, 'email' => $request->email, 'phone' => $request->phone];
        $valid = Validator::make($input, [
            'phone' => 'numeric|nullable',
            'email' => "required|email|unique:auth_user,email,$user->id,id",
        ]);
        if($valid->fails()) return ['success'=>false, 'message'=> $valid->errors()->first()];
        else{
            $user->update($input);
            $this->uploadPhoto($request);
            return ['success'=>true, 'message'=> $user];
        }
    }

    public function profilePassword(Request $request){
        $user = $request->user();
        if($request->old && $request->new && $request->confirm){
            if($request->new == $request->confirm){
                if(Hash::check($request->old, $user->password)){
                    $user->update(['password' => Hash::make($request->new)]);
                    return ['success'=>true, 'message'=> 'Success!'];
                }
                return ['success'=>false, 'message'=> 'Please check your old password'];
            }
            return ['success'=>false, 'message'=> 'Please check your confirm password'];
        }
        return ['success'=>false, 'message'=> 'Please check your password'];
    }

    public function uploadPhoto(Request $request){
        $user = $request->user();
        if($upload = FileUpload::upload('photo', 'user-profile')){
            $user->update(['photo' => $upload]);
            return ['success'=>true, 'message'=> 'Success!'];
        }
        return ['success'=>false, 'message'=> 'File Not Found'];
    }

    public function fileUpload(Request $request, $id=null){
        if($id && $file = Upload::find($id)) {
            $path = Storage::disk('public_uploads')->url($file->filename);
            return redirect($path);
        }
        return abort(404);
    }

    public function deploy(Request $request)
    {
        if($token = $request->header('X-Gitlab-Token')){
            if($token == config('app.deploy_secret')){
                if(isset($request->ref)){
                    if($request->ref == 'refs/heads/'.config('app.deploy_branch')){
                        $root_path = base_path();
                        $process = new Process('cd ' . $root_path . '; ./deploy.sh');
                        $process->run(function ($type, $buffer) {
                            echo $buffer;
                        });
                        return 'Success...';
                    }
                    return 'No Access Branch ('.$request->ref.')';
                }
                return 'Unknown payload repository';
            }
            return 'Invalid Token';
        }
        return 'Unknown Header Token';
    }

    public static function deployRoutes(){
        $prefix = '\App\Http\Controllers';
        Route::post('/deploy', $prefix. '\Main@deploy');
    }

    public static function routes(){
        $main = (new static);
        $main->authRoutes();
        $main->mainRoutes();
        $main->systemRoutes();
    }

    public function authRoutes(){
        $prefix = '\App\Http\Controllers\Auth';
        Route::get('login', $prefix.'\LoginController@showLoginForm')->name('login');
        Route::post('login', $prefix.'\LoginController@login');
        Route::post('logout', $prefix.'\LoginController@logout')->name('logout');

        if ($options['reset'] ?? true) {
            Route::get('password/reset', $prefix.'\ForgotPasswordController@showLinkRequestForm')->name('password.request');
            Route::post('password/email', $prefix.'\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
            Route::get('password/reset/{token}', $prefix.'\ResetPasswordController@showResetForm')->name('password.reset');
            Route::post('password/reset', $prefix.'\ResetPasswordController@reset')->name('password.update');
        }
    }

    public function mainRoutes(){
        $prefix = '\App\Http\Controllers';
        Route::get('/file/{id?}', $prefix. '\Main@fileUpload')->name('upload.file');
        Route::group(['middleware' => 'auth'], function() use ($prefix){
            Route::get('/', $prefix. '\Main@index')->name('main');
            Route::get('/home', $prefix. '\Main@index')->name('home');
            Route::group(['prefix' => 'profile'], function() use ($prefix){
                Route::get('/data', $prefix. '\Main@getProfile')->name('profile.data');
                Route::post('/edit', $prefix. '\Main@profileEdit')->name('profile.edit');
                Route::post('/password', $prefix. '\Main@profilePassword')->name('profile.password');
                Route::post('/upload/photo', $prefix. '\Main@uploadPhoto')->name('profile.upload');
            });
        });
    }

    public function systemRoutes(){
        $prefix = '\App\Http\Controllers\Systems';
        Route::group(['middleware' => ['auth','roles'] ], function() use ($prefix) {
            Route::group(['prefix' => 'sys'], function () use ($prefix) {
                // USER  ------------------------------------------------------------------------------
                Route::group(['prefix' => 'user'], function () use ($prefix) {
                    Route::get('/', $prefix . '\User@index')->name('auth.user');
                    Route::get('/data', $prefix . '\User@data')->name('auth.user.data');
                    Route::post('/push/{id?}', $prefix . '\User@push')->name('auth.user.push');
                    Route::put('/set/role', $prefix . '\User@setRole')->name('auth.user.set.role');
                    Route::put('/set/password', $prefix . '\User@setPassword')->name('auth.user.set.password');
                    Route::put('/restore', $prefix . '\User@restore')->name('auth.user.restore');
                    Route::delete('/delete', $prefix . '\User@delete')->name('auth.user.delete');
                    Route::delete('/forcedelete', $prefix . '\User@forceDelete')->name('auth.user.forcedelete');
                });

                // MODULE  --------------------------------------------------------------------------------------------
                Route::group(['prefix' => 'module'], function () use ($prefix) {
                    Route::get('/', $prefix . '\Module@index')->name('auth.module');
                    Route::get('/data', $prefix . '\Module@data')->name('auth.module.data');
                    Route::post('/create', $prefix . '\Module@create')->name('auth.module.create');
                    Route::put('/update/{id}', $prefix . '\Module@update')->name('auth.module.update');
                    Route::delete('/delete', $prefix . '\Module@delete')->name('auth.module.delete');
                    Route::put('/set/roles/{id}', $prefix . '\Module@updateRoles')->name('auth.module.update.roles');
                    Route::put('/set/field/{field}', $prefix . '\Module@updateField')->name('auth.module.update.field');
                    Route::put('/move/{mode}', $prefix . '\Module@move')->name('auth.module.move');
                });

                // ROLES  --------------------------------------------------------------------------------------------
                Route::group(['prefix' => 'role'], function () use ($prefix) {
                    Route::get('/', $prefix . '\Role@index')->name('auth.role');
                    Route::get('/data', $prefix . '\Role@dataRoles')->name('auth.role.data');
                    Route::get('/data/modules/{role}', $prefix . '\Role@dataModules')->name('auth.role.data.module');
                    Route::post('/create', $prefix . '\Role@create')->name('auth.role.create');
                    Route::put('/update/{id}', $prefix . '\Role@update')->name('auth.role.update');
                    Route::delete('/delete/{id}', $prefix . '\Role@delete')->name('auth.role.delete');
                    Route::put('/set/home/{role}', $prefix . '\Role@setHome')->name('auth.role.set.home');
                    Route::put('/set/module/{role}', $prefix . '\Role@setAuth')->name('auth.role.set.auth');
                });
            });
        });
    }
}

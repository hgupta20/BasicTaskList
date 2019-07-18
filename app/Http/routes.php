<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

use App\Task;
use Illuminate\Http\Request;

Route::group(['middleware' => ['web']], function () {
    /**
     * Show Task Dashboard
     */
    Route::get('/', function () {
        return view('tasks', [
            'tasks' => Task::orderBy('created_at', 'asc')->get()
        ]);
    });

    /**
     * Add New Task
     */
    Route::post('/task', function (Request $request) {
        // custom validator called startswith
        Validator::extend('startswith', function( $attribute, $value, $parameters ) {
        return substr( $value, 0, strlen( $parameters[0] ) ) == $parameters[0];
        });
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);
        $validator2 = Validator::make($request->all(), [
            'name' => '|startswith:P',
        ]);

        if ($validator->fails()) {
            return redirect('/')
                ->withInput()
                ->withErrors($validator);
        }
        if (!$validator2->fails()) {
            return redirect('/')
                ->withInput()
                ->withMessage()
                ->withErrors($validator2);
        }

        $task = new Task;
        $task->name = $request->name;
        $task->save();

        return redirect('/');
    });

    /**
     * Delete Task
     */
    Route::delete('/task/{id}', function ($id) {
        Task::findOrFail($id)->delete();

        return redirect('/');
    });
});

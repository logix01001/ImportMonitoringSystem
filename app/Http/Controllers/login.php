<?php

namespace App\Http\Controllers;

use Agent;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Session;

class login extends Controller
{
    //

    public function login()
    {
		$IE_Detected = null;
        if (Agent::browser() == 'IE') {
            $IE_Detected = 'Please use a newer Browser like firefox or Chrome for better usage of the IMS.';
        }

        if (Session::get('employee_number')) {
            return Redirect::to('/');
        }

        return view('pages.login', compact('IE_Detected'));
    }
    public function logout()
    {
        Session::flush();
        return Redirect::to('login');
    }

    public function change_password(Request $request)
    {
        User::where('employee_number', Session::get('employee_number'))
            ->update([
                'password' => md5(Input::get('newpassword')),
            ]);

        $request->session()->put('password', md5(Input::get('newpassword')));
    }

    public function dologin()
    {
        // validate the info, create rules for the inputs

        $rules = array(
            'username' => 'required', // make sure the email is an actual email
            'password' => 'required', // password can only be alphanumeric and has to be greater than 3 characters
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Input::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::to('login')
                ->withErrors($validator) // send back all errors to the login form
                ->withInput(Input::except('password')); // send back the input (not the password) so that we can repopulate the form
        } else {

            // $client = new PHPOnCouch\CouchClient(
            // 'http://10.168.64.31:5984',
            // 'hrd_masteruser'
            // );

            // $query = [
            //     '$and' => [
            //         ['employee_code' =>  Input::get('username')],
            //         ['password' => md5( Input::get('password'))]
            //     ]
            // ];
            // $doc = $client->find($query);

            $doc = User::where('employee_number', Input::get('username'))
                ->where('password', md5(Input::get('password')))
                ->get();

            if (count($doc) > 0) {
                $userdata = array(
                    'employee_number' => $doc[0]->employee_number,
                    'employee_name' => $doc[0]->employee_name,
                    'password' => $doc[0]->password,
                    'maintenance' => $doc[0]->maintenance,
                    'master' => $doc[0]->master,
                    'encoding' => $doc[0]->encoding,
                    'arrival' => $doc[0]->arrival,
                    'e2m' => $doc[0]->e2m,
                    'current_status' => $doc[0]->current_status,
                    'gatepass' => $doc[0]->gatepass,
                    'storage_validity' => $doc[0]->storage_validity,
                    'container_movement' => $doc[0]->container_movement,
                    'safe_keep' => $doc[0]->safe_keep,

                );

                Session::put($userdata);
                return Redirect::to('/');
            } else {

                return Redirect::to('login')
                    ->with(['loginIncorrect' => 'Incorrect Username / Password']); // send back all errors to the login form
            }

        }
    }
}

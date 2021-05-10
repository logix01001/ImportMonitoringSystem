<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Filesystem\Filesystem;
use JavaScript;
use App\User;
use App\Holiday;

use Session;
use Storage;
use Agent;



class homeController extends Controller
{
    //
    public function __construct(){

        $this->middleware('updateUserPrivilege');

    }


    public function index(){

        return view('pages.home');
    }

    public function maintenance_user(){

        $users = User::get();

        JavaScript::put([
            "users" => $users
        ]);
        return view('pages.maintenance_user');
    }

    public function maintenance_holiday(){

        $holidays = Holiday::orderBy('holiday_date','ASC')->get();

        JavaScript::put([
            "holidays" => $holidays
        ]);
        return view('pages.maintenance_holiday');
    }

    public function maintenance_uploadedcsv($dir = null){

        $directories = Storage::directories();

        if($dir == null){
            $dir = date('Y-m-d');
        }

        if (!Storage::disk('local')->has($dir)) {
            $dir = date('Y-m-d');
        }



        $files = Storage::files($dir);

        JavaScript::put([
            "files" => $files,
            "directories" => $directories,
            "dateToday" => $dir
        ]);
        return view('pages.maintenance_uploadedcsv',compact('files'));

    }

    public function deleteUploadedCSVDirectory($dateFolder = '',$all = 'false'){
        if($all != 'false'){
            $dirs = Storage::directories();

            foreach ($dirs as $dir) {
                Storage::deleteDirectory($dir);
            }
        }else{
            Storage::deleteDirectory($dateFolder);
            return 'Ok';
        }


    }

    public function downloadUploadedCSV($directory= '' , $filename = ''){

         $file_path = storage_path() . '/imscsvimport/' .$directory.'/'. $filename;
        $headers = array(
            'Content-Type: csv',
            'Content-Disposition: attachment; filename='.$filename,
        );

        if ( file_exists( $file_path ) ) {
            // Send Download
            return \Response::download( $file_path, $filename, $headers );
        } else {
            // Error
            exit( 'Requested file does not exist on our server!' );
        }
    }


    public function download( $filename = '' )
    {
        // Check if file exists in app/storage/file folder
        $file_path = storage_path() . '/downloads/' . $filename;
        $headers = array(
            'Content-Type: csv',
            'Content-Disposition: attachment; filename='.$filename,
        );

        if ( file_exists( $file_path ) ) {
            // Send Download
            return \Response::download( $file_path, $filename, $headers );
        } else {
            // Error
            exit( 'Requested file does not exist on our server!' );
        }
    }
}

@extends('layout.index2')

@section('body')
    @if (Session::has('errorMessage'))

    <div class=" text-center animated fadeInDown">
        <h1>403</h1>
        <h3 class="font-bold">Access Denied</h3>

        <div class="error-desc">
            {{Session::get('errorMessage')}}
        </div>
    </div>
        
       
    @endif

@endsection

@section('vuejsscript')

@endsection


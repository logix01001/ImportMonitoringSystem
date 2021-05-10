
@extends('layout.index2')

@section('body')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Unload / Returned Update</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                    {{--
                    @if (Session::get('storage_validity') == 1 && Session::get('container_movement') == 1 )
                        @component('components.storage_demurage_all')
                        @endcomponent
                    @else --}}

                    @component('components.unload_returned_update')
                    @endcomponent

                    {{-- @endif --}}
                    <div class="hr-line-dashed"></div>
                    <div class="row">
                        <div class="col-lg-2">

                            <label for=""> Number of record </label>

                            <select class="form-control" v-model="numberofTake" id="">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="30">30</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="200">200</option>
                                <option value="300">300</option>
                                <option value="ALL"> --- All --- </option>
                            </select>

                        </div>
                        <div class="col-lg-10">

                                <button :disabled="showprogress" class="btn btn-primary btn-block m-t" @click="getRecord_Unload(numberofTake)"><i
                                    class="fa fa-arrow-down"></i> Show More</button>
                        </div>
                    </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('vuejsscript')
    <script src="{{asset('/js/vuejs/unload_returned.js')}}"></script>
@endsection


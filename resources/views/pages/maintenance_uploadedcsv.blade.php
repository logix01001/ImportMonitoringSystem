@extends('layout.index2')

@section('body')

<div class="row">
    
    <div class="col-lg-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>List of Files</h5>
            </div>
           
            <div class="ibox-content">
                   <select :disabled="directories.length == 0" class="form-control" @change="changeDIR" v-model="selected_dir">
                        <option 
                            :value="dir" 
                            v-for="dir in directories">@{{ dir }}</option>
                   </select>
                   <Br>
                    @if (count($files) > 0)
                        <button class="btn btn-warning" @click="deleteDirectory(dateToday)"> Delete This Directory</button>
                        <button class="btn btn-danger" @click="deleteDirectory(dateToday,true)"> Delete All</button>
                    @endif
                   <hr>
                    <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>File Name</th>
                                    <th>Uploaded By</th>
                                    <th>Type</th>  
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($files) > 0)
                                    @foreach ($files as $file)
                                    <tr>
                                        <td><a href="{{ route('maintenance.maintenance_uploadedcsv_download',[$file]) }}">{{explode("-", $file)[5]}}</a></td>
                                        <td>{{  explode("-", $file)[3]}}</td>
                                        <td>{{  explode("-", $file)[4]}}</td> 
                                        
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                       <td colspan="3"> No Files Found.</td>
                                    </tr>
                                @endif
                               
                              
                            </tbody>
                          </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vuejsscript')
<script>
new Vue({
    el: '.vue_app_element',
    data: {
        directories: directories,
        dateToday : dateToday,
        selected_dir: dateToday,
    },
    methods:{
        deleteDirectory(dir,all = false){

            $.confirm({
                title: 'Delete!',
                content: 'Are you sure you want to delete this directories?',
                buttons: {
                    confirm: function () {

                        axios.get(`http://${window.location.host}/${myBaseName}maintenance_deleteUploadedCSVDirectory/${dir}/${all}`,
                        {
                           
            
                        }).then((response)=>{
                         
                                $.alert({
                                    title: 'Deleted!',
                                    content: 'Deleted Directory',
                                });
                            
                                window.location.replace(`http://${window.location.host}/${myBaseName}maintenance_uploadedcsv/`);
                        })

                    },
                    cancel: {
                        text: 'cancel',
                        btnClass: 'btn-warning'
                       
                    }
                }
            });

        },
        changeDIR(){
            window.location.replace(`http://${window.location.host}/${myBaseName}maintenance_uploadedcsv/${this.selected_dir}`);
        }

    }
})
</script>
{{-- <script src="{{asset('/js/vuejs/maintenance_holiday.js')}}"></script> --}}
@endsection


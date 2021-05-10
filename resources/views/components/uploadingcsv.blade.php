
<div class="wrapper  animated fadeInLeft" style="margin: 50px">
<h1 class=" animated fadeInLeft">{{ @$import_for }}</h1>
    <div class="hr-line-dashed"></div>  
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>{{$title}}
                    <small style="color:red">Please DELETE HEADERS BEFORE IMPORTING!. </small>
            </h5>
        </div>
        <div class="ibox-content">
            <div class="row">
                <div class="col-lg-10">
                        <form action="{{$action}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('POST')
                           
                            <p>Select attachment..</p>
                            <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="csvMaterial">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                            <button type="submit" class="btn {{@$color}} btn-outline dim btn-block"><i class="fa fa-upload"></i>Upload</button>
                        </form>
                </div>
                <div class="col-lg-2">
                    <center>
                            <a class="btn {{@$color}} dim  btn-outline" href={{ $downloadable }} >
                        <i class="fa fa-file-excel fa-5x"></i><br>
                       CSV TEMPLATE HEADER 
                    </a>
                        <hr>
                        <small>Update {{@$file_updated_date}}</small>
                    </center>  
                </div>
            </div>
           
              
        </div>
    </div>
</div>











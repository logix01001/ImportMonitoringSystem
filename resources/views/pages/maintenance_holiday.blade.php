@extends('layout.index2')

@section('body')
<div class="row">
    <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Add new holiday </h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-down"></i>
                        </a>
                </div>
                <div class="ibox-content" style="display:none;">
                    <div class="row">
                        <div class="col-lg-3">
                            <h4>Holiday</h4>
                            <div class="hr-line-dashed"></div>
                            <input class="form-control" type="text" v-model="newHolidayObj.holiday_name">
                        </div>
                        <div class="col-lg-3">
                            <h4>Holiday Date</h4>
                            <div class="hr-line-dashed"></div>
                            <input class="form-control" type="text" id="newHolidayDate" v-model="newHolidayObj.holiday_date">
                        </div>
                        <div class="col-lg-3">
                            <h4>Week Day</h4>
                            <div class="hr-line-dashed"></div>
                            <input disabled class="form-control"   type="text" v-model="newHolidayObj.holiday_day">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="row">
                        <button class="btn btn-primary" @click="saveObj(newHolidayObj,'NEW')">
                            Add Holiday
                        </button>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>List of holidays</h5>
            </div>
            <div class="ibox-content">
                    <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Holiday Name</th>
                                    <th>Holiday Date</th>
                                    <th>Week Day</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="holiday in holidays">
                                    <td>@{{holiday.holiday_name}}</td>
                                    <td>@{{holiday.holiday_date}}</td>
                                    <td>@{{holiday.holiday_day}}</td>
                                    <td>
                                        <button type="button" @click="editHoliday(holiday.id)" class="btn btn-primary btn-outline" data-toggle="modal" data-target="#myModal4">
                                                <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline btn-danger" @click="deleteUser(holiday.id)"> <i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                          </table>
            </div>
        </div>
    </div>
</div>


<div class="modal inmodal" id="myModal4" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated fadeIn">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <i class="fa fa-calendar modal-icon"></i>
                <h4 class="modal-title">Imap Holiday</h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="form-group"><label class="col-lg-2 control-label">Holiday Name</label>
                        <div class="col-lg-10">
                            <input type="text" placeholder="Holiday Date" class="form-control" v-model="editHolidayObj.holiday_name">
                        </div>
                    </div>
                    <div class="form-group"><label class="col-lg-2 control-label">Holiday Date</label>

                        <div class="col-lg-10"><input type="text" id="editHolidayDate" placeholder="Holiday Date" :value="editHolidayObj.holiday_date" v-model="editHolidayObj.holiday_date" class="form-control"></div>
                    </div>
                    <div class="form-group"><label class="col-lg-2 control-label">Week Day</label>
                        <div class="col-lg-10"><input type="text"  disabled placeholder="Week Day" v-model="editHolidayObj.holiday_day" class="form-control"></div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button @click="saveObj(editHolidayObj,'EDIT')" class="btn btn-sm btn-success btn-outline dim btn-block">Update</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
               
            </div>
        </div>
    </div>
</div>
@endsection

@section('vuejsscript')
<script src="{{asset('/js/vuejs/maintenance_holiday.js')}}"></script>
@endsection


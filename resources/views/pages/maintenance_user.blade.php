@extends('layout.index2')

@section('body')
<div class="row">
    <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Add new user </h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-down"></i>
                        </a>
                </div>
                <div class="ibox-content" style="display:none;">
                    <div class="row">
                          
                        <div class="col-lg-3 " :class="existUser ? 'has-error': ''">
                            <h4 :class="existUser ? 'control-label': ''">Employee Number</h4>
                            <div class="hr-line-dashed"></div>
                            <input class="form-control" type="text" v-model="newUser.employee_number">
                            <span v-if="existUser" class="control-label">User # is already Exist</span>
                        </div>
                        <div class="col-lg-3">
                            <h4>Employee Name</h4>
                            <div class="hr-line-dashed"></div>
                            <input class="form-control" type="text" v-model="newUser.employee_name">
                        </div>
                        <div class="col-lg-3">
                            <h4>Employee Password</h4>
                            <div class="hr-line-dashed"></div>
                            <input class="form-control" type="password" v-model="newUser.password">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-horizontal">
                                <h4>User privilege's</h4> 
                                    <table class="table table-bordered table-bordered-dark ">
                                        <thead>
                                            <th>Master</th>
                                            <th>Maintenance</th>
                                            <th>Encoding</th>
                                            <th>Arrival</th>
                                            <th>E2M</th>
                                            <th>Current Status</th>
                                            <th>Gatepass</th>
                                            <th>Validity</th>
                                            <th>Container Movement</th>
                                            <th>Safe keep</th>
                                        </thead>
                                        <tbody>
                                            <td><input type="checkbox" v-model="newUser.master" class="js-switch" /> </td>
                                            <td><input type="checkbox" v-model="newUser.maintenance" class="js-switch"  /> </td>
                                            <td><input type="checkbox" v-model="newUser.encoding" class="js-switch"  /> </td>
                                            <td><input type="checkbox" v-model="newUser.arrival" class="js-switch"  /> </td>
                                            <td><input type="checkbox" v-model="newUser.e2m" class="js-switch"  /> </td>
                                            <td><input type="checkbox" v-model="newUser.current_status" class="js-switch"  /> </td>
                                            <td><input type="checkbox" v-model="newUser.gatepass" class="js-switch"  /> </td>
                                            <td><input type="checkbox" v-model="newUser.storage_validity" class="js-switch"  /> </td>
                                            <td><input type="checkbox" v-model="newUser.container_movement" class="js-switch"  /> </td>
                                            <td><input type="checkbox" v-model="newUser.safe_keep" class="js-switch"  /> </td>
                                        </tbody>
                                    </table>
                                  
                                </div>
                            </div>
                        </div>
                    <div class="hr-line-dashed"></div>
                    <div class="row">
                        <button class="btn btn-primary" @click="saveObj(newUser,'NEW')">
                            Add user
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
                <h5>List of users</h5>
            </div>
            <div class="ibox-content">
                    <table class="table table-responsive tableuser">
                            <thead>
                                <tr>
                                    <th>Employee #</th>
                                    <th>Employee Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users">
                                    <td>@{{user.employee_number}}</td>
                                    <td>@{{user.employee_name}}</td>
                                    <td>
                                        <button type="button" @click="geteditUser(user.id)" class="btn btn-primary btn-outline" data-toggle="modal" data-target="#myModal4">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline btn-danger" @click="deleteUser(user.id)"> <i class="fa fa-trash"></i></button>
                                    </td>
                                    
                            </tbody>
                          </table>
            </div>
        </div>
    </div>
</div>
<div class="modal inmodal" id="myModal4" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog" style="width: 800px">
            <div class="modal-content animated fadeIn">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <i class="fa fa-user modal-icon"></i>
                    <h4 class="modal-title">Imap User Infomation</h4>
                </div>
                <div class="modal-body">
                  
                    <div class="hr-line-dashed"></div>
                    <table class="table table-bordered table-bordered-dark">
                        <thead>
                            <th>Master</th>
                            <th>Maintenance</th>
                            <th>Encoding</th>
                            <th>Arrival</th>
                            <th>E2M</th>
                            <th>Current Status</th>
                            <th>Gatepass</th>
                            <th>Validity</th>
                            <th>Container Movement</th>
                            <th>Safe keep</th>
                        </thead>
                        <tbody>
                            <td><input type="checkbox" v-model="editUser.master" /> </td>
                            <td><input type="checkbox" v-model="editUser.maintenance"   /> </td>
                            <td><input type="checkbox" v-model="editUser.encoding" /> </td>
                            <td><input type="checkbox" v-model="editUser.arrival"  /> </td>
                            <td><input type="checkbox" v-model="editUser.e2m" /> </td>
                            <td><input type="checkbox" v-model="editUser.current_status"  /> </td>
                            <td><input type="checkbox" v-model="editUser.gatepass"  /> </td>
                            <td><input type="checkbox" v-model="editUser.storage_validity"  /> </td>
                            <td><input type="checkbox" v-model="editUser.container_movement"   /> </td>
                            <td><input type="checkbox" v-model="editUser.safe_keep"  /> </td>
                        </tbody>
                    </table>
                    <div class="hr-line-dashed"></div>
                    <button type="button" class="btn btn-primary" @click="saveObj(editUser,'EDIT')">Update</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                  
                </div>
            </div>
        </div>
    </div>
@endsection

@section('vuejsscript')
<script src="{{asset('/js/vuejs/maintenance_user.js')}}"></script>
@endsection


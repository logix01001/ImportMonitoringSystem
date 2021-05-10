<nav class="navbar-default navbar-static-side"  role="navigation">
    <div class="sidebar-collapse vue-top-navbar" id="app_top_bar">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element animated fadeInLeft">
                    <img src="{{asset('img/logoims.png')}}" width="150" alt="" srcset="">
                </div>
                <div class="logo-element">
                    <img src="{{asset('img/miniims.png')}}" width="50" alt="" srcset="">
                </div>
            </li>
            <li>
                <a href="{{ route('importation.charts') }}">
                    <i class="fa fa-home"></i>
                    <span class="nav-label"> Home </span>
                </a>
            </li>

            @if (Session::get('employee_number'))
            <li>
                <a href="index.html"><i class="fa fa-truck"></i> <span class="nav-label">Importation</span> <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    @if (Session::get('encoding') == 1 )
                    <li>
                        <a href="{{route('importation.new')}}">
                            <i class="fa fa-plus"></i>
                            <span class="nav-label"> Add Record </span>
                        </a>
                    </li>
                    @endif
                    {{-- <li>
                        <a href="{{route('importation.daily_boc')}}">
                            <i class="fa fa-file"></i>
                            <span class="nav-label"> Daily Process to BOC </span>
                        </a>
                    </li> --}}
                    @if (Session::get('arrival') == 1  )
                    <li>
                        <a href="{{route('importation.arrival_update')}}">
                            <i class="fa fa-ship"></i>
                            <span class="nav-label"> Arrival Update </span>
                        </a>
                    </li>
                    @endif
                    @if (Session::get('e2m') == 1)
                    <li>
                        <a href="{{route('importation.documentation_process')}}">
                            <i class="fa fa-file"></i>
                            <span class="nav-label"> Docs Update </span>
                        </a>
                    </li>
                    @endif
                    @if (Session::get('gatepass') == 1 || Session::get('current_status') == 1 )
                    <li>
                        <a href="{{route('importation.sop')}}">
                            <i class="fa fa-ticket-alt"></i>
                            <span class="nav-label"> Shipment on proccess</span>
                        </a>
                    </li>
                    @endif
                    @if ( Session::get('storage_validity') == 1 )
                    <li>
                        <a href="{{route('importation.storage_validity')}}">
                            <i class="fa fa-shipping-fast"></i>
                            <span class="nav-label"> Containers for pull out </span>
                        </a>
                    </li>

                    @endif
                    @if ( Session::get('container_movement') == 1)
                    <li>
                        <a href="{{route('importation.unload_returned')}}">
                            <i class="fa fa-dolly"></i>
                            <span class="nav-label"> Unloaded / Returned Update </span>
                        </a>
                    </li>

                    @endif
                   <li>
                        <a href="{{route('importation.cleared_shipment')}}">
                            <i class="fa fa-clipboard-check"></i>
                            <span class="nav-label"> Cleared Shipment </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('importation.index')}}">
                            <i class="fa fa-search"></i>
                            <span class="nav-label"> Search Shipment </span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{route('importation.excel_import')}}">
                    <i class="fa fa-file-excel"></i>
                    <span class="nav-label"> Import from Excel</span>
                </a>
            </li>
            @else
            <li>
                <a href="{{route('importation.index')}}">
                    <i class="fa fa-search"></i>
                    <span class="nav-label"> Search Shipment </span>
                </a>
            </li>
            @endif
            @if (Session::get('maintenance') == 1 )
            <li>
                <a href="index.html"><i class="fa fa-cog"></i> <span class="nav-label">Maintenance</span> <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li> <a href="{{route('maintenance.user')}}">
                            <i class="fa fa-user"></i>
                            <span class="nav-label"> Users </span>
                        </a>
                    </li>
                    <li> <a href="{{ route('maintenance.holiday')}}">
                            <i class="fa fa-calendar"></i>
                            <span class="nav-label"> Holidays </span>
                        </a>
                    </li>
                    @if (Session::get('employee_number') == '28731' )
                        <li>
                            <a href="{{ route('maintenance.uploadedCSV')}}">
                                <i class="fa fa-file-excel"></i>
                                <span class="nav-label"> Uploaded CSV </span>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
            @endif
            <li>
                <a href="index.html"><i class="fa fa-chart-bar"></i> <span class="nav-label">Reports</span> <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    {{-- <li>
                        <a href="{{route('importation.breakdown')}}">
                            <i class="fa fa-chart-bar"></i>
                            <span class="nav-label"> Breakdown </span>
                        </a>
                    </li> --}}
                    <li>
                        <a href="{{route('importation.summary_tally')}}">
                            <i class="fa fa-chart-bar"></i>
                            <span class="nav-label"> Summary </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('importation.beyond_storage_free_time_per_day')}}">
                            <i class="fa fa-calendar-alt"></i>
                            <span class="nav-label"> Beyond free time per day </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('importation.beyond_storage_free_time_summary')}}">
                            <i class="fa fa-calendar-alt"></i>
                            <span class="nav-label"> Beyond free time summary </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('importation.incoming_vessels') }}">
                            <i class="fa fa-calendar-alt"></i>
                            <span class="nav-label"> Incoming Vessels </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('importation.onboard_shipment') }}">
                            <i class="fa fa-calendar-alt"></i>
                            <span class="nav-label"> Onboard Shipment </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('importation.vessel_waiting') }}">
                            <i class="fa fa-calendar-alt"></i>
                            <span class="nav-label"> Vessels Waiting </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('importation.notyet_discharge') }}">
                            <i class="fa fa-calendar-alt"></i>
                            <span class="nav-label"> Not yet discharge </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('importation.shipment_on_process_report') }}">
                            <i class="fa fa-calendar-alt"></i>
                            <span class="nav-label"> Shipment onprocess </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('importation.shipment_without_gatepass') }}">
                            <i class="fa fa-calendar-alt"></i>
                            <span class="nav-label"> Shipment without gatepass </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('importation.onhand_gatepass') }}">
                            <i class="fa fa-calendar-alt"></i>
                            <span class="nav-label"> On hand Gatepass </span>
                        </a>
                    </li>
                    {{-- <li>
                        <a href="{{ route('importation.dismounted_with_chassi') }}">
                            <i class="fa fa-calendar-alt"></i>
                            <span class="nav-label"> Dismounted and w/ chassi </span>
                        </a>
                    </li>   --}}
                    <li>
                        <a href="{{ route('importation.containers_irs') }}">
                            <i class="fa fa-calendar-alt"></i>
                            <span class="nav-label"> Containers in IRS </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('importation.containers_not_return') }}">
                            <i class="fa fa-calendar-alt"></i>
                            <span class="nav-label"> Containers not yet return </span>
                        </a>
                    </li>

                </ul>
            </li>
            <li>
                <a href="{{ route('importation.logistics_report') }}">
                    <i class="fa fa-calendar-alt"></i>
                    <span class="nav-label"> Logistics Report </span>
                </a>
            </li>
            <li>
                <a href="{{ route('importation.import_analysis') }}">
                    <i class="fa fa-chart-line"></i>
                    <span class="nav-label"> Import Analysis </span>
                </a>
            </li>
            <li>
                <a href="{{ route('importation.unload_analysis') }}">
                    <i class="fa fa-chart-line"></i>
                    <span class="nav-label"> Unload Analysis </span>
                </a>
            </li>
            <li>
                <a href="{{ route('importation.transport_schedule') }}">
                    <i class="fa fa-chart-line"></i>
                    <span class="nav-label"> Transport schedule </span>
                </a>
            </li>
            <li>
                <a href="{{ route('importation.discharge_gatepass_tally') }}">
                    <i class="fa fa-chart-line"></i>
                    <span class="nav-label"> Transport schedule Tally </span>
                </a>
            </li>
            @if (Session::get('employee_number'))
            <li>
                <a href="{{route('login.logout')}}">
                    <i class="fa fa-sign-out-alt"></i>
                    <span class="nav-label"> Log out </span>
                </a>
            </li>
            @else

            <li>
                <a href="{{route('login.index')}}">
                    <i class="fa fa-sign-in-alt"></i>
                    <span class="nav-label"> Log in </span>
                </a>
            </li>
            @endif

        </ul>
    </div>
</nav>

<div id="page-wrapper" class="gray-bg">
    <div class="row border-bottom">
        <nav class="navbar navbar-static-top  " role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
            </div>
            @if (Session::get('employee_number'))
            <ul class="nav navbar-top-links navbar-right">
                <li>
                    <span class="m-r-sm text-muted welcome-message">Welcome {{Session::get('employee_name')}}</span>
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                        <i class="fa fa-user"></i>
                        <i class="fa fa-cog"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-alerts">
                        <li>
                            <a href="#" data-toggle="modal" data-target="#myModal2">
                                <div>
                                    <i class="fa fa-key"></i> Change Password
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="">
                                <i class="fa fa-sign-out"></i> Log out
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
            @endif
            <div class="pull-right" style="margin: 10px; margin-right: 50px"></div>
        </nav>

    </div>
    <div class="modal inmodal" id="myModal2" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content animated flipInY">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only">Close</span></button>
                    <h4 class="modal-title"> <i class="fa fa-key"></i> Change Password </h4>
                </div>
                <div class="modal-body" id="change_password">

                    <div>
                        <div class="alert alert-success" v-if="updated">
                            Password updated..
                        </div>

                        <div class="form-group"><label>New Password</label> <input type="password" placeholder="New Password"
                                v-model="new_password" class="form-control"></div>
                        <div class="form-group"><label>Confirm Password</label> <input type="password" placeholder="Confirm Password"
                                v-model="confirm_password" class="form-control">
                            <span class="text-danger" v-if="(new_password != '' && confirm_password != '') && new_password != confirm_password">
                                <i class="fa fa-times-circle-o"></i> New and confirm is not match</span>
                            <span class="text-success" v-if="(new_password != '' && confirm_password != '') && new_password == confirm_password"><i
                                    class="fa fa-check-circle-o"></i> Password is match</span>
                        </div>
                        <div class="form-group"><label>Old Password</label> <input type="password" placeholder="Old Password"
                                v-model="old_password" @blur="check_password" class="form-control">
                            <span class="text-danger" v-if="(old_password != '') && check_password() == false "><i
                                    class="fa fa-times-circle-o"></i> Old Password is not Match</span>
                            <span class="text-success" v-if="(old_password != '') && complete == true "><i class="fa fa-check-circle-o"></i>
                                Old Password is Match</span>
                        </div>
                        <div class="form-group"> <button @click="changepassword" v-if="((new_password != '' && confirm_password != '') && new_password == confirm_password) && complete == true"
                                class="btn btn-primary btn-block">Update</button> </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@extends('layout.index2')

@section('body')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Add new BL record </h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">


                <fieldset>
                    <legend>Bill of Lading</legend>
                    
                    <div class="row">
                        <div  class="col-lg-3" v-bind:class="feedback(newBOL.bl_no)">
                            <label for="BL" class="control-label">BL#</label>
                            <div class="hr-line-dashed"></div>
                            <input type="text"
                            :value="newBOL.bl_no.toUpperCase()" @input="newBOL.bl_no = $event.target.value.toUpperCase()"
                            class="form-control" v-model="newBOL.bl_no">
                        </div>
                        <div class="col-lg-3" v-bind:class="feedback(newBOL.invoice_no)">
                            <label for="BL" class="control-label">Invoice Number# <small>(Use Comma(,) to separate invoice #)</small></label>
                            <div class="hr-line-dashed"></div>
                            <input type="text"
                            :value="newBOL.invoice_no.toUpperCase()" @input="newBOL.invoice_no = $event.target.value.toUpperCase()"
                            class="form-control" v-model="newBOL.invoice_no">
                        </div>
                        <div class="col-lg-3"  v-bind:class="feedback(newBOL.factory)">
                                <label for="BL" class="control-label">Factory</label>
                                <div class="hr-line-dashed"></div>
                                <select id="bl_factory" class="form-control" v-model="newBOL.factory">
                                    <option value=""></option>
                                    <option :value="factory.factory_id" v-for="factory in factories">@{{factory.factory_id}}</option>
                                </select>
                            </div>
                        <div class="col-lg-3" v-bind:class="feedback(newBOL.supplier)">
                            <label for="BL" class="control-label">Supplier</label>
                            <div class="hr-line-dashed"></div>
                            <select id="bl_supplier" class="form-control" v-model="newBOL.supplier">
                                <option value=""></option>
                            <option :value="supplier.supplier" v-for="supplier in suppliers">@{{supplier.supplier}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="row">
                            <div class="col-lg-3"  v-bind:class="feedback(newBOL.commodity)">
                                    <label for="BL" class="control-label">Commodity</label>
                                    <div class="hr-line-dashed"></div>
                                    <select id="bl_commodity" class="form-control" v-model="newBOL.commodity">
                                        <option value=""></option>
                                    <option :value="commodity.commodity" v-for="commodity in commodities">@{{commodity.commodity}}</option>
                                    </select>
                                </div>
                        <div class="col-lg-3" v-bind:class="feedback(newBOL.vessel)">
                            <label for="BL" class="control-label">Vessel</label>
                            <div class="hr-line-dashed"></div>
                            <select id="bl_vessel" class="form-control" v-model="newBOL.vessel">
                                <option value=""></option>
                            <option :value="vessel.vessel" v-for="vessel in vessels">@{{vessel.vessel}}</option>
                            </select>
                        </div>
                        <div class="col-lg-3" v-bind:class="feedback(newBOL.connecting_vessel)">
                            <label for="BL" class="control-label">Connecting Vessel</label>
                            <div class="hr-line-dashed"></div>
                            <select id="bl_connecting_vessel" class="form-control" v-model="newBOL.connecting_vessel">
                                <option value=""></option>
                                <option :value="cv.connecting_vessel" v-if="cv.connecting_vessel != null" v-for="cv in connecting_vessels">@{{cv.connecting_vessel}}</option>
                            </select>
                        </div>
                        <div class="col-lg-3" v-bind:class="feedback(newBOL.shipping_line)">
                            <label for="BL" class="control-label">Shipping Line</label>
                            <div class="hr-line-dashed"></div>
                            <select id="bl_shipping_Line" class="form-control" v-model="newBOL.shipping_line">
                                <option value=""></option>
                                <option :value="shipping_line.shipping_line" v-for="shipping_line in shipping_lines">@{{shipping_line.shipping_line}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="row">
                            <div class="col-lg-3" v-bind:class="feedback(newBOL.forwarder)">
                                <label for="BL" class="control-label" >Forwarder</label>
                                <div class="hr-line-dashed"></div>
                                <select id="bl_forwarder" class="form-control" v-model="newBOL.forwarder">
                                    <option value=""></option>
                                    <option :value="forwarder.forwarder" v-for="forwarder in forwarders">@{{forwarder.forwarder}}
                                    </option>
                                </select>
                            </div>
                            <div class="col-lg-3" v-bind:class="feedback(newBOL.broker)">
                                <label for="BL" class="control-label">Broker</label>
                                <div class="hr-line-dashed"></div>
                                <select id="bl_broker" class="form-control" v-model="newBOL.broker">
                                    <option value=""></option>
                                    <option :value="broker.broker" v-for="broker in brokers">@{{broker.broker}}</option>
                                </select>
                            </div>
                            <div class="col-lg-3" v-bind:class="feedback(newBOL.pol)">
                                <label for="BL" class="control-label">POL</label>
                                <div class="hr-line-dashed"></div>
                                <select id="bl_pol" class="form-control" v-model="newBOL.pol">
                                    <option value=""></option>
                                <option :value="pol.pol" v-for="pol in pol">@{{pol.pol}}</option>
                                </select>
                            </div>
                            <div class="col-lg-3" v-bind:class="feedback(newBOL.country)">
                                    <label for="BL"  class="control-label">Country</label>
                                    <div class="hr-line-dashed"></div>
                                    <select id="bl_country" class="form-control" v-model="newBOL.country">
                                        <option value=""></option>
                                    <option :value="country.country" v-for="country in countries">@{{country.country}}</option>
                                    </select>
                                </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="row">
                                <div class="col-lg-3" v-bind:class="feedback(newBOL.pod)">
                                    <label for="BL"  class="control-label">POD</label>
                                    <div class="hr-line-dashed"></div>
                                    <select id="bl_pod" class="form-control" v-model="newBOL.pod">
                                        <option value=""></option>
                                        <option value="SOUTH">SOUTH</option>
                                        <option value="NORTH">NORTH</option>
                                    </select>
                                </div>
                                <div class="col-lg-4" v-bind:class="feedback(newBOL.shipping_docs) || feedback(newBOL.shipping_docs_time)">
                                    <label for="BL" class="control-label">Shipping Document</label>
                                    <div class="hr-line-dashed"></div>
                                    <div class="col-lg-6">
                                            <input type="text" class="form-control" id="bl_shipping_docs_date" placeholder="Date" v-model="newBOL.shipping_docs">

                                    </div>
                                    <div class="col-lg-6" >
                                            <input type="text" class="form-control" placeholder="Time" v-model="newBOL.shipping_docs_time">
                                    </div>
                                    
                                </div>
                                <div class="col-lg-2" v-bind:class="feedback(newBOL.processing_date)">
                                    <label for="BL" class="control-label">Processing Date</label>
                                    <div class="hr-line-dashed"></div>
                                    <input type="text" class="form-control" id="bl_processing_date" v-model="newBOL.processing_date">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="row">
                                    <div class="col-lg-3">
                                        <label for="BL">Estimated time of Departure</label>
                                        <div class="hr-line-dashed"></div>
                                        <input type="text" class="form-control" id="bl_ETD" v-model="newBOL.estimated_time_departure">
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="BL">Estimated time of Arrival</label>
                                        <div class="hr-line-dashed"></div>
                                        <input type="text" class="form-control" id="bl_ETA" v-model="newBOL.estimated_time_arrival">
                                    </div>
                                    <div class="col-lg-3">
                                            <label for="BL">Volume</label>
                                            <div class="hr-line-dashed"></div>
                                            <input type="text" class="form-control" disabled v-model="newBOL.volume">
                                        </div>
                                </div>
                                
                </fieldset>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>List of container </h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">

                  

                    <div class="row">
                            <div class="col-lg-5">
                                <label for="BL"># Container</label>
                                <div class="hr-line-dashed"></div>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" v-model="number_container">
                                </div>
                                <div class="col-lg-4">
                                    <button class="btn btn-primary" @click="generateContainer()"> <i class="fa fa-plus"></i> Add </button>
                                </div>
                                
                            </div>
                                
                    </div>
                    <div class="alert alert-danger" v-if="list_container_required">
                            The container is empty.
                       </div>
                    <div class="row">
                            <div class="col-lg-12">
                                <div style="height:500px; overflow-y: scroll; ">
                                        <table id="container_new_table" class="table table-hover table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Container #</th>
                                                    <th>Container Type <br>
                                                       <span v-if="list_container.length > 0"> <small>Same?</small> <input type="checkbox" class="i-checks" v-model="sameContainer"> </span>
                                                    </th>
                                                    <th>QTY</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(container,i) in list_container">
                                                        <td>
                                                            @{{i + 1}}
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" placeholder="Container #" :value="container.container_number.toUpperCase()" @input="container.container_number = $event.target.value.toUpperCase()" v-model="container.container_number">
                                                        </td>
                                                        <td>
                                                                <select id="bl_pod" class="form-control" @change="check_same_container_type(container.container_type)" v-model="container.container_type">
                                                                        <option value=""></option>
                                                                        <option :value="type.name" v-for="type in container_types">@{{type.name}}</option>
                                                                    </select>
                                                                
                                                        </td>
                                                        <td>
                                                                <input type="text" class="form-control" placeholder="Container #"  v-model="container.quantity">     
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-danger btn-outline dim btn-outline" 
                                                                title="remove this row"
                                                                @click="remove_container_row(i)"
                                                            > 
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                </div>
                               
                            </div>
                                
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="row">
                        <button class="btn btn-primary btn-block" @click="saveObj"> <i class="fa fa-save"></i> Save</button>
                    </div>
                    
            </div>
        </div>
    </div>
</div>

    
@endsection

@section('vuejsscript')
    <script src="{{asset('/js/vuejs/import_new.js')}}"></script>
@endsection


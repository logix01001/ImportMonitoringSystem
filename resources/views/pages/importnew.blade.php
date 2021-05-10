@extends('layout.index2')

@section('body')
<div class="row">

    {{ session()->get('Message') }}
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Registration of Bill of Ladings </h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                    <div class="tabs-container">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#tab-3"> <i class="fa fa-plus fa-2x"></i></a> <center>Add BL</center></li>
                            <li class=""><a data-toggle="tab" href="#tab-4"><i class="fa fa-edit fa-2x"></i></a> <center>Edit BL</center></li>
                            <li class=""><a data-toggle="tab" href="#tab-5"><i class="fa fa-truck fa-2x"></i></a> <center>Split Container</center></li>
                            <li class=""><a data-toggle="tab" href="#tab-6"><i class="fa fa-exchange-alt  fa-2x"></i></a> <center>Change BL</center></li>
                            <li class=""><a data-toggle="tab" href="#tab-7"><i class="fa fa-info-circle  fa-2x"></i></a> <center>Split update</center></li>
                        </ul>
                        <div class="tab-content">
                            <div id="tab-3" class="tab-pane active">
                                <div class="panel-body">
                                     {{-- BEGIN OF BODY TAB 1 --}}
                                        <fieldset>
                                                <legend>Bill of Lading</legend>


                                                    <div class="row">
                                                            <div class="col-lg-6">

                                                                <div class="form-group" v-bind:class="feedback(newBOL.shipping_docs) || feedback(newBOL.shipping_docs_time)">
                                                                    <label class="col-sm-3 control-label">Shipping Receive Docs</label>

                                                                    <div class="col-sm-9">
                                                                        <input type="text" class="form-control" id="bl_shipping_docs_date" placeholder="Date" v-model="newBOL.shipping_docs">
                                                                        <br>
                                                                        <input type="text" class="form-control" placeholder="Time" v-model="newBOL.shipping_docs_time">
                                                                        <div class="hr-line-dashed"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group" v-bind:class="feedback(newBOL.incoterm)">
                                                                    <label class="col-sm-3 control-label">Incoterm</label>

                                                                    <div class="col-sm-9">
                                                                            <input type="text" class="form-control" id="bl_incoterm" v-model="newBOL.incoterm">
                                                                        <div class="hr-line-dashed"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group" v-bind:class="feedback(newBOL.processing_date)">
                                                                    <label class="col-sm-3 control-label">Processing Date</label>

                                                                    <div class="col-sm-9">
                                                                            <input type="text" class="form-control" id="bl_processing_date" v-model="newBOL.processing_date">
                                                                        <div class="hr-line-dashed"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group" v-bind:class="feedback(newBOL.bl_no)">
                                                                    <label class="col-sm-3 control-label">BL #</label>

                                                                    <div class="col-sm-9">
                                                                            <input type="text"
                                                                            @blur="searchExist"
                                                                            :value="newBOL.bl_no.toUpperCase()" @input="newBOL.bl_no = $event.target.value.toUpperCase()"
                                                                            class="form-control" v-model="newBOL.bl_no">
                                                                        <div class="hr-line-dashed"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group" v-bind:class="feedback(newBOL.factory)">
                                                                    <label class="col-sm-3 control-label">Factory</label>

                                                                    <div class="col-sm-9">
                                                                        <select id="bl_factory" class="form-control" v-model="newBOL.factory">
                                                                            <option value=""></option>
                                                                            <option :value="factory.factory_id" v-for="factory in factories">@{{factory.factory_id}}</option>
                                                                        </select>
                                                                        <div class="hr-line-dashed"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group" v-bind:class="feedback(newBOL.supplier)">
                                                                    <label class="col-sm-3 control-label">Supplier</label>

                                                                    <div class="col-sm-9">
                                                                        <select id="bl_supplier" class="form-control" v-model="newBOL.supplier">
                                                                            <option value=""></option>
                                                                            <option :value="supplier.supplier" v-for="supplier in suppliers">@{{supplier.supplier}}</option>
                                                                        </select>
                                                                        <div class="hr-line-dashed"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group" v-bind:class="feedback(newBOL.invoice_no)">
                                                                    <label class="col-sm-3 control-label">Invoice #</label>

                                                                    <div class="col-sm-9">
                                                                            <input type="text"
                                                                            :value="newBOL.invoice_no.toUpperCase()" @input="newBOL.invoice_no = $event.target.value.toUpperCase()"
                                                                            class="form-control" v-model="newBOL.invoice_no"
                                                                            placeholder="Use Comma(,) to separate invoice #">
                                                                        <div class="hr-line-dashed"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-sm-3 control-label">Commodity</label>

                                                                    <div class="col-sm-9">
                                                                        <select id="bl_commodity"  placeholder="Use Comma(,) to separate invoice #" class="form-control"  multiple="multiple" v-model="newBOL.commodity">
                                                                            <option value=""></option>
                                                                            <option :value="commodity.commodity" v-for="commodity in commodities">@{{commodity.commodity}}</option>
                                                                        </select>
                                                                        <div class="hr-line-dashed"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group" v-bind:class="feedback(newBOL.broker)">
                                                                    <label class="col-sm-3 control-label">Broker</label>

                                                                    <div class="col-sm-9">
                                                                        <select id="bl_broker" class="form-control" v-model="newBOL.broker">
                                                                            <option value=""></option>
                                                                            <option :value="broker.broker" v-for="broker in brokers">@{{broker.broker}}</option>
                                                                        </select>
                                                                        <div class="hr-line-dashed"></div>
                                                                    </div>
                                                                </div>
                                                        </div>
                                                        <div class="col-lg-6">

                                                            <div class="form-group" v-bind:class="feedback(newBOL.pol)">
                                                                <label class="col-sm-3 control-label">Port of Loading</label>

                                                                <div class="col-sm-9">
                                                                    <select id="bl_pol" class="form-control" v-model="newBOL.pol">
                                                                        <option value=""></option>
                                                                        <option :value="pol.pol" v-for="pol in pol">@{{pol.pol}}</option>
                                                                    </select>
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group" v-bind:class="feedback(newBOL.country)">
                                                                <label class="col-sm-3 control-label">Country</label>

                                                                <div class="col-sm-9">
                                                                    <select id="bl_country" class="form-control" v-model="newBOL.country">
                                                                        <option value=""></option>
                                                                        <option :value="country.country" v-for="country in countries">@{{country.country}}</option>
                                                                    </select>
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-sm-3 control-label">Estimated time of Departure</label>

                                                                <div class="col-sm-9">
                                                                    <input type="text" class="form-control" id="bl_ETD" v-model="newBOL.estimated_time_departure">
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group" v-bind:class="feedback(newBOL.shipping_line)">
                                                                <label class="col-sm-3 control-label">Shipping Line</label>

                                                                <div class="col-sm-9">
                                                                    <select id="bl_shipping_Line" class="form-control" v-model="newBOL.shipping_line">
                                                                        <option value=""></option>
                                                                        <option :value="shipping_line.shipping_line" v-for="shipping_line in shipping_lines">@{{shipping_line.shipping_line}}</option>
                                                                    </select>
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group" v-bind:class="feedback(newBOL.forwarder)">
                                                                <label class="col-sm-3 control-label">Forwarder</label>

                                                                <div class="col-sm-9">
                                                                    <select id="bl_forwarder" class="form-control" v-model="newBOL.forwarder">
                                                                        <option value=""></option>
                                                                        <option :value="forwarder.forwarder" v-for="forwarder in forwarders">@{{forwarder.forwarder}}</option>
                                                                    </select>
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group" v-bind:class="feedback(newBOL.vessel)">
                                                                <label class="col-sm-3 control-label">Vessel</label>

                                                                <div class="col-sm-9">
                                                                    <select id="bl_vessel" class="form-control" v-model="newBOL.vessel">
                                                                        <option value=""></option>
                                                                        <option :value="vessel.vessel" v-for="vessel in vessels">@{{vessel.vessel}}</option>
                                                                    </select>
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group" v-bind:class="feedback(newBOL.pod)">
                                                                <label class="col-sm-3 control-label">Port of Discharge</label>

                                                                <div class="col-sm-9">
                                                                    <select id="bl_pod" class="form-control" v-model="newBOL.pod">
                                                                        <option value=""></option>
                                                                        <option value="SOUTH">SOUTH</option>
                                                                        <option value="NORTH">NORTH</option>
                                                                    </select>
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-sm-3 control-label">Estimated time of Arrival</label>

                                                                <div class="col-sm-9">
                                                                        <input type="text" class="form-control" id="bl_ETA" v-model="newBOL.estimated_time_arrival">
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-sm-3 control-label">Volume</label>

                                                                <div class="col-sm-9">
                                                                    <input type="text" class="form-control" disabled v-model="newBOL.volume">
                                                                    <ul class="list-group">
                                                                        <li class="list-group-item" v-for="ctype in distinct_container_type" v-if="ctype.container_type != ''">
                                                                            <h3 style="color: #942911">@{{ctype.count + ' x   ' + ctype.container_type}}</h3>
                                                                        </li>
                                                                    </ul>
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </fieldset>
                                            <fieldset>
                                                <legend>List of Containers</legend>
                                                <div class="row">
                                                        <div class="col-lg-5">
                                                            <label for="BL"># Container</label>
                                                            <div class="hr-line-dashed"></div>
                                                            <div class="col-lg-8">
                                                                <input type="text" class="form-control" @keypress="isNumber" v-model="number_container">
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <button class="btn btn-primary" :disabled="number_container < 1 " @click="generateContainer()"> <i class="fa fa-plus"></i> Add </button>
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
                                                                        <th :style="(number_of_container_type_new) ? '' : {'color':'red'}">Container Type <br>
                                                                        <span v-if="list_container.length > 0"> <small>Same?</small> <input type="checkbox" class="i-checks" v-model="sameContainer"> </span>
                                                                        </th>
                                                                        <th>QTY</th>
                                                                        <th class="column200">Split</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>

                                                                        <tr v-for="(container,i) in list_container">
                                                                            <td>
                                                                                @{{i + 1}}
                                                                            </td>
                                                                            <td>

                                                                                <input type="text"
                                                                                :class="container.container_number.search('_') > -1 ? 'container_code_invalid_new' : ''"
                                                                                class="form-control container_register" placeholder="Container #"  :value="container.container_number.toUpperCase()"
                                                                                @input="container.container_number = $event.target.value.toUpperCase()"
                                                                                @keyup="container.container_number = $event.target.value.toUpperCase()"
                                                                                @blur="checkInvalidCodeNew()"

                                                                                v-model="container.container_number">

                                                                            </td>
                                                                            <td>
                                                                                <select id="bl_pod"  class="form-control" @change="check_same_container_type(container.container_type)" v-model="container.container_type">
                                                                                    <option value=""></option>
                                                                                    <option :value="type.name" v-for="type in container_types">@{{type.name}}</option>
                                                                                </select>

                                                                            </td>
                                                                            <td>
                                                                                <input type="text" class="form-control" placeholder="Container #"  v-model="container.quantity">
                                                                            </td>
                                                                            <td>
                                                                                {{-- <input type="text" :disabled="container.quantity > 0" class="form-control" placeholder="BL #"  v-model="container.split_bl_no_fk">      --}}

                                                                                <select class="form-control split_bl"  multiple="multiple" :data-index="i" v-model="container.split_bl_no_fk">
                                                                                    <option value=""></option>
                                                                                    <option :value="bl_no.bl_no" v-for="bl_no in list_of_bl_no_for_split">@{{bl_no.bl_no}}</option>
                                                                                </select>
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
                                                    <button :disabled="!newRecord || !number_of_container_type_new || container_code_invalid_new > 0" id="saveNewButton" class="btn btn-primary btn-block" @click="saveObj"> <i class="fa fa-save"></i> Save</button>
                                                </div>
                                            </fieldset>
                                    {{-- END OF BODY TAB 1 --}}
                                </div>
                            </div>
                            <div id="tab-4" class="tab-pane ">
                                <div class="panel-body">
                                    {{-- BEGIN OF BODY TAB 2 --}}
                                        <div v-if="firstLoad" class="alert alert-info alert-dismissable">
                                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                            Please Search BL #
                                        </div>
                                        <div v-if="!firstLoad && noRecord" class="alert alert-danger alert-dismissable">
                                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                            No result found. Please check your BL #
                                        </div>
                                        <fieldset>
                                            <legend>Edit Bill of Ladings</legend>

                                                <div class="row">
                                                    <div class="col-lg-6">
                                                            <div class="form-group" v-bind:class="feedback(editBOL.bl_no)">
                                                                <label class="col-sm-3 control-label">BL #</label>

                                                                <div class="col-sm-9">
                                                                        <input type="text"
                                                                            @blur="searchEdit"
                                                                            :value="editBOL.bl_no.toUpperCase()" @input="editBOL.bl_no = $event.target.value.toUpperCase()"
                                                                            class="form-control" v-model="editBOL.bl_no">
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                        <div v-show="!noRecord">
                                                            <div class="form-group" v-bind:class="feedback(editBOL.shipping_docs) || feedback(editBOL.shipping_docs_time)">
                                                                <label class="col-sm-3 control-label">Shipping Receive Docs</label>

                                                                <div class="col-sm-9">
                                                                    <input :disabled="noRecord" type="text" class="form-control" id="edit_bl_shipping_docs_date" placeholder="Date" v-model="editBOL.shipping_docs">
                                                                    <br>
                                                                    <input :disabled="noRecord" type="text" class="form-control" placeholder="Time" v-model="editBOL.shipping_docs_time">
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group" >
                                                                <label class="col-sm-3 control-label">Incoterm</label>

                                                                <div class="col-sm-9">
                                                                        <input type="text" class="form-control" v-model="editBOL.incoterm">
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group" v-bind:class="feedback(editBOL.processing_date)">
                                                                <label class="col-sm-3 control-label">Processing Date</label>

                                                                <div class="col-sm-9">
                                                                        <input :disabled="noRecord" type="text" class="form-control" id="edit_bl_processing_date" v-model="editBOL.processing_date">
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group" v-bind:class="feedback(editBOL.factory)">
                                                                <label class="col-sm-3 control-label">Factory</label>

                                                                <div class="col-sm-9">
                                                                    <select :disabled="noRecord" id="editBOL_bl_factory" class="form-control" v-model="editBOL.factory">
                                                                        <option value=""></option>
                                                                        <option :value="factory.factory_id" v-for="factory in factories">@{{factory.factory_id}}</option>
                                                                    </select>
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group" v-bind:class="feedback(editBOL.supplier)">
                                                                <label class="col-sm-3 control-label">Supplier</label>

                                                                <div class="col-sm-9">
                                                                    <select :disabled="noRecord" id="editBOL_bl_supplier" class="form-control" v-model="editBOL.supplier">
                                                                        <option value=""></option>
                                                                        <option :value="supplier.supplier" v-for="supplier in suppliers">@{{supplier.supplier}}</option>
                                                                    </select>
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group" v-bind:class="feedback(editBOL.invoice_no)">
                                                                <label class="col-sm-3 control-label">Invoice #</label>

                                                                <div class="col-sm-9">
                                                                        <input type="text"
                                                                        :disabled="noRecord"
                                                                        :value="editBOL.invoice_no.toUpperCase()" @input="editBOL.invoice_no = $event.target.value.toUpperCase()"
                                                                        class="form-control" v-model="editBOL.invoice_no"
                                                                        placeholder="Use Comma(,) to separate invoice #">
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-sm-3 control-label">Commodity</label>

                                                                <div class="col-sm-9">
                                                                    <select :disabled="noRecord" id="editBOL_bl_commodity"  placeholder="Use Comma(,) to separate invoice #" class="form-control"  multiple="multiple" v-model="editBOL.commodity">
                                                                        <option value=""></option>
                                                                        <option :value="commodity.commodity" v-for="commodity in commodities">@{{commodity.commodity}}</option>
                                                                    </select>
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group" v-bind:class="feedback(editBOL.broker)">
                                                                <label class="col-sm-3 control-label">Broker</label>
                                                                <div class="col-sm-9">
                                                                    <select :disabled="noRecord" id="editBOL_bl_broker" class="form-control" v-model="editBOL.broker">
                                                                        <option value=""></option>
                                                                        <option :value="broker.broker" v-for="broker in brokers">@{{broker.broker}}</option>
                                                                    </select>
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div v-show="!noRecord">
                                                            <div class="form-group" v-bind:class="feedback(editBOL.pol)">
                                                                <label class="col-sm-3 control-label">Port of Loading</label>

                                                                <div class="col-sm-9">
                                                                    <select :disabled="noRecord" id="editBOL_bl_pol" class="form-control" v-model="editBOL.pol">
                                                                        <option value=""></option>
                                                                        <option :value="pol.pol" v-for="pol in pol">@{{pol.pol}}</option>
                                                                    </select>
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group" v-bind:class="feedback(editBOL.country)">
                                                                <label class="col-sm-3 control-label">Country</label>

                                                                <div class="col-sm-9">
                                                                    <select :disabled="noRecord" id="editBOL_bl_country" class="form-control" v-model="editBOL.country">
                                                                        <option value=""></option>
                                                                        <option :value="country.country" v-for="country in countries">@{{country.country}}</option>
                                                                    </select>

                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-sm-3 control-label">Estimated time of Departure</label>

                                                                <div class="col-sm-9">
                                                                    <input :disabled="noRecord" type="text" class="form-control" id="editBOL_bl_ETD" v-model="editBOL.estimated_time_departure">
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group" v-bind:class="feedback(editBOL.shipping_line)">
                                                                <label class="col-sm-3 control-label">Shipping Line</label>

                                                                <div class="col-sm-9">
                                                                    <select :disabled="noRecord"  id="editBOL_bl_shipping_Line" class="form-control" v-model="editBOL.shipping_line">
                                                                        <option value=""></option>
                                                                        <option :value="shipping_line.shipping_line" v-for="shipping_line in shipping_lines">@{{shipping_line.shipping_line}}</option>
                                                                    </select>
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group" v-bind:class="feedback(editBOL.forwarder)">
                                                                <label class="col-sm-3 control-label">Forwarder</label>

                                                                <div class="col-sm-9">
                                                                    <select :disabled="noRecord" id="editBOL_bl_forwarder" class="form-control" v-model="editBOL.forwarder">
                                                                        <option value=""></option>
                                                                        <option :value="forwarder.forwarder" v-for="forwarder in forwarders">@{{forwarder.forwarder}}</option>
                                                                    </select>
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group" v-bind:class="feedback(editBOL.vessel)">
                                                                <label class="col-sm-3 control-label">Vessel</label>

                                                                <div class="col-sm-9">
                                                                    <select :disabled="noRecord" id="editBOL_bl_vessel" class="form-control" v-model="editBOL.vessel">
                                                                        <option value=""></option>
                                                                        <option :value="vessel.vessel" v-for="vessel in vessels">@{{vessel.vessel}}</option>
                                                                    </select>
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group" v-bind:class="(editBOL.pod != null) ? feedback(editBOL.pod) : ''">
                                                                <label class="col-sm-3 control-label">Port of Discharge</label>

                                                                <div class="col-sm-9">
                                                                    <select :disabled="noRecord" id="editBOL_bl_pod" class="form-control" v-model="editBOL.pod">
                                                                        <option value=""></option>
                                                                        <option value="SOUTH">SOUTH</option>
                                                                        <option value="NORTH">NORTH</option>
                                                                    </select>
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-sm-3 control-label">Estimated time of Arrival</label>

                                                                <div class="col-sm-9">
                                                                        <input :disabled="noRecord" type="text" class="form-control" id="editBOL_bl_ETA" v-model="editBOL.estimated_time_arrival">
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-sm-3 control-label">Volume</label>

                                                                <div class="col-sm-9">
                                                                    <input :disabled="noRecord" type="text" class="form-control" disabled v-model="editBOL.volume">
                                                                    <ul class="list-group">
                                                                        <li class="list-group-item" v-for="ctype in edit_distinct_container_type" v-if="ctype.container_type != ''">
                                                                            <h3 style="color: #942911">@{{ctype.count + ' x   ' + ctype.container_type}}</h3>
                                                                        </li>
                                                                    </ul>
                                                                    <div class="hr-line-dashed"></div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                        </fieldset>
                                        <div v-show="!noRecord">
                                            <fieldset>
                                                <legend>List of Containers</legend>
                                                <div class="row">
                                                        <div class="col-lg-5">
                                                            <label for="BL"># Container</label>
                                                            <div class="hr-line-dashed"></div>
                                                            <div class="col-lg-8">
                                                                <input type="text" class="form-control" v-model="edit_number_container">
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <button class="btn btn-primary" :disabled="edit_number_container < 1" @click="generateContainer_edit()"> <i class="fa fa-plus"></i> Add </button>
                                                            </div>

                                                        </div>

                                                </div>
                                                <div class="alert alert-danger" v-if="edit_list_container_required">
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
                                                                                <th :style="(number_of_container_type_edit) ? '' : {'color':'red'}">Container Type <br>
                                                                                <span v-if="editBOL.edit_list_container.length > 0"> <small>Same?</small> <input type="checkbox" class="i-checks" v-model="edit_sameContainer"> </span>
                                                                                </th>
                                                                                <th>QTY</th>
                                                                                <th>Split</th>
                                                                                <th>Action</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>

                                                                                <tr v-if="!container.remove" v-for="(container,i) in editBOL.edit_list_container">
                                                                                    <td>
                                                                                        @{{i + 1}}
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="text"
                                                                                        :class="container.container_number.search('_') > -1 ? 'container_code_invalid_edit' : ''"
                                                                                        class="form-control container_register_Edit" placeholder="Container #" :value="container.container_number.toUpperCase()"
                                                                                        @input="container.container_number = $event.target.value.toUpperCase()"
                                                                                        @keyup="container.container_number = $event.target.value.toUpperCase()"
                                                                                        @blur="checkInvalidCodeEdit()"
                                                                                        v-model="container.container_number">
                                                                                    </td>
                                                                                    <td>
                                                                                            <select id="edit_bl_pod" class="form-control" @change="check_same_container_type_edit(container.container_type)" v-model="container.container_type">
                                                                                                    <option value=""></option>
                                                                                                    <option :value="type.name" v-for="type in container_types">@{{type.name}}</option>
                                                                                                </select>

                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="text" class="form-control" placeholder="Container #"  v-model="container.quantity">
                                                                                    </td>
                                                                                    <td>
                                                                                        {{-- <input type="text" :disabled="container.quantity > 0" class="form-control" placeholder="BL #"  v-model="container.split_bl_no_fk">      --}}
                                                                                        <select class="form-control split_bl_edit"  multiple="multiple" :data-index="i" v-model="container.split_bl_no_fk">
                                                                                            <option value=""></option>
                                                                                            <option :value="bl_no.bl_no" v-for="bl_no in list_of_bl_no_for_split">@{{bl_no.bl_no}}</option>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td>
                                                                                        <button class="btn btn-danger btn-outline dim btn-outline"
                                                                                            title="remove this row"
                                                                                            @click="remove_container_row_edit(i)"
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
                                                    <button :disabled="!number_of_container_type_edit || container_code_invalid_edit > 0" class="btn btn-primary btn-block" @click="updateObj"> <i class="fa fa-save"></i> Update</button>
                                                </div>
                                            </fieldset>
                                        </div>
                                    {{-- END OF BODY TAB 2 --}}
                                </div>
                            </div>
                            <div id="tab-5" class="tab-pane">
                                <div class="panel-body">
                                    {{-- BEGIN OF BODY TAB 3 --}}
                                    <fieldset>
                                        <legend>Checking for Split Container</legend>

                                        <small>Click BL to view bl splits</small>
                                    </fieldset>
                                    <div class="row">
                                        <div class="col-lg-3">
                                                <input type="text" id="date_month"  placeholder="Please Select month..." class="form-control">
                                        </div>
                                        <div class="col-lg-3 pull-right">
                                            <h3> # of split for this month @{{bl_with_split.length}}</h3>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-lg-12">

                                            <table class="table table-hover">
                                                <thead>
                                                <tr>
                                                    <th>BL #</th>
                                                    <th>Container #</th>
                                                    <th>Quantity</th>
                                                    <th># of BL Connected</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    <tr
                                                    :style="1 + parseInt(_.sumBy(split.splits_bl_array,'quantity')) > 1 ? {'background-color':'#f97272' } : {'background-color':'#b0ff8c'}"
                                                    v-for="(split,i) in bl_with_split"
                                                    >
                                                        <td @click="modalSplit(i)" style="cursor:pointer" >@{{split.bl_no_fk}}</td>
                                                        <td>@{{split.container_number}}</td>
                                                        <td>@{{ 1 + parseInt(_.sumBy(split.splits_bl_array,'quantity')) }}</td>
                                                        <td>@{{split.splits_bl_array.length}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                                <div class="alert alert-danger"  v-if="bl_with_split.length == 0">
                                                        No Split Record Found.
                                                </div>
                                        </div>

                                        <div class="modal inmodal" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog">
                                            <div class="modal-content animated fadeIn">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                        <i class="fa fa-truck modal-icon"></i>
                                                        <h4 class="modal-title">List of splits for</h4>
                                                        <h5>Container #
                                                            <span style="color:red">
                                                                <span v-if="bl_with_split.length > 0">
                                                                        @{{ bl_with_split[selectedIndexViewSplit].container_number }}
                                                                </span>
                                                            </span>
                                                        </h5>
                                                        <small class="font-bold"></small>
                                                    </div>
                                                    <div class="modal-body">
                                                        <table class="table table-hover">
                                                            <thead>
                                                            <tr>
                                                                <th>BL #</th>
                                                                <th  v-if="bl_with_split.length > 0">Quantity
                                                                    {{-- <button v-if="_.sumBy(bl_with_split[selectedIndexViewSplit].splits_bl_array,'quantity') > 0" class="btn btn-primary btn-sm pull-right"> change all to 0</button>  --}}
                                                                </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody  v-if="bl_with_split.length > 0">
                                                                <tr v-for="splits in bl_with_split[selectedIndexViewSplit].splits_bl_array">
                                                                    <td>@{{splits.bl_no_fk}}</td>
                                                                    <td>
                                                                        <input type="text" size="1" style="text-align:center;" @blur="saveSplitQuantity(splits.id,splits.quantity)" class="form-control" v-model.number="splits.quantity">
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- END OF BODY TAB 3 --}}
                                </div>
                            </div>
                            <div id="tab-6" class="tab-pane">
                                    <div class="panel-body">
                                        {{-- BEGIN OF BODY TAB 3 --}}
                                        <fieldset>
                                            <legend>Edit Bill of Lading #</legend>


                                        </fieldset>
                                        <div class="row">
                                                <div class="form-horizontal">
                                                    <div class="form-group"><label class="col-sm-2 control-label">OLD BL #</label>

                                                        <div class="col-sm-8">
                                                            <input type="text" :disabled="ready_transfer"  @blur="searchExistForTransfer" v-model="transferBLNo" class="form-control">
                                                            <span v-if="transfer_not_exit_bl" class="help-block m-b-none" style="color:red">This BL # is not exist</span>
                                                        </div>
                                                    </div>
                                                    <div class="hr-line-dashed"></div>
                                                    <div class="form-group"><label class="col-sm-2 control-label">NEW BL #</label>
                                                        <div class="col-sm-8"><input :disabled="!ready_transfer" type="text" v-model="transferNewBLNo" class="form-control">
                                                        </div>
                                                    </div>

                                                    <div class="hr-line-dashed"></div>

                                                    <button @click="updateTransferBL" :disabled="!ready_transfer || transferNewBLNo.trim().length == 0" class="btn btn-block btn-primary">Update</button>
                                                </div>
                                        </div>



                                        {{-- END OF BODY TAB 6 --}}
                                    </div>
                            </div>
                            <div id="tab-7" class="tab-pane">
                                <div class="panel-body">
                                    {{-- BEGIN OF BODY TAB 3 --}}
                                    <fieldset>
                                        <legend>Split container update  <span style="color:red" class="pull-right"> @{{  Object.keys(this.split_lists).length  }} </span></legend>


                                    </fieldset>
                                    <div class="row"  style="height:500px;overflow-x:scroll"  id="tablerow">


                                        <table class="table  table-hover table-bordered-dark   toggle-arrow-tiny"  id="fixTable_chrome" >
                                            <thead>
                                            <tr>
                                                <th>BL #</th>
                                                <th>Container #</th>
                                                <th>List to update</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody >
                                                <tr

                                                v-for="(split,i) in split_lists"

                                                >
                                                    <td @click="modalSplit(i)" style="cursor:pointer" >@{{split.bl_no}}</td>
                                                    <td>@{{split.container_number}}</td>
                                                    <td >
                                                        <ul>
                                                            <li v-for="(diff,key,index) in split.difference" v-if="['cid','bid','bl_no','quantity','split_bl_no_fk'].includes(key) == false">
                                                                @{{ key }}
                                                            </li>
                                                        </ul>
                                                    </td>
                                                    <td>
                                                        <button type="button" @click="splitlistsindex(i)" class="btn btn-primary pull-right"  data-toggle="modal" data-target="#myModal10" >
                                                           Update
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>


                                    <div  class="modal inmodal"  id="myModal10" tabindex="-1" role="dialog"  aria-hidden="true">
                                        <div class="modal-dialog modal-lg" >
                                            <div class="modal-content animated fadeIn">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                    <h4 class="modal-title">Split Updating</h4>
                                                    <h5 class="modal-title" v-if="selectedIndexSplitUpdate > -1"><small>Container #</small>   @{{ split_lists[selectedIndexSplitUpdate].container_number }}</h5>
                                                </div>
                                                <div class="modal-body"  v-if="selectedIndexSplitUpdate > -1">
                                                   <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <b style="color:red">MAIN</b>
                                                                <br>
                                                                <span v-if="selectedIndexSplitUpdate > -1">@{{ split_lists[selectedIndexSplitUpdate].main.bl_no }} </span>
                                                            </div>
                                                            <div class="panel-body">
                                                                <ul>
                                                                    <li v-for="(diff,key,index) in split_lists[selectedIndexSplitUpdate].difference" v-if="['cid','bid','bl_no','quantity','split_bl_no_fk'].includes(key) == false">
                                                                        @{{ key }} - <b>@{{ diff }} </b>
                                                                    </li>
                                                                </ul>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <b style="color:orange">SPLIT</b>
                                                                <br>
                                                                <span v-if="selectedIndexSplitUpdate > -1"> @{{ split_lists[selectedIndexSplitUpdate].bl_no }} </span>
                                                            </div>
                                                            <div class="panel-body" style="max-height: 500px; overflow-x:scroll">
                                                                <div class="form-group" v-for="(diff,key,index) in split_lists[selectedIndexSplitUpdate].difference" v-if="['cid','bid','bl_no','quantity','split_bl_no_fk'].includes(key) == false">
                                                                    <label class="col-lg-12 col-form-label"> @{{ key }}</label>
                                                                    <div class="col-lg-9">
                                                                        <input type="text" v-model="split_lists[selectedIndexSplitUpdate][key]" :data-container="split_lists[selectedIndexSplitUpdate].container_number" :data-bid="split_lists[selectedIndexSplitUpdate].bid" :data-cid="split_lists[selectedIndexSplitUpdate].cid" class="form-control" disabled>

                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <button @click="updatesplitcontainer(selectedIndexSplitUpdate,key)" v-if="split_lists[selectedIndexSplitUpdate][key] != split_lists[selectedIndexSplitUpdate].difference[key]" class="btn btn-outline btn-primary dim" type="button">update</button>
                                                                        <button v-else class="btn btn-primary dim" type="button" disabled><i class="fa fa-check"></i></button>
                                                                    </div>
                                                                </div>
                                                            </div>
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



                                    {{-- END OF BODY TAB 6 --}}
                                </div>
                        </div>
                        </div>
                    </div>



            </div>
        </div>
    </div>
</div>




@endsection

@section('vuejsscript')
    <script src="{{asset('/js/vuejs/import_new.js')}}"></script>
@endsection


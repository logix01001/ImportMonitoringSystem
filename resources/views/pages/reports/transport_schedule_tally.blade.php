@extends('layout.index2')

@section('body')

<div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Transport container tally</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">

                        <div class="col-lg-4">
                            <h2> As of @{{ today }} </h2>
                        </div>

                        <div class="col-lg-4">
                            <a  href="{{route('importation.transport_schedule_tally_export_download')}}" class="btn btn-primary btn-outline btn-sm btn-dim pull-right download" data-clipboard-target="#tbodydata"> Download Template <i class="fa fa-2x fa-file-excel"></i> <i class="fa fa-2x fa-download"></i> </a>
                            <button @click="copydata" class="btn btn-primary btn-outline btn-sm download" data-clipboard-target="#tbodydata"> Copy data <i class="fa fa-2x fa-copy"></i> </button>
                            {{-- href="{{route('importation.transport_schedule_tally_export_download')}}" --}}
                            {{-- @click="selectElementContents(document.getElementById('tbodydata') )" --}}
                        </div>
                    </div>
                    <br>
                    <div id="tablediv">

                        <table class="table fixed_header" style="border:1px solid;white-space: nowrap;">
                            <thead>
                                <tr>
                                    <th rowspan="3" style="width:400px !important">Date</th>
                                    <th rowspan="3">Discharge</th>
                                    <th rowspan="3">W Gatepass</th>
                                    <th colspan="4" v-for="factory in factories">
                                        @{{ factory }}
                                    </th>
                                    <th  colspan="4">TOTAL</th>
                                </tr>
                                <tr>
                                    <template v-for="factory in factories">
                                        <template v-for="pod in port">
                                            <th colspan="2">@{{pod}}</th>
                                        </template>
                                    </template>
                                    <template v-for="pod in port">
                                        <th colspan="2">@{{pod}}</th>
                                    </template>
                                </tr>
                                <tr>
                                    <template v-for="factory in factories">
                                        <template v-for="pod in port">
                                            <th>20</th>
                                            <th>40</th>
                                        </template>
                                    </template>
                                    <template v-for="pod in port">
                                        <th>20</th>
                                        <th>40</th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody id="tbodydata">
                                <tr v-for="row in data">
                                    <td class="stickycolumn">@{{row.date}}</td>
                                    <td class="stickycolumn1">@{{row.discharge}}</td>
                                    <td class="stickycolumn2">@{{row.gatepass}}</td>
                                    <template v-for="factory in factories">
                                        <td>
                                            <span v-if="row[factory][0] > 0"> @{{row[factory][0]}}</span>
                                            <span v-else>-</span>
                                        </td>
                                        <td>
                                            <span v-if="row[factory][1] > 0"> @{{row[factory][1]}}</span>
                                            <span v-else>-</span>
                                        </td>
                                        <td>
                                            <span v-if="row[factory][2] > 0"> @{{row[factory][2]}}</span>
                                            <span v-else>-</span>
                                        </td>
                                        <td>
                                            <span v-if="row[factory][3] > 0"> @{{row[factory][3]}}</span>
                                            <span v-else>-</span>
                                        </td>
                                    </template>
                                    <td>@{{row.total[0]}}</td>
                                    <td>@{{row.total[1]}}</td>
                                    <td>@{{row.total[2]}}</td>
                                    <td>@{{row.total[3]}}</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </div>
</div>


@endsection

@section('vuejsscript')
    <script src="{{asset('/js/vuejs/transport_schedule_tally.js')}}"></script>
@endsection

@section('headscript')
<style>
    #tablediv {
        max-width: 100%;
        height: 500px;
        overflow-y: scroll;

    }
    .fixed_header tbody{

        width: 100%;
        max-width: 100%;
        overflow: auto;

        overflow-y: scroll;
    }



    /* .table thead {
        position: -webkit-sticky;
        position: sticky;
        top: 0;
    }

    .table th {
        text-align: center;
    } */
    .table th {
       border: 1px black solid;
    }
    .table td {
       border: 1px black solid;
    }
    .table > thead > tr > th {
    border-bottom: 1px solid #000;
    }
</style>
@endsection

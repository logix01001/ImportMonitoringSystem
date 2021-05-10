@extends('layout.index2')


@section('body')

                    @if (session('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                    @endif
                    @if (session('nofile'))
                    <div class="alert alert-info">
                        {{ session('nofile') }}
                    </div>
                    @endif
                    @if (session('Error_Importation'))
                    <div class="alert alert-danger">
                        {{ session('Error_Importation') }}
                    </div>
                    @endif

                    @component('components.uploadingcsv')
                        @slot('import_for')
                            INSERT New Record
                        @endslot
                        @slot('title')
                            iNSERT IMPORT MONITORING
                        @endslot
                        @slot('action')
                            {{route('importation.do_excel_import')}}
                        @endslot
                        @slot('downloadable')
                             {{url('/download/headerCSV.csv')}}
                        @endslot
                        @slot('color')
                            btn-primary
                        @endslot
                        @slot('file_updated_date')
                        <?php
                            $filename = storage_path() . '/downloads/headerCSV.csv';
                            if (file_exists($filename)) {
                                echo date ("F d Y H:i:s.", filemtime($filename));
                            }
                        ?>
                        @endslot
                    @endcomponent

                    @component('components.uploadingcsv')
                        @slot('import_for')
                            UPDATE DISCHARGE CONTAINER
                        @endslot
                        @slot('title')
                            UPDATE DISCHARGE CONTAINER
                        @endslot
                        @slot('action')
                            {{route('importation.doexcel_import_update_discharge_date')}}
                        @endslot
                        @slot('downloadable')
                            {{url('/download/update_container_discharges.csv')}}
                        @endslot
                        @slot('color')
                            btn-warning
                        @endslot
                        @slot('file_updated_date')
                        <?php
                            $filename = storage_path() . '/downloads/update_container_discharges.csv';
                            if (file_exists($filename)) {
                                echo date ("F d Y H:i:s.", filemtime($filename));
                            }
                        ?>
                        @endslot
                    @endcomponent


                    @component('components.uploadingcsv')
                        @slot('import_for')
                            UPDATE Docs Team
                        @endslot
                        @slot('title')
                            UPDATE DOCS TEAM
                        @endslot
                        @slot('action')
                            {{route('importation.do_excel_import_docs_team')}}
                        @endslot
                        @slot('downloadable')
                            {{url('/download/docs_team_template.csv')}}
                        @endslot
                        @slot('color')
                            btn-success
                        @endslot
                        @slot('file_updated_date')
                        <?php
                            $filename = storage_path() . '/downloads/docs_team_template.csv';
                            if (file_exists($filename)) {
                                echo date ("F d Y H:i:s.", filemtime($filename));
                            }
                        ?>
                        @endslot
                    @endcomponent

                    {{-- @component('components.uploadingcsv')
                        @slot('import_for')
                            UPDATE TSAD
                        @endslot
                        @slot('title')
                               TSAD
                        @endslot
                        @slot('action')
                            {{route('importation.do_excel_import_update_tsad')}}
                        @endslot
                        @slot('downloadable')
                            {{url('/download/update_tsad.csv')}}
                        @endslot
                        @slot('color')
                            btn-warning
                        @endslot
                        @slot('file_updated_date')
                       //
                            // $filename = storage_path() . '/downloads/update_tsad.csv';
                            // if (file_exists($filename)) {
                            //     echo date ("F d Y H:i:s.", filemtime($filename));
                            // }

                        @endslot
                    @endcomponent --}}

                    @component('components.uploadingcsv')
                        @slot('import_for')
                            UPDATE Gatepass
                        @endslot
                        @slot('title')
                            GATEPASS
                        @endslot
                        @slot('action')
                            {{route('importation.do_excel_import_update_gatepass')}}
                        @endslot
                        @slot('downloadable')
                            {{url('/download/update_gatepass.csv')}}
                        @endslot
                        @slot('color')
                            btn-danger
                        @endslot
                        @slot('file_updated_date')
                        <?php
                            $filename = storage_path() . '/downloads/update_gatepass.csv';
                            if (file_exists($filename)) {
                                echo date ("F d Y H:i:s.", filemtime($filename));
                            }
                        ?>
                        @endslot
                    @endcomponent

                    @component('components.uploadingcsv')
                        @slot('import_for')
                            UPDATE Current Status
                        @endslot
                        @slot('title')
                            CURRENT STATUS
                        @endslot
                        @slot('action')
                            {{route('importation.do_excel_import_update_current_status')}}
                        @endslot
                        @slot('downloadable')
                            {{url('/download/update_current_status.csv')}}
                        @endslot
                        @slot('color')
                            btn-primary
                        @endslot
                        @slot('file_updated_date')
                        <?php
                            $filename = storage_path() . '/downloads/update_current_status.csv';
                            if (file_exists($filename)) {
                                echo date ("F d Y H:i:s.", filemtime($filename));
                            }
                        ?>
                        @endslot
                    @endcomponent

                    @component('components.uploadingcsv')
                        @slot('import_for')
                            UPDATE Validity
                        @endslot
                        @slot('title')
                            VALIDITY
                        @endslot
                        @slot('action')
                            {{route('importation.do_excel_import_update_validity')}}
                        @endslot
                        @slot('downloadable')
                            {{url('/download/update_validity.csv')}}
                        @endslot
                        @slot('color')
                            btn-success
                        @endslot
                        @slot('file_updated_date')
                        <?php
                            $filename = storage_path() . '/downloads/update_validity.csv';
                            if (file_exists($filename)) {
                                echo date ("F d Y H:i:s.", filemtime($filename));
                            }
                        ?>
                        @endslot
                    @endcomponent

                    {{-- @component('components.uploadingcsv')
                        @slot('import_for')
                            UPDATE Booking Time
                        @endslot
                        @slot('title')
                            TRUCKER BOOKING TIME
                        @endslot
                        @slot('action')
                            {{route('importation.do_excel_import_update_booking_time')}}
                        @endslot
                        @slot('downloadable')
                         {{url('/download/update_booking_time.csv')}}
                        @endslot
                        @slot('color')
                            btn-default
                        @endslot
                        @slot('file_updated_date')

                            // $filename = storage_path() . '/downloads/update_booking_time.csv';
                            // if (file_exists($filename)) {
                            //     echo date ("F d Y H:i:s.", filemtime($filename));
                            // }

                        @endslot
                    @endcomponent --}}

                    @component('components.uploadingcsv')
                        @slot('import_for')
                            UPDATING OF DELIVERY
                        @endslot
                        @slot('title')
                           DELIVERY UPDATE
                        @endslot
                        @slot('action')
                            {{route('importation.do_excel_import_update_delivery_pullout')}}
                        @endslot
                        @slot('downloadable')
                        {{url('/download/uploading_delivery.csv')}}
                        @endslot
                        @slot('color')
                            btn-warning
                        @endslot
                        @slot('file_updated_date')
                        <?php
                            $filename = storage_path() . '/downloads/uploading_delivery.csv';
                            if (file_exists($filename)) {
                                echo date ("F d Y H:i:s.", filemtime($filename));
                            }
                        ?>
                        @endslot
                    @endcomponent

                    @component('components.uploadingcsv')
                        @slot('import_for')
                           UPDATING ACTUAL UNLOADING DATE
                        @endslot
                        @slot('title')
                            UNLOADING CONTAINERS
                        @endslot
                        @slot('action')
                            {{route('importation.do_excel_import_update_actual_unload')}}
                        @endslot
                        @slot('downloadable')
                        {{url('/download/unloading_container.csv')}}
                        @endslot
                        @slot('color')
                            btn-primary
                        @endslot
                        @slot('file_updated_date')
                        <?php
                            $filename = storage_path() . '/downloads/unloading_container.csv';
                            if (file_exists($filename)) {
                                echo date ("F d Y H:i:s.", filemtime($filename));
                            }
                        ?>
                        @endslot
                    @endcomponent

                    @component('components.uploadingcsv')
                        @slot('import_for')
                            UPDATE RETURNED
                        @endslot
                        @slot('title')
                            UPDATE RETURNED
                        @endslot
                        @slot('action')
                            {{route('importation.do_excel_import_update_return')}}
                        @endslot
                        @slot('downloadable')
                            {{url('/download/update_return.csv')}}
                        @endslot
                        @slot('color')
                            btn-danger
                        @endslot
                        @slot('file_updated_date')
                        <?php
                            $filename = storage_path() . '/downloads/update_return.csv';
                            if (file_exists($filename)) {
                                echo date ("F d Y H:i:s.", filemtime($filename));
                            }
                        ?>
                        @endslot
                    @endcomponent


@endsection

@section('vuejsscript')
<script>
    $('form').submit(function(){

            $(this).children('button[type=submit]').prop('disabled', true);


    });
</script>
@endsection

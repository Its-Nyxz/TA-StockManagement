@extends('layouts.app')
@section('title', __('types of goods'))
@section('content')
    <x-head-datatable />
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card w-100">
                    <div class="card-header row">
                        <div class="d-flex justify-content-end align-items-center w-100">
                            @if (Auth::user()->role->name != 'staff')
                                <button class="btn btn-success" type="button" data-toggle="modal" data-target="#TambahData"
                                    id="modal-button"><i class="fas fa-plus"></i> {{ __('Add data') }}</button>
                            @endif
                        </div>
                    </div>


                    <!-- Modal -->
                    <div class="modal fade" id="TambahData" tabindex="-1" aria-labelledby="TambahDataModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="TambahDataModalLabel"></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group mb-3">
                                        <label for="name">{{ __('name') }}</label>
                                        <input type="text" class="form-control" id="name" autocomplete="off">
                                        <input type="hidden" name="id" id="id">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="desc">{{ __('description') }}</label>
                                        <textarea class="form-control" id="desc"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                        id="kembali">{{ __('back') }}</button>
                                    <button type="button" class="btn btn-success"
                                        id="simpan" data-action="simpan">{{ __('save') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="data-jenis" width="100%"
                                class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                                <thead>
                                    <tr>
                                        <th class="border-bottom-0" width="8%">{{ __('no') }}</th>
                                        <th class="border-bottom-0">{{ __('name') }}</th>
                                        <th class="border-bottom-0">{{ __('description') }}</th>
                                        @if (Auth::user()->role->name != 'staff')
                                            <th class="border-bottom-0" width="1%">{{ __('action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-data-table />
    <script>
        function isi() {
            // Define language settings
            const langID = {
                decimal: "",
                searchPlaceholder: "Cari Data",
                emptyTable: "Tabel kosong",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                infoPostFix: "",
                thousands: ".",
                lengthMenu: "Tampilkan _MENU_ data",
                loadingRecords: "Memuat...",
                processing: "Sedang memproses...",
                search: "Cari:",
                zeroRecords: "Data tidak ditemukan",
                paginate: {
                    first: "<<",
                    last: ">>",
                    next: ">",
                    previous: "<",
                },
                aria: {
                    orderable: "Urutkan kolom ini",
                    orderableReverse: "Urutkan kolom ini terbalik",
                },
            };

            const langEN = {
                decimal: "",
                searchPlaceholder: "Search Data",
                emptyTable: "No data available",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                infoPostFix: "",
                thousands: ",",
                lengthMenu: "Show _MENU_ entries",
                loadingRecords: "Loading...",
                processing: "Processing...",
                search: "Search:",
                zeroRecords: "No matching records found",
                paginate: {
                    first: "<<",
                    last: ">>",
                    next: ">",
                    previous: "<",
                },
                aria: {
                    orderable: "Order by this column",
                    orderableReverse: "Reverse order this column",
                },
            };

            const currentLang = $('html').attr('lang');
            const languageSettings = currentLang === 'id' ? langID : langEN;

            $('#data-jenis').DataTable({
                responsive: true,
                lengthChange: true,
                autoWidth: false,
                processing: true,
                serverSide: true,
                language: languageSettings,
                ajax: `{{ route('barang.jenis.list') }}`,
                columns: [{
                        "data": null,
                        "sortable": false, 
                        "className": "text-center",        
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'description',
                        name: 'description',
                        render: function(data) {
                            if (data == null) {
                                return "<span class='font-weight-bold'>-</span>";
                            }
                            const formattedText = capitalizeAfterPeriod(data);
                            return `<span class="capitalize-first-after-period">${formattedText}</span>`;
                        }
                    },
                    @if (Auth::user()->role->name != 'staff')
                        {
                            data: 'tindakan',
                            name: 'tindakan'
                        }
                    @endif
                ]
            }).buttons().container();
        }

        function simpan() {
            if ($('#name').val().length == 0) {
                return Swal.fire({
                    position: "center",
                    icon: "warning",
                    title: "nama tidak boleh kosong !",
                    showConfirmButton: false,
                    imer: 1500
                });
            }
            $.ajax({
                url: `{{ route('barang.jenis.save') }}`,
                type: "post",
                data: {
                    name: $("#name").val(),
                    description: $("#desc").val(),
                    "_token": "{{ csrf_token() }}"
                },
                success: function(res) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: res.message,
                        showConfirmButton: false,
                        timer: 1500
                    });

                    $('#kembali').click();
                    $("#name").val(null);
                    $("#desc").val(null);
                    $('#data-jenis').DataTable().ajax.reload();
                },
                error: function(err) {
                    console.log(err.responJson.text);
                },
            });
        }


        function ubah() {
            $.ajax({
                url: `{{ route('barang.jenis.update') }}`,
                type: "put",
                data: {
                    id: $("#id").val(),
                    name: $("#name").val(),
                    description: $("#desc").val(),
                    "_token": "{{ csrf_token() }}"
                },
                success: function(res) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: res.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#kembali').click();
                    $("#name").val(null);
                    $("#desc").val(null);
                    $('#data-jenis').DataTable().ajax.reload();
                    $('#simpan').text('Simpan');
                },
                error: function(err) {
                    console.log(err.responJson.text);
                },

            });
        }


        $(document).ready(function() {
            isi();

            $('#simpan').on('click', function() {
                // if ($(this).text() === 'Simpan Perubahan') {
                //     ubah();
                // } else {
                //     simpan();
                // }

                var action = $(this).data('action');
                if (action === 'ubah') {
                    ubah(); 
                } else {
                    simpan(); 
                }
            });

            $("#modal-button").on("click", function() {
                $("#TambahDataModalLabel").text("{{ __('Add type of goods') }}");
                $("#name").val(null);
                $("#desc").val(null);
                // $("#simpan").text("Simpan");
                $("#simpan").data('action', 'simpan');
            });

        });



        $(document).on("click", ".ubah", function() {
            let id = $(this).attr('id');
            $("#modal-button").click();
            // $("#simpan").text("Simpan Perubahan");
            $("#simpan").data('action', 'ubah');
            $.ajax({
                url: "{{ route('barang.jenis.detail') }}",
                type: "post",
                data: {
                    id: id,
                    "_token": "{{ csrf_token() }}"
                },
                success: function({
                    data
                }) {

                    $("#id").val(data.id);
                    $("#name").val(data.name);
                    $("#desc").val(data.description);
                }
            });

        });

        $(document).on("click", ".hapus", function() {
            let id = $(this).attr('id');
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success m-1",
                    cancelButton: "btn btn-danger m-1"
                },
                buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
                title: "{{ __('You are Sure') }} ?",
                text: "{{ __('This data will be deleted') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "{{ __('Yes, Delete') }}",
                cancelButtonText: "{{ __('No, Cancel') }}!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('barang.jenis.delete') }}",
                        type: "delete",
                        data: {
                            id: id,
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: res.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            $('#data-jenis').DataTable().ajax.reload();
                        }
                    });
                }
            });

        });
    </script>

@endsection

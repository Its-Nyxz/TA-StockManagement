@extends('layouts.app')
@section('title', __('goods'))
@section('content')
    <x-head-datatable />
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card w-100">
                    <div class="card-header row">
                        <div class="d-flex justify-content-end align-items-center w-100">
                            @can('super')
                                <button type="button" class="btn btn-primary m-1 mt-1" id="upload-modal-button"
                                    data-bs-toggle="modal" data-bs-target="#uploadModal">
                                    <i class="fas fa-file-import"></i>
                                </button>
                            @endcan
                            @can('super&admin')
                                <button class="btn btn-success" type="button" data-toggle="modal" data-target="#TambahData"
                                    id="modal-button"><i class="fas fa-plus"></i> {{ __('Add data') }}</button>
                            @endcan
                        </div>
                    </div>
                    <!-- Modal -->
                    <div class="modal fade" id="TambahData" aria-labelledby="TambahDataModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="TambahDataModalLabel"></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="form-group">
                                                <label for="kode" class="form-label">{{ __('code of goods') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="kode" readonly class="form-control">
                                                <input type="hidden" name="id" />
                                            </div>
                                            <div class="form-group">
                                                <label for="nama" class="form-label">{{ __('name of goods') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="nama" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="jenisbarang" class="form-label">{{ __('types of goods') }} <span
                                                        class="text-danger">*</span></label>
                                                <select name="jenisbarang" id="jenisbarang" class="form-control">
                                                    <option value="">-- {{ __('select category') }} --</option>
                                                    @foreach ($jenisbarang as $jb)
                                                        <option value="{{ $jb->id }}">{{ $jb->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="satuan" class="form-label">{{ __('unit of goods') }} <span
                                                        class="text-danger">*</span></label>
                                                <select name="satuan" id="satuan" class="form-control">
                                                    <option value="">-- {{ __('select unit') }} --</option>
                                                    @foreach ($satuan as $s)
                                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="merk" class="form-label">{{ __('brand of goods') }} <span
                                                        class="text-danger">*</span></label>
                                                <select name="merk" id="merk" class="form-control">
                                                    <option value="">-- {{ __('select brand') }} --</option>
                                                    @foreach ($merk as $m)
                                                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="supplier" class="form-label">{{ __('supplier of goods') }}
                                                    <span class="text-danger">*</span></label>
                                                <select name="supplier" id="supplier" class="form-control">
                                                    <option selected value="">--{{ __('select supplier') }} --
                                                    </option>
                                                    @foreach ($supplier as $sp)
                                                        <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group item-count" id="item-count">
                                                <label for="harga" class="form-label">{{ __('initial amount') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" value="0" name="jumlah" class="form-control">
                                            </div>
                                            <!-- <div class="form-group">
                                                                    <label for="harga" class="form-label">{{ __('price of goods') }} <span class="text-danger">*</span></label>
                                                                    <input type="text"  id="harga" name="harga" class="form-control" placeholder="RP. 0">
                                                                </div> -->
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="title" class="form-label">{{ __('photo') }}</label>
                                                <img src="{{ asset('default.png') }}" width="80%" alt="profile-user"
                                                    id="outputImg" class="text-center">
                                                <input class="form-control mt-5" id="GetFile" name="photo"
                                                    type="file" accept=".png,.jpeg,.jpg,.svg">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                        id="kembali">{{ __('back') }}</button>
                                    <button type="button" class="btn btn-success" id="simpan"
                                        data-action="simpan">{{ __('save') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Import-->
                    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="uploadModalLabel">Import Barang</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('barang.import') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <label for="file">{{ __('Choose File') }}</label>
                                            <input type="file" name="file" class="form-control" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary mt-2">{{ __('Import') }}</button>
                                    </form>
                                    <hr>
                                    <a href="{{ route('barang.template') }}"
                                        class="btn btn-success mt-2">{{ __('Download Template') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="data-tabel" width="100%"
                                class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                                <thead>
                                    <tr>
                                        <th class="border-bottom-0" width="4%">{{ __('no') }}</th>
                                        <th class="border-bottom-0">{{ __('photo') }}</th>
                                        <th class="border-bottom-0">{{ __('code') }}</th>
                                        <th class="border-bottom-0">{{ __('variant name') }}</th>
                                        <th class="border-bottom-0">{{ __('type') }}</th>
                                        <th class="border-bottom-0">{{ __('unit') }}</th>
                                        <th class="border-bottom-0">{{ __('brand') }}</th>
                                        <th class="border-bottom-0">{{ __('supplier') }}</th>
                                        <th class="border-bottom-0">{{ __('initial stock') }}</th>
                                        <!-- <th class="border-bottom-0">{{ __('price') }}</th> -->
                                        {{-- @if (Auth::user()->role->name != 'staff') --}}
                                        @can('super&admin')
                                            <th class="border-bottom-0" width="1%">{{ __('action') }}</th>
                                        @endcan
                                        {{-- @endif --}}
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        function harga() {
            this.value = formatIDR(this.value.replace(/[^0-9.]/g, ''));
        }


        function formatIDR(angka) {
            // Ubah angka menjadi string dan hapus simbol yang tidak diperlukan
            var strAngka = angka.toString().replace(/[^0-9]/g, '');

            // Jika tidak ada angka yang tersisa, kembalikan string kosong
            if (!strAngka) return '';

            // Pisahkan angka menjadi bagian yang sesuai dengan ribuan
            var parts = strAngka.split('.');
            var intPart = parts[0];
            var decPart = parts.length > 1 ? '.' + parts[1] : '';

            // Tambahkan pemisah ribuan
            intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            // Tambahkan simbol IDR
            return 'RP.' + intPart + decPart;
        }

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

            $('#data-tabel').DataTable({
                lengthChange: true,
                processing: true,
                responsive: true,
                serverSide: true,
                language: languageSettings,
                ajax: `{{ route('barang.list') }}`,
                columns: [{
                        "data": null,
                        "sortable": false,
                        "className": "text-center",
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'img',
                        name: 'img'
                    }, {
                        data: 'code',
                        name: 'code'
                    }, {
                        data: 'name',
                        name: 'name'
                    }, {
                        data: 'category_name',
                        name: 'category_name'
                    },
                    {
                        data: 'unit_name',
                        name: 'unit_name'
                    },
                    {
                        data: 'brand_name',
                        name: 'brand_name'
                    },
                    {
                        data: 'supplier_name',
                        name: 'supplier_name'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    // {
                    //     data:'price',
                    //     name:'price'
                    // },
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
            const name = $("input[name='nama']").val();
            const code = $("input[name='kode']").val();
            const image = $("#GetFile")[0].files;
            const category_id = $("select[name='jenisbarang']").val();
            const unit_id = $("select[name='satuan']").val();
            const brand_id = $("select[name='merk']").val();
            const supplier_id = $("select[name='supplier']").val();
            // const price = $("input[name='harga']").val();
            // return console.log({name,code,category_id,unit_id,brand_id,price,quantity});
            const Form = new FormData();
            Form.append('image', image[0]);
            Form.append('code', code);
            Form.append('name', name);
            Form.append('category_id', category_id);
            Form.append('unit_id', unit_id);
            Form.append('brand_id', brand_id);
            Form.append('supplier_id', supplier_id);
            // Form.append('price', price);
            if (name.length == 0 || category_id.length == 0 || unit_id.length == 0 || brand_id.length == 0 || supplier_id
                .length == 0) {
                return Swal.fire({
                    position: "center",
                    icon: "warning",
                    title: "Bertanda * Tidak Boleh Kosong !",
                    showConfirmButton: false,
                    imer: 1500
                });
            }
            // if(price.length == 0){
            //     return Swal.fire({
            //         position: "center",
            //         icon: "warning",
            //         title: "harga tidak boleh kosong !",
            //         showConfirmButton: false,
            //         imer: 1500
            //     });
            // }
            $.ajax({
                url: `{{ route('barang.save') }}`,
                type: "post",
                processData: false,
                contentType: false,
                dataType: 'json',
                data: Form,
                success: function(res) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: res.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#kembali').click();
                    $("input[name='nama']").val(null);
                    $("input[name='kode']").val(null);
                    $("#GetFile")[0].files = null;
                    $("select[name='jenisbarang']").val(null);
                    $("select[name='satuan']").val(null);
                    $("select[name='merk']").val(null);
                    $("select[name='supplier']").val(null);
                    $("input[name='jumlah']").val(0);
                    // $("input[name='harga']").val(null);
                    $('#data-tabel').DataTable().ajax.reload();
                },
                error: function(err) {
                    console.log(err);
                },

            }).then(() => {
                setTimeout(function() {
                    location.reload(); // Reloads the page after 1500ms
                }, 1500);
            });
        }


        function ubah() {
            const name = $("input[name='nama']").val();
            const code = $("input[name='kode']").val();
            const image = $("#GetFile")[0].files;
            const category_id = $("select[name='jenisbarang']").val();
            const unit_id = $("select[name='satuan']").val();
            const brand_id = $("select[name='merk']").val();
            const supplier_id = $("select[name='supplier']").val();
            // const price = $("input[name='harga']").val();
            const quantity = $("input[name='jumlah']").val();
            // return console.log({name,code,category_id,unit_id,brand_id,price,quantity});
            const Form = new FormData();
            Form.append('id', $("input[name='id']").val());
            Form.append('image', image[0]);
            Form.append('code', code);
            Form.append('name', name);
            Form.append('category_id', category_id);
            Form.append('unit_id', unit_id);
            Form.append('brand_id', brand_id);
            Form.append('supplier_id', supplier_id);
            Form.append('quantity', quantity);
            // Form.append('price', price);
            $.ajax({
                url: `{{ route('barang.update') }}`,
                type: "post",
                contentType: false,
                processData: false,
                dataType: 'json',
                data: Form,
                success: function(res) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: res.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#kembali').click();
                    $("input[name='id']").val(null);
                    $("input[name='nama']").val(null);
                    $("input[name='kode']").val(null);
                    $("#GetFile").val(null);
                    $("select[name='jenisbarang']").val(null);
                    $("select[name='satuan']").val(null);
                    $("select[name='merk']").val(null);
                    $("select[name='supplier']").val(null);
                    $("input[name='jumlah']").val(0);
                    // $("input[name='harga']").val(null);
                    $('#data-tabel').DataTable().ajax.reload();
                },
                error: function(err) {
                    console.log(err);
                },


            }).then(() => {
                setTimeout(function() {
                    location.reload(); // Reloads the page after 1500ms
                }, 1500);
            });
        }

        $(document).ready(function() {
            $("#harga").on("input", harga);
            isi();

            $('#jenisbarang, #satuan, #merk, #supplier').select2({
                theme: 'bootstrap4',
                allowClear: true,
                minimumInputLength: 0 // Set this to enable search after 1 character
            });

            $('#simpan').on('click', function() {
                var action = $(this).data('action');
                if (action === 'ubah') {
                    ubah();
                } else {
                    simpan();
                }
            });

            $("#modal-button").on("click", function() {
                $("#TambahDataModalLabel").text("{{ __('Add goods') }}");
                $("#item-count").hide();
                $("input[name='nama']").val(null);
                $("input[name='id']").val(null);
                $("input[name='kode']").val(null);
                $("#GetFile").val(null);
                $("#outputImg").attr("src", "{{ asset('default.png') }}");
                $("select[name='jenisbarang'], select[name='satuan'], select[name='merk'], select[name='supplier']")
                    .val(null).trigger('change'); // Reset Select2
                $("input[name='jumlah']").val(0);
                // $("input[name='harga']").val(null);
                $("#simpan").data('action', 'simpan');
                // id = new Date().getTime();
                // Dapatkan timestamp saat ini
                const now = new Date();

                // Konversi ke timestamp Indonesia (WIB, UTC+7)
                const indonesiaOffset = 7 * 60 * 60 * 1000; // Offset WIB dalam milidetik
                const idTime = new Date(now.getTime() + indonesiaOffset);

                // Format `idTime` sebagai timestamp atau string sesuai kebutuhan
                const id = Math.floor(idTime.getTime() / 1000); // Dalam detik
                $("input[name='kode']").val("BRG-" + id);
            });

            $("#upload-modal-button").on("click", function() {
                $('#uploadModal').modal('show');
            });

        });



        $(document).on("click", ".ubah", function() {
            let id = $(this).attr('id');
            $("#modal-button").click();
            $("#item-count").show();
            $("#simpan").data('action', 'ubah');
            $.ajax({
                url: "{{ route('barang.detail') }}",
                type: "post",
                data: {
                    id: id,
                    "_token": "{{ csrf_token() }}"
                },
                success: function({
                    data
                }) {
                    $("input[name='id']").val(data.id);
                    $("input[name='nama']").val(data.name);
                    $("input[name='kode']").val(data.code);
                    $("select[name='jenisbarang']").val(data.category_id).trigger(
                        'change'); // Set nilai dan trigger
                    $("select[name='satuan']").val(data.unit_id).trigger(
                        'change'); // Set nilai dan trigger
                    $("select[name='merk']").val(data.brand_id).trigger(
                        'change'); // Set nilai dan trigger
                    $("select[name='supplier']").val(data.supplier_id).trigger(
                        'change'); // Set nilai dan trigger
                    $("input[name='jumlah']").val(data.quantity);
                    let imageUrl = data.image ? `{{ asset('storage/barang') }}/${data.image}` :
                        `{{ asset('default.png') }}`;
                    $("#outputImg").attr("src", imageUrl); // Set gambar pada elemen img
                    // $("input[name='harga']").val(data.price);
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
                        url: "{{ route('barang.delete') }}",
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
                            $('#data-tabel').DataTable().ajax.reload();
                        }
                    });
                }
            });


        });
    </script>
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        @endif

        @if (session('error') || $errors->has('file'))
            Swal.fire({
                icon: 'error',
                title: '{{ session('error') ? 'Gagal' : 'File Tidak Cocok' }}',
                text: '{{ session('error') ?? $errors->first('file') }}',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        @endif
    </script>
@endsection

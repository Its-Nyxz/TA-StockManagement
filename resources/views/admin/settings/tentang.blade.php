@extends('layouts.app')
@section('title', __('messages.aboutus-label'))
@section('content')
    @can('super&admin')
        <div class="container-fluid">
            <div class="row d-flex justify-content-center align-items-start w-100">
                <div class="col-lg-9 col-md-12">
                    <div class="card p-3">
                        <div class="card-header">
                            <div class="d-flex flex-column justify-content-center align-items-center w-100">
                                <label for="logo">
                                    @php
                                        $imageSrc = asset('user.png'); // Default image
                                        if (!empty($tentang->logo)) {
                                            $imageSrc = asset('storage/tentang/' . $tentang->logo); // Tentang logo
                                        } elseif (!empty(Auth::user()->image)) {
                                            $imageSrc = asset('storage/users/' . Auth::user()->image); // User image
                                        }
                                    @endphp
                                    <img id="photo_logo" src="{{ $imageSrc }}" class="img-circle elevation-2"
                                        style="width:100% !important;max-width:240px !important;aspect-ratio:1 !important;object-fit:cover !important;"
                                        alt="logo">
                                </label>
                                <input class="d-none" type="file" accept="image/*" name="logo" id="logo">
                                <h1 class="h1 text-uppercase font-weight-bold" id="judul_tentang">{{ $tentang->judul }}</h1>
                            </div>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="id" value="{{ $tentang->id }}">
                            <div class="form-group mb-3">
                                <label for="judul" class="form-label">{{ __('Judul') }}</label>
                                <input type="text" name="judul" placeholder="Judul" id="judul"
                                    value="{{ $tentang->judul }}" class="form-control">
                            </div>
                            <div class="form-group mb-3">
                                <label for="deskripsi" class="form-label">{{ __('Deskripsi') }}</label>
                                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="5">{{ $tentang->deskripsi }}</textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label for="kontak_email" class="form-label">{{ __('Email Kontak') }}</label>
                                <input type="email" name="kontak_email" placeholder="Email Kontak" id="kontak_email"
                                    value="{{ $tentang->kontak_email }}" class="form-control">
                            </div>
                            <div class="form-group mb-3">
                                <label for="kontak_telepon" class="form-label">{{ __('Telepon Kontak') }}</label>
                                <input type="text" name="kontak_telepon" placeholder="Telepon Kontak" id="kontak_telepon"
                                    value="{{ $tentang->kontak_telepon }}" class="form-control">
                            </div>
                            <div class="form-group mb-3">
                                <label for="alamat" class="form-label">{{ __('Alamat') }}</label>
                                <textarea name="alamat" id="alamat" class="form-control" rows="3">{{ $tentang->alamat }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end align-items-center">
                            <button class="btn btn-primary" id="simpan">{{ __('save') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
    @can('user')
        <div class="container-fluid">
            <div class="row d-flex justify-content-center align-items-start w-100">
                <div class="col-lg-9 col-md-12">
                    <div class="card p-3">
                        <div class="card-header">
                            <div class="d-flex flex-column justify-content-center align-items-center w-100">
                                @php
                                    $imageSrc = asset('user.png'); // Default image
                                    if (!empty($tentang->logo)) {
                                        $imageSrc = asset('storage/tentang/' . $tentang->logo); // Tentang logo
                                    }
                                @endphp
                                <img id="photo_logo" src="{{ $imageSrc }}" class="img-circle elevation-2"
                                    style="width:100% !important;max-width:240px !important;aspect-ratio:1 !important;object-fit:cover !important;"
                                    alt="logo">
                                <h1 class="h1 text-uppercase font-weight-bold mt-3">{{ $tentang->judul }}</h1>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-bold">{{ __('Deskripsi') }}</label>
                                <p>{{ $tentang->deskripsi }}</p>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-bold">{{ __('Email Kontak') }}</label>
                                <p class="text-none">{{ $tentang->kontak_email ?? __('Tidak tersedia') }}</p>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-bold">{{ __('Telepon Kontak') }}</label>
                                <p class="text-none">{{ $tentang->kontak_telepon ?? __('Tidak tersedia') }}</p>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-bold">{{ __('Alamat') }}</label>
                                <p class="text-none">{{ $tentang->alamat ?? __('Tidak tersedia') }}</p>
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan


    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            const photo_logo = $("#photo_logo");
            const logo = $("input[name='logo']");
            const id = $("input[name='id']");
            const judul = $("input[name='judul']");
            const deskripsi = $("textarea[name='deskripsi']");
            const kontak_email = $("input[name='kontak_email']");
            const kontak_telepon = $("input[name='kontak_telepon']");
            const alamat = $("textarea[name='alamat']");

            logo.change(function(event) {
                const reader = new FileReader();
                reader.onload = function() {
                    photo_logo.attr('src', reader.result);
                };
                reader.readAsDataURL(event.target.files[0]);
            });

            $("#simpan").click(function() {
                const formData = new FormData();
                formData.append('id', id.val());
                formData.append('logo', logo[0].files[0]);
                formData.append('judul', judul.val());
                formData.append('deskripsi', deskripsi.val());
                formData.append('kontak_email', kontak_email.val());
                formData.append('kontak_telepon', kontak_telepon.val());
                formData.append('alamat', alamat.val());

                $.ajax({
                    url: "{{ route('settings.tentang.update') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: res.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        $("#judul_tentang").text(judul.val());
                    },
                    error: function(err) {
                        console.log(err.responseText);
                    },
                });
            });
        });
    </script>
@endsection

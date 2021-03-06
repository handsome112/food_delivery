@extends('layouts.admin.app')

@section('title','Update campaign')

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-edit"></i> {{__('messages.update')}} {{__('messages.campaign')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{route('admin.campaign.update-basic',[$campaign['id']])}}" method="post" id=campaign-form
                      enctype="multipart/form-data">
                      @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label" for="title">{{__('messages.title')}}</label>
                                <input type="text" name="title" class="form-control" placeholder="New campaign" value ="{{$campaign->title}}" required>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="input-label" for="title">{{__('messages.start')}} {{__('messages.date')}}</label>
                                        <input type="date" id="date_from" class="form-control" required name="start_date" value="{{$campaign->start_date->format('Y-m-d')}}"> 
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="input-label" for="title">{{__('messages.end')}} {{__('messages.date')}}</label>
                                    <input type="date" id="date_to" class="form-control" required="" name="end_date" value="{{$campaign->end_date->format('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="input-label text-capitalize" for="title">{{__('messages.daily')}} {{__('messages.start')}} {{__('messages.time')}}</label>
                                        <input type="time" id="start_time" class="form-control" name="start_time" value="{{$campaign->start_time}}">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="input-label text-capitalize" for="title">{{__('messages.daily')}} {{__('messages.end')}} {{__('messages.time')}}</label>
                                    <input type="time" id="end_time" class="form-control" name="end_time" value="{{$campaign->end_time}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="input-label" for="description">{{__('messages.description')}}<small style="color: red">* (maximum 255 characters)</small></label>
                                <textarea type="text" name="description" class="form-control" placeholder="About the campaign..." maxlength="255">{{$campaign->description}}</textarea>
                            </div>
                            <div class="form-group">
                                <label>{{__('messages.campaign')}} {{__('messages.image')}}</label>
                                <small style="color: red">* ( {{__('messages.ratio')}} 3:1 )</small>
                                <div class="custom-file">
                                    <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                           accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    <label class="custom-file-label" for="customFileEg1">{{__('messages.choose')}} {{__('messages.file')}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-6" style="margin-top: auto;margin-bottom: auto;">
                            <div class="form-group" style="margin-bottom:0%;">
                                <center>
                                    <img style="width: 80%;border: 1px solid; border-radius: 10px;" id="viewer"
                                         src="{{asset('storage/app/public/campaign')}}/{{$campaign->image}}" alt="campaign image"/>
                                </center>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">{{__('messages.update')}}</button>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        $("#date_from").on("change", function () {
            $('#date_to').attr('min',$(this).val());
        });

        $("#date_to").on("change", function () {
            $('#date_from').attr('max',$(this).val());
        });
        $(document).ready(function(){
            $('#date_from').attr('max','{{$campaign->end_date->format("Y-m-d")}}');
            $('#date_to').attr('min','{{$campaign->start_date->format("Y-m-d")}}');
        });
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });

        function show_item(type) {
            if (type === 'product') {
                $("#type-product").show();
                $("#type-category").hide();
            } else {
                $("#type-product").hide();
                $("#type-category").show();
            }
        }
    
        $('#campaign-form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.campaign.update-basic',[$campaign['id']])}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('Campaign updated successfully!', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route('admin.campaign.list', 'basic')}}';
                        }, 2000);
                    }
                }
            });
        });
    </script>
    
@endpush

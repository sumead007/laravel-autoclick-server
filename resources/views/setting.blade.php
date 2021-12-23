@extends('layouts.app')

@section('content')
    <style>
        /* Chat containers */
        .container1 {
            border: 2px solid #dedede;
            background-color: #f1f1f1;
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0;
        }

        /* Darker chat container */
        .darker {
            border-color: #ccc;
            background-color: #ddd;
        }

        /* Clear floats */
        .container1::after {
            content: "";
            clear: both;
            display: table;
        }

        /* Style images */
        .container1 img {
            float: left;
            max-width: 150px;
            width: 100%;
            margin-right: 20px;
            /* border-radius: 50%; */
        }

        /* Style the right image */
        .container1 img.right {
            float: right;
            margin-left: 20px;
            margin-right: 0;
        }

        /* Style time text */
        .time-right {
            float: right;
            color: #aaa;
        }

        /* Style time text */
        .time-left {
            float: left;
            color: #999;
        }

    </style>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('สถานะ: ') }}
                        @if ($data->status == 1)
                            <span class="text-success" id="text_status">เปิดใช้งาน</span>
                        @else
                            <span class="text-danger" id="text_status">ปิดใช้งาน</span>
                        @endif
                        <br>
                        {{ __('เหตุการณ์:') }}
                        @if ($data->action == 0)
                            <span class="text-dark" id="text_action">เริ่มต้นหรือยังไม่ได้ล็อกอิน</span>
                        @elseif($data->action== 1)
                            <span class="text-info" id="text_action">กำลังรอ OTP</span>
                        @elseif($data->action== 2)
                            <span class="text-success" id="text_action">กำลังทำงาน</span>
                        @elseif($data->action== 3)
                            <span class="text-danger" id="text_action">จบการทำงาน</span>
                        @elseif($data->action== 4)
                            <span class="text-danger" id="text_action">ล็อกอินไม่สำเร็จ</span>
                        @endif
                        <div align="right">
                            @if ($data->status == 1)
                                <a href="javascript:void(0)" onclick="config(this)" data-status="1"
                                    class="btn btn-danger">ปิดใช้งาน</a>
                            @else
                                <a href="javascript:void(0)" onclick="config(this)" data-status="0"
                                    class="btn btn-primary">เปิดใช้งาน</a>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form id="form_first">
                            <div class="form-group">
                                <label for="user_login">รหัสที่ใช้ Login :</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" name="user_login" id="user_login" disabled
                                        value='{{ $data->user_login }}'>
                                    <span id="user_loginError" class="alert-message text-danger"></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password">รหัสผ่าน</label>
                                <div class="col-sm-12">
                                    <input type="password" class="form-control" name="password" id="password" disabled
                                        value='{{ $data->password }}'>
                                    <span id="passwordError" class="alert-message text-danger"></span>
                                </div>
                            </div>
                        </form>
                        <div align="right">
                            <a href="javascript:void(0)" onclick="edit_btn(this)" id="btn_edit"
                                class="btn btn-warning">แก้ไข</a>
                            <a href="javascript:void(0)" onclick="cancel_btn(this)" id="btn_cancel" hidden
                                class="btn btn-danger">ยกเลิก</a>
                            <a href="javascript:void(0)" onclick="save_btn(this)" id="btn_save" hidden
                                class="btn btn-success">บันทึก</a>
                        </div>

                    </div>
                </div>
                <br>

                <div class="card">
                    <div class="card-header">{{ __('ภาพยืนยันตัวตน') }}
                    </div>
                    <div class="card-body">
                        <img src="{{ asset($data->image_screen_shot) }}" alt="" width="100%">
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        let t = setInterval(reload, 10000);

        function reload() {
            let status = $("#text_status").html()
            if (status == "เปิดใช้งาน") {
                location.reload();
            } else {
                clearInterval(t);
            }
        }

        function config(val) {
            var status = $(val).attr('data-status');
            // console.log(status);

            Swal.fire({
                title: 'คุณแน่ใจใช่หรือไม่?',
                text: "คุณต้องการเปลี่ยนสถานะใช่หรือไม่?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ตกลง',
                cancelButtonText: "ยกเลิก"
            }).then((result) => {
                if (result.isConfirmed) {
                    let _url = "{{ route('user.configs.update_status') }}";
                    $.ajax({
                        url: _url,
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            status: status,
                        },
                        success: function(res) {
                            // console.log(res);
                            if (res) {
                                $("#btn_edit").attr('hidden', !true);
                                $("#btn_save").attr('hidden', !false);
                                $("#btn_cancel").attr('hidden', !false);

                                $("#user_login").attr('disabled', !false);
                                $("#password").attr('disabled', !false);
                                $(val).attr('data-status', res.data);
                                if (res.data == 0) {
                                    $(val).attr('data-status', res.data);
                                    $(val).attr('class', 'btn btn-primary');
                                    $(val).html('เปิดใช้งาน');

                                    $("#text_status").html('ปิดใช้งาน');
                                    $("#text_status").attr('class', 'text-danger');
                                } else {
                                    $(val).attr('data-status', res.data);
                                    $(val).attr('class', 'btn btn-danger');
                                    $(val).html('ปิดใช้งาน');
                                    $("#text_status").html('เปิดใช้งาน');
                                    $("#text_status").attr('class', 'text-success');
                                }

                                $("#text_action").html('กำลังดำเนินการ...');
                                $("#text_action").attr('class', 'text-Secondary');
                                setTimeout(continueExecution, 30000)

                                Swal.fire(
                                    'สำเร็จ!',
                                    res.message,
                                    'success'
                                )


                            }
                        },
                        error: function(err) {
                            Swal.fire(
                                'สำเร็จ!',
                                'มีข้อผิดพลาดบางอย่างกรุณาลองใหม่อีกครั้ง',
                                'error'
                            )
                        }
                    });
                }
            })
        }

        function continueExecution() {
            // console.log("test");
            location.reload();
        }



        function edit_btn(val) {
            $("#btn_edit").attr('hidden', true);
            $("#btn_save").attr('hidden', false);
            $("#btn_cancel").attr('hidden', false);

            $("#user_login").attr('disabled', false);
            $("#password").attr('disabled', false);
        }

        function cancel_btn() {
            Swal.fire({
                title: 'คุณแน่ใจใช่หรือไม่?',
                text: "คุณต้องการบันทีกใช่หรือไม่?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ตกลง',
                cancelButtonText: "ยกเลิก"
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#btn_edit").attr('hidden', !true);
                    $("#btn_save").attr('hidden', !false);
                    $("#btn_cancel").attr('hidden', !false);

                    $("#user_login").attr('disabled', !false);
                    $("#password").attr('disabled', !false);
                }
            })
        }

        function clear_ms_error() {
            $('#user_loginError').text("");
            $('#passwordError').text("");
        }

        function save_btn() {
            Swal.fire({
                title: 'คุณแน่ใจใช่หรือไม่?',
                text: "คุณต้องการบันทีกใช่หรือไม่?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ตกลง',
                cancelButtonText: "ยกเลิก"
            }).then((result) => {
                if (result.isConfirmed) {
                    var form = $('#form_first')[0];
                    var data = new FormData(form);
                    let _url = "{{ route('user.configs.store') }}";
                    $.ajax({
                        url: _url,
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        enctype: 'multipart/form-data',
                        processData: false, // Important!
                        contentType: false,
                        cache: false,
                        timeout: 600000,
                        data: data,
                        success: function(res) {
                            console.log(res);
                            if (res.code == 200) {
                                $("#btn_edit").attr('hidden', !true);
                                $("#btn_save").attr('hidden', !false);
                                $("#btn_cancel").attr('hidden', !false);

                                $("#user_login").attr('disabled', !false);
                                $("#password").attr('disabled', !false);
                                Swal.fire(
                                    'สำเร็จ!',
                                    res.message,
                                    'success'
                                )
                            }
                        },
                        error: function(err) {
                            Swal.fire(
                                'สำเร็จ!',
                                'มีข้อผิดพลาดบางอย่างกรุณาลองใหม่อีกครั้ง หรือกรุณาปิดการใช้งานก่อนบันทึก',
                                'error'
                            )
                            clear_ms_error();
                            $('#user_loginError').text(err.responseJSON.errors.user_login);
                            $('#passwordError').text(err.responseJSON.errors.password);

                        }
                    });
                }
            })

        }
    </script>
@endsection

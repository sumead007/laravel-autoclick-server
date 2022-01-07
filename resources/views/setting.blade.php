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
                        <br>
                        ส่งสำเร็จ: {{$sum_sent_success}} คน

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
                        <h4>ข้อมูลเข้าใข้ไลน์</h4> <br>
                        <div class="d-flex justify-content-between m-1">
                            <div>
                                <a href="javascript:void(0)" class="btn btn-success" id="select_all"
                                    onclick="select_all()">เลือกทั้งหมด</a>
                                <a href="javascript:void(0)" class="btn btn-info" id="reset_select"
                                    onclick="reset_select()">รีเซต</a>
                            </div>
                            <div>
                                <a href="javascript:void(0)" class="btn btn-success" onclick="addPost()">
                                    เพิ่มข้อมูล
                                </a>
                            </div>
                        </div>
                        <form id="form_second">
                            <div class="table-responsive">
                                <table class="table text-nowrap p-0 " id="table_crud">
                                    <thead class="thead-dark">
                                        <tr align="center">
                                            <th id="th_choese">เลือก</th>
                                            <th scope="col">ลำดับ</th>
                                            <th scope="col">ไอดีที่เข้าใช้งาน</th>
                                            <th scope="col">สถานะ</th>
                                            <th scope="col">จำนวนที่แอต</th>
                                            <th scope="col">จำนวนแชทที่ส่ง</th>
                                            <th scope="col">ชื่อเครื่อง</th>
                                            <th scope="col">ยืนยัน OTP</th>
                                            <th scope="col">บันทึกเมื่อ</th>
                                            <th scope="col">อื่นๆ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($datas2 as $user)
                                            <tr align="center" id="row_{{ $user->id }}">
                                                <th id="td_choese" class="align-middle">
                                                    <div align="center">
                                                        <input type="checkbox" class="form-check" name="select[]"
                                                            data-cusm_id="{{ $user->id }}" id="select_input"
                                                            value="{{ $user->id }}">
                                                    </div>
                                                </th>
                                                <td class="align-middle">
                                                    {{ $datas2->firstitem() + $loop->index }}
                                                </td>
                                                <td class="align-middle">
                                                    {{ $user->user_login }}
                                                </td>
                                                <td class="align-middle">
                                                    @if ($user->status == 0)
                                                        <b class="text-black-50">ไม่ถูกใช้งาน</b>
                                                    @elseif($user->status == 1)
                                                        <b class="text-success">กำลังถูกใช้งาน</b>
                                                    @else
                                                        <b class="text-danger">โดนบล็อก</b>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    {{ $user->num_add }}
                                                </td>
                                                <td class="align-middle">
                                                    {{ $user->num_chat }}
                                                </td>
                                                <td class="align-middle">
                                                    {{ $user->machine }}
                                                </td>
                                                <td class="align-middle">
                                                    @if ($user->otp == 2)
                                                        <b class="text-success">ยืนยันแล้ว</b>
                                                    @else
                                                        <a href="javascript:void(0)" class="btn btn-success"
                                                            onclick="accpt_otp(@json($user->id))" id='btn_edit'>ยืนยัน
                                                            OTP</a>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    {{ Carbon\Carbon::parse($user->created_at)->locale('th')->diffForHumans() }}
                                                </td>

                                                <td class="align-middle" align="center">
                                                    <a href="javascript:void(0)" class="btn btn-warning"
                                                        onclick="editPost(@json($user->id))" id='btn_edit'>แก้ไข</a>
                                                    <a href="javascript:void(0)" class="btn btn-danger"
                                                        onclick="deletePost(@json($user->id))" id='btn_delete'>ลบ</a>
                                                </td>
                                            </tr>

                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div align="right">
                                <a href="javascript:void(0)" onclick="save_btn_select_login(0)"
                                    class="btn btn-danger">ยกเลิกที่เลือก</a>
                                <a href="javascript:void(0)" onclick="save_btn_select_login(1)"
                                    class="btn btn-success">ใช้งานที่เลือก</a>
                            </div>
                        </form>
                        <div class="d-flex justify-content-center">
                            {!! $datas2->links() !!}
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

    {{-- modal store --}}
    <div class="modal fade" id="post-modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="text_addcus">เพิ่มรายชื่อ</h4>
                </div>
                <div class="modal-body">
                    <form id="form_first">
                        <input type="hidden" name="post_id" id="post_id">

                        <div class="form-group">
                            <label for="user_login">รหัสที่ใช้ Login :</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="user_login" id="user_login">
                                <span id="user_loginError" class="alert-message text-danger"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password">รหัสผ่าน</label>
                            <div class="col-sm-12">
                                <input type="password" class="form-control" name="password" id="password">
                                <span id="passwordError" class="alert-message text-danger"></span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-outline-danger mr-auto" id="btn_user_status"
                        onclick="user_status(event.target)">ปิดบัญชีนี้</button> --}}
                    <button type="button" class="btn btn-primary" onclick="createPost()">บันทึก</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = async function(e) {
            // let status = JSON.parse("{{ json_encode($chk_datas3) }}");
            // let real_data = JSON.parse("{{ json_encode($real_data) }}");
            let status = {!! json_encode($chk_datas3) !!};
            let real_data = {!! json_encode($real_data) !!};
            // console.log(real_data);
            if (status > 0) {

                setTimeout(function() {
                    window.location.reload(1);
                }, 5000);
                Swal.fire({
                    title: 'กรุณายืนยัน OTP ก่อนใช้งาน!!',
                    text: 'ของไอดีชื่อ: ' + real_data.user_login,
                    imageUrl: '{{ asset($data->image_screen_shot2) }}',
                    imageWidth: 400,
                    imageHeight: 200,
                    imageAlt: 'Custom image',
                    allowOutsideClick: false,
                    confirmButtonText: 'ยกเลิกOTP',
                    confirmButtonColor: '#d33',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // console.log("accept");
                        let _url = "/update_status_otp/" + real_data.id;
                        $.ajax({
                            url: _url,
                            type: "POST",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            timeout: 600000,
                            data: {
                                "otp": 0,
                            },
                            success: function(res) {
                                // console.log(res);
                                window.location.reload(1);

                            },
                            error: function(err) {
                                Swal.fire(
                                    'มีข้อผิดพลาด!',
                                    err.responseJSON.message,
                                    'error'
                                )
                                // clear_ms_error();
                                // $('#user_loginError').text(err.responseJSON.errors.user_login);
                                // $('#passwordError').text(err.responseJSON.errors.password);

                            }
                        });
                    }
                })
            }

        }

        function accpt_otp(id) {
            let _url = "/update_status_otp/" + id;
            $.ajax({
                url: _url,
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                timeout: 600000,
                data: {
                    "otp": 1,
                },
                success: function(res) {
                    // console.log(res);
                    window.location.reload(1);

                    // Swal.fire(
                    //     'สำเร็จ!',
                    //     'บันทึกข้อมูลสำเร็จ',
                    //     'success'
                    // ).then(function() {

                    // })
                },
                error: function(err) {
                    Swal.fire(
                        'มีข้อผิดพลาด!',
                        err.responseJSON.message,
                        'error'
                    )
                    // clear_ms_error();
                    // $('#user_loginError').text(err.responseJSON.errors.user_login);
                    // $('#passwordError').text(err.responseJSON.errors.password);

                }
            });
        }

        function save_btn_select_login(val) {
            // console.log("test");
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
                    var form = $('#form_second')[0];
                    var data = new FormData(form);
                    data.append("status", val);

                    let _url = "{{ route('user.select_user_login.store') }}";
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
                            var status = res.data.status == 1 ?
                                '<b class="text-success">กำลังถูกใช้งาน</b>' :
                                '<b class="text-black-50">ไม่ถูกใช้งาน</b>'

                            $.each(res.data.id, function(i, item) {
                                $("#table_crud #row_" + item + " td:nth-child(4)").html(status);
                            });
                            Swal.fire(
                                'สำเร็จ!',
                                'บันทึกข้อมูลสำเร็จ',
                                'success'
                            )
                            reset_select()
                        },
                        error: function(err) {
                            Swal.fire(
                                'มีข้อผิดพลาด!',
                                'มีข้อผิดพลาดบางอย่างกรุณาลองใหม่อีกครั้ง หรือกรุณาปิดการใช้งานก่อนบันทึก',
                                'error'
                            )
                            // clear_ms_error();
                            // $('#user_loginError').text(err.responseJSON.errors.user_login);
                            // $('#passwordError').text(err.responseJSON.errors.password);

                        }
                    });
                }
            })

        }

        function select_all() {
            $("[id='select_input']").prop('checked', true);
        }

        function reset_select() {
            $("[id='select_input']").prop('checked', false);
        }

        function createPost() {
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
                    var id = $("#post_id").val();
                    let _url = "{{ route('user.configs.store_line_login') }}";
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
                            if (res) {
                                // let status = "";
                                // if(res.data.status == "0"){
                                //     status = '<b class="text-black-50">ไม่ถูกใช้งาน</b>';
                                // }else if(res.data.status == "1"){
                                //     status = '<b class="text-success">กำลังถูกใช้งาน</b>'
                                // }else{
                                //     status = '<b class="text-danger">โดนบล็อก</b>'
                                // }

                                if (id != '') {
                                    $("#table_crud #row_" + id + " td:nth-child(3)").html(res.data
                                        .user_login);
                                } else {

                                    $("#table_crud").prepend(
                                        `
                                            <tr align="center" id="row_${res.data.id}">
                                            <th id="td_choese" class="align-middle">
                                                <div align="center">
                                                    <input type="checkbox" class="form-check" name="select[]"
                                                        data-cusm_id="${res.data.id}" id="select_input"
                                                        value="${res.data.id}">
                                                </div>
                                            </th>
                                            <td class="align-middle">
                                                <i class="text-success">new</i>
                                            </td>
                                            <td class="align-middle">
                                                ${res.data.user_login}
                                            </td>
                                            <td class="align-middle">
                                                <b class="text-black-50">ไม่ถูกใช้งาน</b>
                                            </td>
                                            <td class="align-middle">
                                               0
                                            </td>
                                            <td class="align-middle">
                                                0
                                            </td>
                                            <td class="align-middle">
                                                
                                            </td>
                                            <td class="align-middle">
                                                <a href="javascript:void(0)" class="btn btn-success"
                                                            onclick="accpt_otp(${res.data.id})" id='btn_edit'>ยืนยัน
                                                            OTP</a>
                                            </td>
                                            <td class="align-middle">
                                                ${res.data.created_at_2}
                                            </td>
                                            <td class="align-middle" align="center">
                                                <a href="javascript:void(0)" class="btn btn-warning"
                                                    onclick="editPost(${res.data.id})" id='btn_edit'>แก้ไข</a>
                                                <a href="javascript:void(0)" class="btn btn-danger"
                                                    onclick="deletePost(${res.data.id})" id='btn_delete'>ลบ</a>
                                            </td>
                                        </tr>
                                        `
                                    );
                                }
                                $('#post-modal').modal('hide');

                                Swal.fire(
                                    'สำเร็จ!',
                                    res.message,
                                    'success'
                                )
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            Swal.fire(
                                'มีข้อผิดพลาด!',
                                err.responseJSON.message,
                                'error'
                            )
                            clear_ms_error();
                            $('#passwordError').text(err.responseJSON.errors.password);
                            $('#user_loginError').text(err.responseJSON.errors.user_login);
                        }
                    });
                }
            })
        }

        function editPost(pass_id) {
            clear_ms_error();
            var id = pass_id;
            let _url = "/user/get_api/get_addline_login/" + id;
            $("#text_addcus").html("แก้ไขรายชื่อ");
            $("#form_first")[0].reset();
            $.ajax({
                url: _url,
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res) {
                        $("#post_id").val(res.id);
                        $("#user_login").val(res.user_login);
                        $("#password").val(res.password);
                        $('#post-modal').modal('show');

                    }
                }
            });
        }

        function deletePost(pass_id) {
            Swal.fire({
                title: 'คูณแน่ใจใช่หรือไม่?',
                text: "คุณต้องการลบข้อมูลใช่หรือไม่?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ตกลง',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    var id = pass_id;
                    let _url = "/user/configs/delete/" + id;
                    let _token = $('meta[name="csrf-token"]').attr('content');

                    $.ajax({
                        url: _url,
                        type: "DELETE",
                        data: {
                            _token: _token,
                        },
                        success: function(res) {

                            if (res.code == '200') {
                                $("#row_" + id).remove();
                                Swal.fire(
                                    'สำเร็จ!',
                                    'ข้อมูลถูกลบเรียบร้อยแล้ว',
                                    'success'
                                )
                            } else {
                                Swal.fire(
                                    'ไม่สำเร็จ!',
                                    res.error,
                                    'error'
                                )
                            }

                        },
                        error: function(err) {
                            Swal.fire(
                                'มีข้อผิดพลาด!',
                                err.responseJSON.message,
                                'error'
                            )

                        }

                    });
                }
            })

        }

        function clear_ms_error() {
            $('#passwordError').text("");
            $('#user_loginError').text("");
        }

        function addPost() {
            $('#post-modal').modal('show');
            $("#form_first")[0].reset();
        }

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
                                'มีข้อผิดพลาด!',
                                err.responseJSON.message,
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

@extends('layouts.app')

@section('content')
    <style>
        .progress {
            display: block;
            text-align: center;
            width: 0;
            height: 5px;
            background: skyblue;
            transition: width .3s;
        }

        .progress.hide {
            opacity: 0;
            transition: opacity 1.3s;
        }

    </style>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('ข้อมูล Line') }}</div>
                    <div class="progress"></div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div align="right">
                            <a href="javascript:void(0)" class="btn btn-success" onclick="btn_upload_excel()">
                                นำเข้าจากไฟล์ excel
                            </a>
                            <a href="javascript:void(0)" class="btn btn-primary" onclick="addPost()">
                                เพิ่มข้อมูล
                            </a>
                        </div>
                        <form id="upload_excel" name="upload_excel">
                            <input id='fileid' name="fileid" type='file' accept=".csv, .xlsx" hidden />
                        </form>
                        <br>
                        <div class="table-responsive-md">
                            <table class="table  text-nowrap p-0 " id="table_crud">
                                <thead class="thead-dark">
                                    <tr align="center">
                                        <th id="th_choese" hidden>เลือก</th>
                                        <th scope="col">ลำดับ</th>
                                        <th scope="col">ID</th>
                                        <th scope="col">เบอร์โทร</th>
                                        <th scope="col">แอตจาก</th>
                                        <th scope="col">บันทึกเมื่อ</th>
                                        <th scope="col">อื่นๆ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $user)
                                        <tr align="center" id="row_{{ $user->id }}">
                                            <th id="td_choese" class="align-middle" hidden>
                                                <div align="center">
                                                    <input type="checkbox" class="form-check" name="select"
                                                        data-cusm_id="{{ $user->id }}" id="select_input"
                                                        value="{{ $user->id }}">
                                                </div>
                                            </th>
                                            <td class="align-middle">
                                                {{ $datas->firstitem() + $loop->index }}
                                            </td>
                                            <td class="align-middle">
                                                {{ $user->user_id }}
                                            </td>
                                            <td class="align-middle">
                                                {{ $user->user_tel }}
                                            </td>
                                            <td class="align-middle">
                                                @if ($user->type == 0)
                                                    ID LINE
                                                @else
                                                    เบอร์โทร
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
                        <div class="d-flex justify-content-center">
                            {!! $datas->links() !!}
                        </div>
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
                    <form name="form_second" id="form_second" class="form-horizontal">
                        <input type="hidden" name="post_id" id="post_id">
                        <div class="form-group">
                            <label for="type">แอตจาก</label>
                            <div class="col-sm-12">
                                <select name="type" id="type" class="form-control">
                                    <option value="" disabled selected>กรุณาเลือกประเภทที่จะแอตLine</option>
                                    <option value="0">ID</option>
                                    <option value="1">เบอร์โทร</option>
                                </select>
                                <span id="typeError" class="alert-message text-danger"></span>
                            </div>
                        </div>

                        <div class="form-group" id="f-image">
                            <label for="id">ID <span class="text-danger"> <i>*ไม่มีไม่ต้องกรอก</i></span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="id" name="id"
                                    placeholder="กรุณากรอก ID (ไม่มีให้ใส่ -)" required>
                                <span id="idError" class="alert-message text-danger"></span>
                            </div>
                        </div>

                        <div class="form-group" id="f-text">
                            <label for="phone">เบอร์โทร <span class="text-danger">
                                    <i>*ไม่มีไม่ต้องกรอก</i></span></label>
                            <div class="col-sm-12">
                                <input type="number" class="form-control" id="phone" name="phone"
                                    placeholder="กรุณากรอกชื่อเบอร์โทร (ไม่มีให้ใส่ -)" required>
                                <span id="phoneError" class="alert-message text-danger"></span>
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
        function btn_upload_excel() {
            document.getElementById('fileid').click();
        }
        document.getElementById('fileid').addEventListener('change', submitForm);

        function submitForm() {
            Swal.fire({
                title: 'คูณแน่ใจใช่หรือไม่?',
                text: "คุณต้องการนำเข้าข้อมูลใช่หรือไม่?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ตกลง',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // document.getElementById('formid').submit();
                    let _token = $('meta[name="csrf-token"]').attr('content');

                    var form = $('#upload_excel')[0];
                    var data = new FormData(form);
                    let _url = "{{ route('file-import') }}";
                    $.ajax({
                        xhr: function() {
                            var xhr = new window.XMLHttpRequest();
                            xhr.upload.addEventListener("progress", function(evt) {
                                if (evt.lengthComputable) {
                                    var percentComplete = evt.loaded / evt.total;
                                    // console.log(percentComplete);
                                    $('.progress').css({
                                        width: percentComplete * 100 + '%'
                                    });
                                    if (percentComplete === 1) {
                                        $('.progress').addClass('hide');
                                    }
                                }
                            }, false);
                            xhr.addEventListener("progress", function(evt) {
                                if (evt.lengthComputable) {
                                    var percentComplete = evt.loaded / evt.total;
                                    console.log(percentComplete);
                                    $('.progress').css({
                                        width: percentComplete * 100 + '%'
                                    });
                                }
                            }, false);
                            return xhr;
                        },
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
                            // console.log(res);
                            Swal.fire(
                                'สำเร็จ!',
                                res.sucess,
                                'success'
                            ).then(function() {
                                location.reload();
                            })
                        },
                        error: function(err) {
                            let message = "";
                            if (err.responseJSON.message != null) {
                                message = err.responseJSON.message
                            } else if (err.responseJSON.errors.fileid[0] != null) {
                                message = err.responseJSON.errors.fileid[0]
                            } else {
                                message = "มีข้อผิดพลาดกรุณาลองรีเฟชหน้าจออีกครั้ง"
                            }

                            Swal.fire(
                                'เกิดข้อผิดพลาด!',
                                message,
                                'error'
                            )
                        }
                    });
                }
            })

        }

        function editPost(pass_id) {
            clear_ms_error();
            var id = pass_id;
            let _url = "/user/get_api/get_addline/" + id;
            $("#text_addcus").html("แก้ไขรายชื่อ");
            $("#form_second")[0].reset();
            $.ajax({
                url: _url,
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    // console.log(res);
                    if (res) {
                        $("#post_id").val(res.id);
                        $("#type").val(res.type);
                        $("#id").val(res.user_id);
                        $("#phone").val(res.user_tel);
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
                    let _url = "/user/addline/delete/" + id;
                    let _token = $('meta[name="csrf-token"]').attr('content');

                    $.ajax({
                        url: _url,
                        type: "DELETE",
                        data: {
                            _token: _token,
                        },
                        success: function(res) {
                            // console.log(res);
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

        function addPost() {
            $('#type').removeAttr('style');
            $('#post-modal').modal('show');
            $("#form_second")[0].reset();
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
                    var form = $('#form_second')[0];
                    var data = new FormData(form);
                    var id = $("#post_id").val();
                    let _url = "{{ route('user.addline.store') }}";
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
                            // console.log(res);
                            if (res) {
                                let status = res.data.type == "0" ? 'ID LINE' : 'เบอร์โทร'
                                let user_id = res.data.user_id == null ? "" : res.data.user_id;
                                let user_tel = res.data.user_tel == null ? "" : res.data.user_tel;

                                if (id != '') {
                                    $("#table_crud #row_" + id + " td:nth-child(3)").html(user_id);
                                    $("#table_crud #row_" + id + " td:nth-child(4)").html(user_tel);
                                    $("#table_crud #row_" + id + " td:nth-child(5)").html(status);
                                } else {
                                    let user_id = res.data.user_id == null ? "" : res.data.user_id;
                                    let user_tel = res.data.user_tel == null ? "" : res.data.user_tel;
                                    $("#table_crud").prepend(
                                        `
                                            <tr align="center" id="row_${res.data.id}">
                                            <th id="td_choese" class="align-middle" hidden>
                                                <div align="center">
                                                    <input type="checkbox" class="form-check" name="select"
                                                        data-cusm_id="${res.data.id}" id="select_input"
                                                        value="${res.data.id}">
                                                </div>
                                            </th>
                                            <td class="align-middle">
                                                <i class="text-success">new</i>
                                            </td>
                                            <td class="align-middle">
                                                ${user_id}
                                            </td>
                                            <td class="align-middle">
                                                ${user_tel}
                                            </td>
                                            <td class="align-middle">
                                                ${status}
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
                            Swal.fire(
                                'มีข้อผิดพลาด!',
                                err.responseJSON.message,
                                'error'
                            )
                            clear_ms_error();
                            $('#typeError').text(err.responseJSON.errors.type);
                            $('#idError').text(err.responseJSON.errors.id);
                            $('#phoneError').text(err.responseJSON.errors.phone);

                        }
                    });
                }
            })
        }

        function clear_ms_error() {
            $('#typeError').text("");
            $('#idError').text("");
            $('#phoneError').text("");
        }
    </script>
@endsection

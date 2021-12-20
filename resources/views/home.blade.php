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
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('ข้อมูลแชท') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <div align="right">
                            <a href="javascript:void(0)" class="btn btn-primary" onclick="addPost()">
                                เพิ่มข้อมูล
                            </a>

                        </div>
                        <form id="form_first">
                            <ol id="sortable">
                                @foreach ($datas as $data)
                                    <li class="ui-state-default" id="li_{{ $data->id }}">
                                        <div class="container1">
                                            <input type="hidden" name="id[]" value="{{ $data->id }}">
                                            @if ($data->type == 0)
                                                <img src="{{ asset($data->data) }}" alt="Avatar">
                                            @else
                                                <p>{{ $data->data }}</p>
                                            @endif
                                            <span class="time-right">
                                                บันทึกเมื่อ
                                                {{ Carbon\Carbon::parse($data->created_at)->locale('th')->diffForHumans() }}
                                                <a href="javascript:void(0)" onclick="editPost(@json($data->id))"
                                                    class="btn btn-warning">แก้ไข</a>
                                                <a href="javascript:void(0)" onclick="deletePost(@json($data->id))"
                                                    class="btn btn-danger">ลบ</a>
                                            </span>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </form>
                        <div align="right">
                            <a href="javascript:void(0)" onclick="save_btn()" class="btn btn-success">บันทึก</a>
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
                    <h4 class="modal-title" id="text_addcus">เพิ่มข้อมูล</h4>
                </div>
                <div class="modal-body">
                    <form name="form_second" id="form_second" class="form-horizontal">
                        <input type="hidden" name="post_id" id="post_id">
                        <div class="form-group">
                            <label for="type">เลือกประเภทข้อความ</label>
                            <div class="col-sm-12">
                                <select name="type" id="type" class="form-control" onchange="select_change(this)">
                                    <option value="" disabled selected>กรุณาเลือกประเภทข้อความ</option>
                                    <option value="0">รูป</option>
                                    <option value="1">ข้อความ</option>
                                </select>
                                <span id="typeError" class="alert-message text-danger"></span>
                            </div>
                        </div>

                        <div class="form-group" id="f-text" hidden>
                            <label for="text">ข้อความ</label>
                            <div class="col-sm-12">

                                <textarea class="form-control" name="text" id="text" cols="30" rows="10"
                                    placeholder="กรุณากรอกข้อความ" required></textarea>
                                <span id="textError" class="alert-message text-danger"></span>
                            </div>
                        </div>

                        <div class="form-group" id="f-image" hidden>
                            <label for="image">รูปภาพ</label>
                            <div class="col-sm-12">
                                <input type="file" class="form-control" id="image" name="image" required accept="image/*">
                                <span id="imageError" class="alert-message text-danger"></span>
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
        function editPost(pass_id) {

            $('#type').css('pointer-events', 'none');
            clear_ms_error();
            var id = pass_id;
            let _url = "/user/get_api/get_message/" + id;
            $("#text_addcus").html("แก้ไขข้อมูล");
            $("#form_first")[0].reset();
            $.ajax({
                url: _url,
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    console.log(res);
                    if (res) {
                        $("#post_id").val(res.id);
                        $("#type").val(res.type);
                        if (res.type == 0) {
                            $("#f-text").attr("hidden", true);
                            $("#f-image").attr("hidden", false);
                        } else {
                            $("#text").val(res.data);
                            $("#f-text").attr("hidden", false);
                            $("#f-image").attr("hidden", true);
                        }

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
                    let _url = "/user/delete/" + id;
                    let _token = $('meta[name="csrf-token"]').attr('content');

                    $.ajax({
                        url: _url,
                        type: "DELETE",
                        data: {
                            _token: _token,
                        },
                        success: function(res) {
                            console.log(res);
                            if (res.code == '200') {
                                $("#li_" + id).remove();
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

                        }

                    });
                }
            })

        }

        function select_change(dthis) {
            var status = $(dthis).val()
            // console.log(status);
            if (status == 0) {
                $("#f-text").attr("hidden", true);
                $("#f-image").attr("hidden", false);
            } else {
                $("#f-text").attr("hidden", false);
                $("#f-image").attr("hidden", true);
            }
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
                    let _url = "{{ route('user.store') }}";
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
                                if (id != '') {
                                    if (res.data.type == 0) {
                                        $("#li_" + id).find('img').replaceWith(
                                            "<img src='" + res.data.data + "' alt='Avatar'>"
                                        );
                                    } else {
                                        $("#li_" + id).find('p').replaceWith(
                                            "<p>" + res.data.data + "</p>"
                                        );
                                    }
                                } else {
                                    if (res.data.type == 0) {
                                        $("#sortable").append(
                                            "<li class='ui-state-default' id='li_" + res.data.id +
                                            "'>" +
                                            '<div class="container1">' +
                                            "<input type='hidden' name='id[]' value=" + res.data
                                            .id +
                                            ">" +
                                            "<img src=" + res.data.data2 + " alt='Avatar'>" +
                                            '<span class="time-right">' +
                                            res.data.created_at_2 +
                                            '</span>' +
                                            '</div>' +
                                            "</li>"
                                        );
                                    } else {
                                        $("#sortable").append(
                                            "<li class='ui-state-default' id='li_" + res.data.id +
                                            "'>" +
                                            '<div class="container1">' +
                                            "<input type='hidden' name='id[]' value=" + res.data
                                            .id +
                                            ">" +
                                            "<p>" + res.data.data + "</p>" +
                                            '<span class="time-right">' +
                                            res.data.created_at_2 +
                                            '</span>' +
                                            '</div>' +
                                            "</li>"
                                        );
                                    }
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
                            clear_ms_error();
                            $('#typeError').text(err.responseJSON.errors.type);
                            $('#textError').text(err.responseJSON.errors.text);
                            $('#imageError').text(err.responseJSON.errors.image);
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

        function clear_ms_error() {
            $('#typeError').text("");
            $('#textError').text("");
            $('#imageError').text("");
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
                    let _url = "{{ route('user.save_change') }}";
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
    </script>
@endsection

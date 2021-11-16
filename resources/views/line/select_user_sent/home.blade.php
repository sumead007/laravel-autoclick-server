@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('เลือกรายชื่อ Line ที่จะส่ง') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <div align="right">
                            {{-- <a href="javascript:void(0)" class="btn btn-primary" onclick="addPost()">
                                เพิ่มข้อมูล
                            </a> --}}

                            <a href="javascript:void(0)" class="btn btn-success" id="select_all"
                                onclick="select_all()">เลือกทั้งหมด</a>
                            <a href="javascript:void(0)" class="btn btn-info" id="reset_select"
                                onclick="reset_select()">รีเซต</a>
                        </div>
                        <br>
                        <form id="form_first">
                            <table class="table text-nowrap p-0" id="table_crud">
                                <thead class="thead-dark">
                                    <tr align="center">
                                        <th id="th_choese">เลือก</th>
                                        <th scope="col">ID</th>
                                        <th scope="col">ประเภท ID</th>
                                        <th scope="col">สถานะ</th>
                                        <th scope="col">บันทึกเมื่อ</th>
                                        {{-- <th scope="col">อื่นๆ</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $user)
                                        <tr align="center" id="row_{{ $user->id }}">
                                            <th id="td_choese" class="align-middle">
                                                <div align="center">
                                                    <input type="checkbox" class="form-check" name="select[]"
                                                        data-cusm_id="{{ $user->id }}" id="select_input"
                                                        value="{{ $user->id }}">
                                                </div>
                                            </th>
                                            <td class="align-middle">
                                                {{ $user->user_id }}
                                            </td>
                                            <td class="align-middle">
                                                @if ($user->type == 0)
                                                    ID LINE
                                                @else
                                                    เบอร์โทร
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if ($user->status == 1)
                                                    <p class="text-danger">ยังไม่อยู่ในคิว</p>
                                                @else
                                                    <p class="text-success">อยู่ในคิวแล้ว</p>
                                                @endif
                                            </td>

                                            <td class="align-middle">
                                                {{ Carbon\Carbon::parse($user->created_at)->locale('th')->diffForHumans() }}
                                            </td>

                                            {{-- <td class="align-middle" align="center">
                                            <a href="javascript:void(0)" class="btn btn-warning"
                                                onclick="editPost(@json($user->id))" id='btn_edit'>แก้ไข</a>
                                            <a href="javascript:void(0)" class="btn btn-danger"
                                                onclick="deletePost(@json($user->id))" id='btn_delete'>ลบ</a>
                                        </td> --}}
                                        </tr>

                                    @endforeach
                                </tbody>
                            </table>
                        </form>
                        {{-- <div class="d-flex justify-content-center">
                            {!! $datas->links() !!}
                        </div> --}}
                        <div align="right">
                            <a href="javascript:void(0)" onclick="save_btn(1)" class="btn btn-danger">ยกเลิกคิว</a>
                            <a href="javascript:void(0)" onclick="save_btn(0)" class="btn btn-success">จองคิว</a>
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
                            <label for="type">เลือกประเภทLine</label>
                            <div class="col-sm-12">
                                <select name="type" id="type" class="form-control" onchange="select_change(this)">
                                    <option value="" disabled selected>กรุณาเลือกประเภทLine</option>
                                    <option value="0">ID</option>
                                    <option value="1">เบอร์โทร</option>
                                </select>
                                <span id="typeError" class="alert-message text-danger"></span>
                            </div>
                        </div>

                        <div class="form-group" id="f-image" hidden>
                            <label for="id">ID</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="id" name="id" placeholder="กรุณากรอก ID"
                                    required>
                                <span id="idError" class="alert-message text-danger"></span>
                            </div>
                        </div>

                        <div class="form-group" id="f-text" hidden>
                            <label for="phone">เบอร์โทร</label>
                            <div class="col-sm-12">
                                <input type="number" class="form-control" id="phone" name="phone"
                                    placeholder="กรุณากรอกชื่อเบอร์โทร" required>
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
        function save_btn(val) {
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
                    data.append("status", val);
                    // console.log(val);

                    let _url = "{{ route('user.select_user_sent.store') }}";
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
                                "<p class='text-danger'>ยังไม่อยู่ในคิว</p>" :
                                "<p class='text-success'>อยู่ในคิวแล้ว</p>"

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
    </script>
@endsection

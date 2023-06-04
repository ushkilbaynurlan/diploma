@extends('layouts.admin')

@section('page-title')
    {{ __('Messenger') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Messenger') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-body chat-div-wrap p-1">
                    <div class="chat-wrap">
                        <div class="chat-user">
                            <div class="chat-persons scrollbar-inner">
                                <ul class="users">
                                    @foreach ($users as $user)
                                        <li class="user_chat" id="{{ $user->id }}">
                                            <div class="peroson">
                                                <img class="avatar-sm rounded-circle mr-3" style="width:40px;" src="{{asset(Storage::url('public\avatar.png'))}}" alt="{{$user->email}}">
                                                <div>
                                                    @if(!empty($user->name))
                                                        <h6 class="m-0 chat_user">{{ $user->name }}</h6>
                                                        <span class="text-muted text-small">{{$user->email}}</span>
                                                    @else
                                                        <h6 class="m-0 chat_user py-2">{{$user->email}}</h6>
                                                    @endif
                                                </div>
                                                @if($user->unread() > 0)
                                                    <div class="status">
                                                        <span class="badge badge-info pending">{{ $user->unread() }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="chat-screen">
                            <div class="chat-head">
                                <div class="peroson">
                                    <div class="float-left">
                                        <h6 class="m-0 chat_head mt-2">{{__('Please Select User')}}</h6>
                                    </div>
                                    <div class="float-right delete-user">

                                    </div>
                                </div>
                            </div>
                            <div class="chat-body scrollbar-inner message-wrapper">
                                <div class="messages">
                                    <div class="text-center pt-5">
                                        {{__('No Message Found..!')}}
                                    </div>
                                </div>
                            </div>
                            <div class="chat-footer" style="display: none;">
                                <div class="send-msg-box">
                                    <input type="text" value="" class="form-control send_msg" placeholder="{{__('Type Message here')}}" name="message"/><i class="ti ti-brand-telegram send_msg_btn"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var receiver_id = '';
        var my_id = 0;

        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Enable pusher logging - don't include this in production
            Pusher.logToConsole = false;

            var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                forceTLS: true
            });

            var channel = pusher.subscribe('my-channel');
            channel.bind('my-event', function (data) {
                /*alert(JSON.stringify(data));*/
                if (my_id == data.from) {
                    $('#' + data.to).click();
                } else if (my_id == data.to) {
                    if (receiver_id == data.from) {
                        // if receiver is selected, reload the selected user ...
                        $('#' + data.from).click();
                    } else {
                        // if receiver is not seleted, add notification for that user
                        var pending = parseInt($('#' + data.from + ' .peroson').find('.pending').html());
                        if (pending) {
                            $('#' + data.from + ' .peroson').find('.pending').html(pending + 1);
                        } else {
                            $('#' + data.from + ' .peroson').append(' <span class="badge badge-info pending">1</span>');
                        }
                    }
                }
            });

            $('.user_chat').click(function () {
                var name = $(this).find('.chat_user').html();
                $('.user_chat').removeClass('active-person');
                $(this).addClass('active-person');
                $(this).find('.pending').remove();

                receiver_id = $(this).attr('id');

                $.ajax({
                    type: "get",
                    url: "{{ url('/admin/message') }}" + '/' + receiver_id, // need to create this route
                    data: "",
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        // console.log(data.deletehtml);
                        $('.delete-user').html(data.deletehtml);
                        $('.messages').html(data.messagehtml);
                        $('.chat_head').text(name);

                        loadConfirm();
                        scrollToBottomFunc();
                        // alert('try');
                    }
                });
            });

            $(document).on('keyup', '.send-msg-box input', function (e) {
                var message = $(this).val();

                // check if enter key is pressed and message is not null also receiver is selected
                if (e.keyCode == 13 && message != '' && receiver_id != '') {

                    $(this).val(''); // while pressed enter text box will be empty

                    var datastr = "&receiver_id=" + receiver_id + "&message=" + message;
                    $.ajax({
                        type: "post",
                        url: "message", // need to create this post route
                        data: datastr,
                        cache: false,
                        success: function (data) {
                            loadConfirm();
                        },
                        error: function (jqXHR, status, err) {
                        },
                        complete: function () {
                            scrollToBottomFunc();
                        }
                    });
                }
            });

            $(document).on('click', '.send_msg_btn', function () {

                var message = $('.send_msg').val();
                // console.log(message);
                // check if enter key is pressed and message is not null also receiver is selected

                $('.send_msg').val(''); // while pressed enter text box will be empty

                var datastr = "&receiver_id=" + receiver_id + "&message=" + message;
                $.ajax({
                    type: "post",
                    url: "message", // need to create this post route
                    data: datastr,
                    cache: false,
                    success: function (data) {
                        loadConfirm();
                    },
                    error: function (jqXHR, status, err) {
                    },
                    complete: function () {
                        scrollToBottomFunc();
                    }
                });

            });

        });

        // make a function to scroll down auto
        function scrollToBottomFunc() {
            $('.message-wrapper').animate({
                scrollTop: $('.message-wrapper').get(0).scrollHeight
            }, 10);
        }

        $(".user_chat").click(function(){
            $(".chat-footer").removeAttr("style")
        });
    </script>
@endpush

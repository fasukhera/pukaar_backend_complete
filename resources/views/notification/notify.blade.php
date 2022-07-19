@extends('layouts.app')
@section('content')
    <div class="row chat-row justify-content-center">
        <div class="col-md-8">
            <div class="users">
                <h5>Users</h5>
                <ul class="list-group list-chat-item">
                    @if($users->count())
                        @foreach($users as $user)
                            <li class="notify-user-list
                                @if($user->id == $friendInfo->id) active @endif">
                                <a href="#">
                                    <div class="chat-image">
                                        {!! makeImageFromName($user->first_name) !!}
                                    </div>

                                    <div class="chat-name font-weight-bold">
                                        {{ $user->first_name }}
                                    </div></br></br>

                                    <div class="request">
                                    <button class="btn btn-small btn-primary" id="request" value="{{$user->id}}">Request For Chat</button>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
        <div classs="container p-5" id="notifications">
            
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(function (){
            let $container = $("#notifications");

            let user_id = "{{ auth()->user()->id }}";
            let ip_address = '127.0.0.1';
            let socket_port = '8005';
            let socket = io(ip_address + ':' + socket_port);
            let friendId = "{{ $friendInfo->id }}";

            socket.on('connect', function() {
                socket.emit('user_connected', user_id);
            });

            document.getElementById("request").onclick = function(userId) {
                sendNotification(userId);
                };

            function sendNotification(userId) {
                let url = "{{ route('notification.request-for-chat') }}";
                let form = $(this);
                let formData = new FormData();
                let token = "{{ csrf_token() }}";
                let sender_id = "{{ auth()->user()->id }}";

                formData.append('_token', token);
                formData.append('sender_id', sender_id);
                formData.append('reciever_id', friendId);

                $.ajax({
                   url: url,
                   type: 'POST',
                   data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'JSON',
                   success: function (response) {
                       if (response.success) {
                           console.log(response.success);
                       }
                   }
                });
            };

            /* appendNotificationToReciever(notification) {
                let $notifications = '<div class="alert alert-primary" role="alert" id="notification">\n' + notification.title + 
                '</div>\n'
                
                $notifications.append(notifications);
            }; */

            socket.on("notification-channel:App\\Events\\NotificationEvent", function (notification)
            {
                console.log(notification);
                alert(notification.data+':'+
                notification.sender_id+':'+
                notification.sender_name);
            });
        });
    </script>
@endpush
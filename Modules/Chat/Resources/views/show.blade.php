@extends('backEnd.master')
@section('title')
    Show
@endsection
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor" id="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="chat_main_wrapper">
                        <div class="chat_flow_list_wrapper ">
                            <div class="box_header">
                                <div class="main-title">
                                    <h3 class="m-0">Chat List</h3>
                                </div>
                                <a class="primary-btn radius_30px fix-gr-bg" href="{{url('admin/communication_new_chat')}}"><i class="ti-plus"></i>New Chat</a>
                            </div>
                            <div x-data="chatSidePanel({
                                settings: {{ json_encode(generalSetting()->only(['teacher_phone_view', 'teacher_email_view'])) }},
                                search_url: {{ json_encode(route('chat.user.search')) }},
                                single_chat_url: {{ json_encode(route('chat.index')) }},
                                chat_block_url: {{ json_encode(route('chat.user.block')) }},
                                create_group_url: {{ json_encode(route('chat.group.create')) }},
                                group_chat_show: {{ json_encode(route('chat.group.show')) }},
                                users: {{ json_encode($users) }},
                                groups: {{ json_encode($groups) }},
                                can_create_group: {{ json_encode(createGroupPermission()) }},
                                asset_type: {{ json_encode('/public') }}
                            })">
                                <div class="chat_flow_list crm_full_height">
                                    <div class="chat_flow_list_inner">
                                        <div class="serach_field_chat mb_30">
                                            <div class="search_inner">
                                                <form :action="searchUrl" method="GET">
                                                    <div class="search_field">
                                                        <input type="text" name="keywords" placeholder="Search" id="users_list_sidebar">
                                                    </div>
                                                    <button type="submit"> <i class="ti-search"></i> </button>
                                                </form>
                                            </div>
                                        </div>
                                        <ul style="list-style: none;">
                                            <template x-if="users.length > 0">
                                                <div>
                                                    <template x-for="(user, index) in users" :key="user.id">
                                                        <li>
                                                            <div class="single_list d-flex align-items-center">
                                                                <div class="thumb">
                                                                    <a @click.prevent="openUserProfileModal('profileEditForm'+index)" href="#">
                                                                        <img :src="getUserAvatar(user)" alt="" height="50" width="50">
                                                                    </a>
                                                                </div>
                                                                <div class="list_name">
                                                                    <a :href="singleChatUrl+'/'+user.id">
                                                                        <h4>
                                                                            <span x-text="user.first_name + ' ' + user.last_name"></span>
                                                                            <span :class="getStatusClass(user.active_status.status)"></span>
                                                                        </h4>
                                                                    </a>
                                                                    <p x-if="user.last_message" :id="'last_message'+index" x-text="stripHtml(user.last_message)"></p>
                                                                </div>
                                                            </div>
                                                            <div class="modal fade admin-query" :id="'profileEditForm'+index" aria-modal="true">
                                                                <div class="modal-dialog modal_800px modal-dialog-centered">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h4 class="modal-title">
                                                                                <div class="thumb" style="display: inline">
                                                                                    <a href="#"><img :src="getUserAvatar(user)" alt="" height="50" width="50"></a>
                                                                                </div>
                                                                                <span x-text="user.first_name + ' ' + user.last_name"></span>
                                                                            </h4>
                                                                            <button type="button" class="close" data-dismiss="modal"><i class="ti-close"></i></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div class="row">
                                                                                <div class="col-xl-6">
                                                                                    <div class="primary_input mb-25" x-show="canShowContact(user)">
                                                                                        <label class="primary_input_label">Username <span class="text-danger">*</span></label>
                                                                                        <input name="name" disabled class="primary_input_field name" placeholder="Name" :value="user.username" type="text">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-xl-6" x-show="canShowEmail(user)">
                                                                                    <div class="primary_input mb-25">
                                                                                        <label class="primary_input_label">Email <span class="text-danger">*</span></label>
                                                                                        <input name="email" class="primary_input_field name" disabled placeholder="Email" :value="user.email" type="email" readonly>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-xl-6">
                                                                                    <div class="primary_input mb-25" x-show="canShowPhone(user)">
                                                                                        <label class="primary_input_label">Phone</label>
                                                                                        <input name="username" class="primary_input_field name" disabled :value="user.phone" type="text" readonly>
                                                                                    </div>
                                                                                    <a x-if="user.blocked && user.blocked_by_me" :href="chatBlockUrl+'/unblock/'+user.id" class="primary-btn small fix-gr-bg">
                                                                                        Unblock this user
                                                                                    </a>
                                                                                    <a x-else-if="user.blocked" href=""></a>
                                                                                    <a x-else :href="chatBlockUrl+'/block/'+user.id" class="primary-btn small fix-gr-bg">
                                                                                        Block this user
                                                                                    </a>
                                                                                </div>
                                                                                <div class="col-xl-6">
                                                                                    <div class="primary_input mb-25">
                                                                                        <label class="primary_input_label">Description</label>
                                                                                        <p x-text="user.description"></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </template>
                                                </div>
                                            </template>
                                            <template x-if="users.length === 0">
                                                <p>No conversation found!</p>
                                            </template>
                                        </ul>
                                    </div>
                                    <div class="main-title2 d-flex align-items-center justify-content-between">
                                        <h4>Group</h4>
                                        <label class="primary_input_label green_input_label m-0">
                                            <a x-if="canCreateGroup" :href="createGroupUrl">Create Group <i class="fa fa-plus-circle"></i></a>
                                        </label>
                                    </div>
                                    <div class="chat_flow_list_inner">
                                        <ul style="list-style: none;">
                                            <template x-for="group in groups" :key="group.id">
                                                <li>
                                                    <div class="single_list d-flex align-items-center">
                                                        <div class="thumb">
                                                            <a href="#">
                                                                <img x-if="group.photo_url" :src="baseUrl+group.photo_url" alt="">
                                                                <img x-else :src="baseUrl + assetType+'/chat/images/bw-spondon-icon.png'" alt="">
                                                            </a>
                                                        </div>
                                                        <div class="list_name">
                                                            <div class="create_group d-flex align-items-center justify-content-between">
                                                                <a :href="groupChatShow+'/'+group.id"><h4 class="m-0" x-text="group.name"></h4></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(app('general_settings')->get('chatting_method') == null || app('general_settings')->get('chatting_method')  == 'log')
                            <div x-data="chatWindow({
                                new_message_check_url: {{ json_encode(route('chat.message.check')) }},
                                to_user: {{ json_encode($activeUser->load('activeStatus')) }},
                                from_user: {{ json_encode(auth()->user()->load('activeStatus')) }},
                                send_message_url: {{ json_encode(route('chat.send')) }},
                                app_url: {{ json_encode(env('APP_URL'). '/') }},
                                files_url: {{ json_encode(route('chat.files', ['type' => 'single', 'id' => $activeUser->id])) }},
                                loaded_conversations: {{ json_encode(collect($messages)) }},
                                connected_users: {{ json_encode(collect($users)) }},
                                forward_message_url: {{ json_encode(route('chat.send.forward')) }},
                                delete_message_url: {{ json_encode(route('chat.delete')) }},
                                load_more_url: {{ json_encode(route('chat.load.more')) }},
                                can_file_upload: {{ json_encode(app('general_settings')->get('chat_can_upload_file')== 'yes') }},
                                asset_type: {{ json_encode('/public') }}
                            })">
                                @include('chat::partials._chat_window')
                            </div>
                        @else
                            <div x-data="chatWindow({
                                to_user: {{ json_encode($activeUser->load('activeStatus')) }},
                                from_user: {{ json_encode(auth()->user()->load('activeStatus')) }},
                                send_message_url: {{ json_encode(route('chat.send')) }},
                                app_url: {{ json_encode(env('APP_URL'). '/') }},
                                files_url: {{ json_encode(route('chat.files', ['type' => 'single', 'id' => $activeUser->id])) }},
                                loaded_conversations: {{ json_encode($messages) }},
                                connected_users: {{ json_encode(collect($users)) }},
                                forward_message_url: {{ json_encode(route('chat.send.forward')) }},
                                delete_message_url: {{ json_encode(route('chat.delete')) }},
                                load_more_url: {{ json_encode(route('chat.load.more')) }},
                                can_file_upload: {{ json_encode(app('general_settings')->get('chat_can_upload_file')== 'yes') }},
                                asset_type: {{ json_encode('/public') }}
                            })">
                                @include('chat::partials._chat_window')
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

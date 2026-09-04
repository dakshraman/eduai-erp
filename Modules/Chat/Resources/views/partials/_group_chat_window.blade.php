<div class="chat_view_list">
    <div class="box_header">
        <div class="main-title">
            <div class="dropdown CRM_dropdown">
                <button class="btn btn-secondary dropdown-toggle" id="dropdownMenu3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span x-text="group.name"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu3">
                    <a x-show="my_role == 1" data-toggle="modal" data-target="#manageRole" class="dropdown-item">
                        @lang('chat::chat.user_role')
                    </a>
                    <a data-toggle="modal" data-target="#chat_search" class="dropdown-item">@lang('common.search')</a>
                    <a x-show="can_add_people" data-toggle="modal" data-target="#addPeopleForm" class="dropdown-item">
                        @lang('common.add') @lang('common.people')
                    </a>
                    <a data-toggle="modal" data-target="#removePeopleForm" class="dropdown-item">@lang('chat::chat.member')</a>
                    <a :href="files_url" class="dropdown-item">@lang('chat::chat.media_files')</a>
                    <a @click.prevent="leave_group()" class="dropdown-item">@lang('chat::chat.leave_group')</a>
                    <a x-show="my_role == 1" @click.prevent="delete_group()" class="dropdown-item">@lang('chat::chat.delete_group')</a>
                    <a x-show="my_role == 1 && !read_only" @click.prevent="make_read_only()" class="dropdown-item">@lang('chat::chat.mark_as_read_only')</a>
                    <a x-show="my_role == 1 && read_only" @click.prevent="make_read_only('unmark')" class="dropdown-item">@lang('chat::chat.remove_read_only')</a>
                </div>
            </div>
        </div>
        <div class="dropdown CRM_dropdown">
            <button class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                <span x-text="user.first_name"></span>
                <span :class="user.active_status ? (user.active_status.status == 1 ? 'active_chat' : (user.active_status.status == 0 ? 'inactive_chat' : (user.active_status.status == 3 ? 'busy_chat' : 'away_chat'))) : 'away_chat'"></span>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                <a :href="app_url+'/chat/user/status/1?url='+Laravel.current_path_without_domain" class="dropdown-item">@lang('common.active')</a>
                <a :href="app_url+'/chat/user/status/0?url='+Laravel.current_path_without_domain" class="dropdown-item">@lang('common.inactive')</a>
                <a :href="app_url+'/chat/user/status/2?url='+Laravel.current_path_without_domain" class="dropdown-item">@lang('common.away')</a>
                <a :href="app_url+'/chat/user/status/3?url='+Laravel.current_path_without_domain" class="dropdown-item">@lang('common.busy')</a>
            </div>
        </div>
    </div>

    <div class="chat_view_list_inner crm_full_height">
        <div x-show="search_result_bar" class="search_indicator py-2">
            <div class="float-left">
                <p class="mb-0">@lang('common.showing') <span x-text="search_result_count===0 ? search_result_count : search_index+1"></span> @lang('common.of') <span x-text="search_result_count"></span> @lang('common.results')</p>
            </div>
            <div class="float-right">
                <p class="mb-0">
                    <a href="#" @click.prevent="scroll_to_search_up()" class="px-1"><span class="ti-arrow-up"></span></a>
                    <a href="#" @click.prevent="scroll_to_search()" class="px-1"><span class="ti-arrow-down"></span></a>
                    <a href="#" @click.prevent="close_search_bar()" class="px-1"><span class="ti-close"></span></a>
                </p>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="chat_view_list_inner_scrolled" id="chat_container" style="max-height: 70vh;">
            <div>
                <p class="text-center" x-show="loadable">
                    <a class="cursor-pointer" @click.prevent="loadMore()">
                        @lang('common.load_more') <span class="ti-arrow-up"></span>
                    </a>
                </p>

                <template x-for="(thread, index) in only_threads" :key="thread.id">
                    <div :id="'target'+index">
                        <div x-if="parseInt(thread.user.id) != parseInt(user.id) && !thread.removedByMe" class="chat_single d-flex" style="overflow: unset">
                            <div class="thumb mr_20">
                                <a href="#"><img :src="getUserAvatar(thread.user)" alt=""></a>
                            </div>
                            <div class="chat_text_info_wraper d-flex align-items-center">
                                <div class="chat_text_info">
                                    <div x-if="thread.conversation.reply">
                                        <p class="reply_p" x-if="thread.conversation.reply.message_type == 0" x-html="urlMaker(thread.conversation.reply.message)"></p>
                                        <p class="reply_p" x-else>@lang('chat::chat.attachment')</p>
                                    </div>

                                    <div x-if="thread.conversation.forward_from">
                                        <p class="reply_p font-italic" x-if="thread.conversation.forward_from.message_type == 0">
                                            <span>@lang('chat::chat.forwarded_message') : </span><br>
                                            <span x-html="urlMaker(thread.conversation.forward_from.message)"></span>
                                        </p>
                                        <div class="reply_p audio-padding" x-if="thread.conversation.forward_from.message_type == 4">
                                            <span>@lang('chat::chat.forwarded_message') : </span><br>
                                            <audio :src="baseUrl+thread.conversation.forward_from.file_name" controls></audio>
                                        </div>
                                        <div x-else-if="thread.conversation.forward_from.message_type == 5" class="reply_p p-3 w-100">
                                            <span>@lang('chat::chat.forwarded_message') : </span><br>
                                            <video class="w-100 border-radius-25" controls>
                                                <source :src="baseUrl+thread.conversation.forward_from.file_name" type="video/mp4">
                                            </video>
                                        </div>
                                        <div class="reply_p p-3" x-else-if="thread.conversation.forward_from.message_type == 1">
                                            <span>@lang('chat::chat.forwarded_message') : </span><br>
                                            <img class="border-radius-25 cursor-pointer" @click="imageViewLargeScreen(baseUrl+thread.conversation.forward_from.file_name)" x-if="thread.conversation.forward_from.file_name" :src="baseUrl+thread.conversation.forward_from.file_name" alt="">
                                            <img class="border-radius-25" x-else :src="baseUrl+asset_type+'/chat/images/msg_img.png'" alt="">
                                        </div>
                                        <p class="reply_p" x-else-if="thread.conversation.message_type == 2 || thread.conversation.message_type == 3">
                                            <span>@lang('chat::chat.forwarded_message') : </span><br>
                                            <u><a style="color: white;" :href="baseUrl+'chat/group/file/download/'+thread.conversation.forward_from.id+'/'+thread.group_id" x-text="thread.conversation.forward_from.original_file_name"></a></u>
                                        </p>
                                    </div>

                                    <div class="audio-padding" x-if="thread.conversation.message_type == 4">
                                        <audio :src="baseUrl+thread.conversation.file_name" controls></audio>
                                    </div>
                                    <div x-else-if="thread.conversation.message_type == 5" class="p-3 w-100">
                                        <video class="w-100 border-radius-25" controls>
                                            <source :src="baseUrl+thread.conversation.file_name" type="video/mp4">
                                        </video>
                                    </div>
                                    <p x-else-if="thread.conversation.message_type == 0">
                                        <strong x-text="thread.user.first_name + ' ' + thread.user.last_name + ' : '"></strong><br>
                                        <span :id="'text'+index" class="textmsg" x-html="urlMaker(thread.conversation.message)"></span>
                                    </p>
                                    <div class="p-3" x-else-if="thread.conversation.message_type == 1">
                                        <img class="border-radius-25 cursor-pointer" @click="imageViewLargeScreen(baseUrl+thread.conversation.file_name)" x-if="thread.conversation.file_name" :src="baseUrl+thread.conversation.file_name" alt="">
                                        <img class="border-radius-25" x-else :src="baseUrl+asset_type+'/chat/images/msg_img.png'" alt="">
                                    </div>
                                    <p x-else-if="thread.conversation.message_type == 2 || thread.conversation.message_type == 3">
                                        <u><a style="color: white;" :href="baseUrl+'chat/group/file/download/'+thread.conversation.id+'/'+thread.group_id" x-text="thread.conversation.original_file_name"></a></u>
                                    </p>
                                    <p x-else x-text="thread.conversation.file_name"></p>
                                </div>
                                <span class="chat_date ml_15 ml-2">
                                    <span x-text="diffHuman(thread.conversation.created_at)"></span>
                                    <div class="dropdown">
                                        <a data-toggle="dropdown"><span class="ti-angle-down cursor-pointer"></span></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a @click.prevent="reply(thread.conversation)" class="dropdown-item cursor-pointer">@lang('chat::chat.quote')</a>
                                            <a @click.prevent="forwardModalOpen(thread.conversation)" class="dropdown-item cursor-pointer">@lang('chat::chat.forward')</a>
                                            <a x-show="thread.user.id == user.id" @click.prevent="deleteMessage(thread)" class="dropdown-item cursor-pointer">@lang('chat::chat.delete')</a>
                                        </div>
                                    </div>
                                </span>
                            </div>
                        </div>

                        <div x-if="thread.user.id == user.id && !thread.removedByMe" class="chat_single d-flex sender_chat" style="overflow: unset">
                            <div class="chat_text_info_wraper d-flex align-items-center">
                                <span class="chat_date ml_15">
                                    <span x-text="diffHuman(thread.conversation.created_at)"></span>
                                    <div class="dropdown">
                                        <a data-toggle="dropdown"><span class="ti-angle-down cursor-pointer"></span></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a @click.prevent="reply(thread.conversation)" class="dropdown-item cursor-pointer">@lang('chat::chat.quote')</a>
                                            <a @click.prevent="forwardModalOpen(thread.conversation)" class="dropdown-item cursor-pointer">@lang('chat::chat.forward')</a>
                                            <a @click.prevent="deleteMessage(thread)" class="dropdown-item cursor-pointer">@lang('chat::chat.delete')</a>
                                        </div>
                                    </div>
                                </span>
                                <div class="chat_text_info">
                                    <div x-if="thread.conversation.reply">
                                        <p class="sender_reply_p" x-if="thread.conversation.reply.message_type == 0" x-html="urlMaker(thread.conversation.reply.message)"></p>
                                        <p class="sender_reply_p" x-else>@lang('chat::chat.attachment')</p>
                                    </div>

                                    <div x-if="thread.conversation.forward_from">
                                        <p class="sender_reply_p font-italic" x-if="thread.conversation.forward_from.message_type == 0">
                                            <span>@lang('chat::chat.forwarded_message') : </span><br>
                                            <span x-text="thread.conversation.forward_from.message"></span>
                                        </p>
                                        <div class="sender_reply_p audio-padding" x-if="thread.conversation.forward_from.message_type == 4">
                                            <span>@lang('chat::chat.forwarded_message') : </span><br>
                                            <audio :src="baseUrl+thread.conversation.forward_from.file_name" controls></audio>
                                        </div>
                                        <div x-else-if="thread.conversation.forward_from.message_type == 5" class="sender_reply_p p-3 w-100">
                                            <span>@lang('chat::chat.forwarded_message') : </span><br>
                                            <video class="w-100 border-radius-25" controls>
                                                <source :src="baseUrl+thread.conversation.forward_from.file_name" type="video/mp4">
                                            </video>
                                        </div>
                                        <div class="sender_reply_p p-3" x-else-if="thread.conversation.forward_from.message_type == 1">
                                            <span>@lang('chat::chat.forwarded_message') : </span><br>
                                            <img class="border-radius-25 cursor-pointer" @click="imageViewLargeScreen(baseUrl+thread.conversation.forward_from.file_name)" x-if="thread.conversation.forward_from.file_name" :src="baseUrl+thread.conversation.forward_from.file_name" alt="">
                                            <img class="border-radius-25" x-else :src="baseUrl+asset_type+'/chat/images/msg_img.png'" alt="">
                                        </div>
                                        <p class="sender_reply_p" x-else-if="thread.conversation.message_type == 2 || thread.conversation.message_type == 3">
                                            <span>@lang('chat::chat.forwarded_message') : </span><br>
                                            <u><a style="color: white;" :href="baseUrl+'chat/group/file/download/'+thread.conversation.forward_from.id+'/'+thread.group_id" x-text="thread.conversation.forward_from.original_file_name"></a></u>
                                        </p>
                                    </div>

                                    <div class="audio-padding" x-if="thread.conversation.message_type == 4">
                                        <audio :src="baseUrl+thread.conversation.file_name" controls></audio>
                                    </div>
                                    <div x-else-if="thread.conversation.message_type == 5" class="p-3 w-100">
                                        <video class="w-100 border-radius-25" controls>
                                            <source :src="baseUrl+thread.conversation.file_name" type="video/mp4">
                                        </video>
                                    </div>
                                    <p x-else-if="thread.conversation.message_type == 0">
                                        <span :id="'text'+index" class="textmsg" x-html="urlMaker(thread.conversation.message)"></span>
                                    </p>
                                    <div class="p-3" x-else-if="thread.conversation.message_type == 1">
                                        <img class="border-radius-25 cursor-pointer" @click="imageViewLargeScreen(baseUrl+thread.conversation.file_name)" x-if="thread.conversation.file_name" :src="baseUrl+thread.conversation.file_name" alt="">
                                        <img class="border-radius-25" x-else :src="baseUrl+asset_type+'/chat/images/msg_img.png'" alt="">
                                    </div>
                                    <p x-else-if="thread.conversation.message_type == 2 || thread.conversation.message_type == 3">
                                        <u><a style="color: white;" :href="baseUrl+'chat/group/file/download/'+thread.conversation.id+'/'+thread.group_id" x-text="thread.conversation.original_file_name"></a></u>
                                    </p>
                                    <p x-else x-text="thread.conversation.file_name"></p>
                                </div>
                            </div>
                            <div class="thumb">
                                <a href="#"><img :src="getUserAvatar(user)" alt=""></a>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div x-show="replying" class="bg-gray-200 p-3 replying-box mb-2-3 ml-5">
            <p>
                <strong class="text-info">@lang('chat::chat.replying') : </strong><br>
                <span x-html="replying_text"></span>
            </p>
            <span @click="quoteClosePreview" class="close-quote-preview cursor-pointer"><i class="fa fa-times"></i></span>
        </div>

        <form x-show="!read_only" @submit.prevent="sendMessage" :class="{'pt-100px' : addPadding}">
            <div class="chat_input_box d-flex align-items-center">
                <div class="input_thumb">
                    <img :src="getUserAvatar(user)" alt="">
                </div>
                <div class="input-group">
                    <div x-show="preview_url" class="preview_imgs">
                        <img :src="preview_url" alt="" style="object-fit: contain">
                        <span class="close_preview" @click.prevent="closePreview"><i class="ti-close"></i></span>
                    </div>

                    <div x-show="file_name_preview" class="preview_imgs">
                        <span x-text="file_name_preview"></span>
                        <span class="close_preview" @click.prevent="closePreview"><i class="ti-close"></i></span>
                    </div>

                    <div class="quillWrapper">
                        <div x-ref="quillEditor" style="min-height: 60px;"></div>
                    </div>

                    <div class="input-group-append">
                        <button class="btn pr-2" type="button" @click.prevent="toggleRecording">
                            <i :class="{'microphone-red': record_status}" class="ti-microphone-alt"></i>
                        </button>
                        <button class="btn" type="button" @click.prevent="emoji = !emoji">
                            <i :class="{'imoji-box-open': emoji}" class="ti-face-smile img_toggle"></i>
                        </button>
                        <button x-show="can_file_upload" class="btn" type="button">
                            <i class="ti-clip"></i>
                            <input type="file" @change="onFileChange" id="imgInp" accept=".jpg,.jpeg,.png,.doc,.docx,.pdf,.mp4,.3gp,.webm" style="display:none;">
                        </button>
                        <button class="btn svg_send_button" @click.prevent="sendMessage" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" width="20px" height="20px">
                                <g>
                                    <rect x="0" y="0" width="40" height="40" style="fill: #F5F8FF; fill-opacity: 1; stroke: none;" />
                                    <path style="stroke: none; fill-rule: nonzero; fill: #989898; fill-opacity: 1;" d="M 40.210938 10.667969 L 35.167969 22.199219 L 34.816406 23 L 35.167969 23.800781 L 40.210938 35.332031 L 9.386719 23 L 40.210938 10.667969 M 44 7 L 4 23 L 44 39 L 37 23 Z M 44 7 "/>
                                </g>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <span class="text-muted" x-show="typing">@lang('chat::chat.someone') @lang('chat::chat.is_typing')...</span>

    <div class="mt-2 bg-white timer-display" x-show="record_status">
        <div class="timer-padding">
            <span class="timer" x-text="timing"></span>
        </div>
        <div class="timer-padding">
            <img :src="baseUrl+asset_type+'/chat/images/recording.gif'" alt="" style="width: 140px; height: 35px">
            <span class="text-muted">@lang('chat::chat.your_voice_is_recording')...</span>
        </div>
        <div class="stop-button-padding">
            <a href="#" @click.prevent="toggleRecording">
                <img :src="baseUrl+asset_type+'/chat/images/recording-stop.png'" alt="" style="height: 35px; width: 35px;">
            </a>
        </div>
    </div>
</div>

<!-- Add People Modal -->
<div class="modal fade admin-query" id="addPeopleForm" aria-modal="true">
    <div class="modal-dialog modal_800px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('common.add') @lang('common.people')</h4>
                <button type="button" class="close" data-dismiss="modal"><i class="ti-close"></i></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="primary_input mb-15">
                            <label class="primary_input_label">@lang('chat::chat.member')</label>
                            <select class="primary_select" x-model="add_user">
                                <option value="" disabled>@lang('common.select')</option>
                                <template x-for="single_user in connected_users" :key="single_user.id">
                                    <option :value="single_user.id" x-text="single_user.first_name"></option>
                                </template>
                            </select>
                        </div>
                        <button @click.prevent="add_new_user()" class="primary-btn radius_30px fix-gr-bg">@lang('common.add')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Manage Role Modal -->
<div x-show="my_role == 1" class="modal fade admin-query" id="manageRole" aria-modal="true">
    <div class="modal-dialog modal_800px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('chat::chat.manage_role')</h4>
                <button type="button" class="close" data-dismiss="modal"><i class="ti-close"></i></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="primary_input mb-15">
                            <label class="primary_input_label">@lang('chat::chat.member')</label>
                            <select class="primary_select" x-model="assignable_user">
                                <option value="" disabled>@lang('common.select')</option>
                                <template x-for="usr in group.users" :key="usr.id">
                                    <option :value="usr.id" x-text="usr.first_name"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="primary_input mb-15">
                            <label class="primary_input_label">@lang('chat::chat.user_role')</label>
                            <select class="primary_select" x-model="assignable_role">
                                <option value="" disabled>@lang('common.select')</option>
                                <option value="1">@lang('chat::chat.chat_admin')</option>
                                <option value="2">@lang('chat::chat.chat_moderate')</option>
                            </select>
                        </div>
                        <button @click.prevent="assign_role_to_user()" class="primary-btn radius_30px fix-gr-bg">@lang('common.add')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Remove People Modal -->
<div class="modal fade admin-query" id="removePeopleForm" aria-modal="true">
    <div class="modal-dialog modal_800px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('chat::chat.member')</h4>
                <button type="button" class="close" data-dismiss="modal"><i class="ti-close"></i></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-12">
                        <template x-for="single_user in group.users" :key="single_user.id">
                            <div x-show="single_user.id !== user.id">
                                <li class="my-4 py-2 remove-list list-unstyled">
                                    <div class="single_list d-flex align-items-center p-2">
                                        <div class="thumb">
                                            <img style="width: 100px" x-if="single_user.avatar_url" :src="baseUrl + single_user.avatar_url" alt="">
                                            <a x-else href="#"><img :src="getUserAvatar(single_user)" alt=""></a>
                                        </div>
                                        <div class="list_name ml-5">
                                            <a>
                                                <h4 x-text="single_user.first_name + ' ' + single_user.last_name"></h4>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="p-2" x-show="my_role == 1">
                                        <button @click.prevent="remove_people(single_user.id)" class="primary-btn radius_30px fix-gr-bg">@lang('common.remove')</button>
                                    </div>
                                </li>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search Modal -->
<div class="modal fade admin-query" id="chat_search" aria-modal="true">
    <div class="modal-dialog modal_800px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('common.search')</h4>
                <button type="button" class="close" data-dismiss="modal"><i class="ti-close"></i></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="primary_input mb_20">
                            <label class="primary_input_label">@lang('common.keywords')</label>
                            <input class="primary_input_field" placeholder="-" type="text" x-model="keywords">
                        </div>
                        <br>
                        <button @click.prevent="search()" class="primary-btn radius_30px fix-gr-bg">@lang('common.search')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image View Modal -->
<div class="modal fade admin-query" id="imageView" aria-modal="true">
    <div class="modal-dialog modal_800px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('chat::chat.preview')</h4>
                <button type="button" class="close" data-dismiss="modal"><i class="ti-close"></i></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-12">
                        <img :src="imageSrc" alt="" class="w-100">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Forward Modal -->
<div class="modal fade admin-query" id="forward" aria-modal="true">
    <div class="modal-dialog modal_800px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('chat::chat.forward') @lang('chat::chat.message')</h4>
                <button type="button" class="close" data-dismiss="modal"><i class="ti-close"></i></button>
            </div>
            <div class="modal-body" x-show="forward_conversation">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card mb-2">
                            <div class="card-header" x-html="forward_conversation ? forward_conversation.message : ''"></div>
                        </div>
                        <div class="primary_input mb_20">
                            <input x-model="forward_addon_text" class="primary_input_field" :placeholder="__('chat::chat.write_something_Optional')" type="text">
                        </div>
                    </div>
                    <div class="users-box">
                        <template x-for="(list_user, indexFor) in all_connected_users" :key="list_user.id">
                            <div class="col-xl-12 mt-1 mb-1">
                                <div class="single_list d-flex justify-content-between">
                                    <div class="thumb">
                                        <a href="#">
                                            <img class="forward-image" :src="getUserAvatar(list_user)" alt="">
                                            <h4 x-text="list_user.first_name + ' ' + list_user.last_name"></h4>
                                        </a>
                                    </div>
                                    <div class="mt-4">
                                        <a href="#" :id="'forwordClick'+indexFor"
                                            x-show="forward_conversation && forward_conversation.forward_from"
                                            @click.prevent="forward(forward_conversation.forward_from, list_user, indexFor)"
                                            class="primary-btn radius_30px fix-gr-bg">
                                            <i class="ti-share"></i>@lang('common.send')
                                        </a>
                                        <a href="#" :id="'forwordClick'+indexFor"
                                            x-show="forward_conversation && !forward_conversation.forward_from"
                                            @click.prevent="forward(forward_conversation, list_user, indexFor)"
                                            class="primary-btn radius_30px fix-gr-bg">
                                            <i class="ti-share"></i>@lang('common.send')
                                        </a>
                                    </div>
                                </div>
                                <hr style="margin-top: 0; margin-bottom: 0">
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

document.addEventListener('alpine:init', () => {

    Alpine.data('chatSidePanel', (data = {}) => ({
        users: data.users || [],
        groups: data.groups || [],
        allUsers: data.all_users || [],
        searchUrl: data.search_url || '',
        singleChatUrl: data.single_chat_url || '',
        chatBlockUrl: data.chat_block_url || '',
        createGroupUrl: data.create_group_url || '',
        groupChatShow: data.group_chat_show || '',
        canCreateGroup: data.can_create_group || false,
        assetType: data.asset_type || '',
        settings: data.settings || {},
        baseUrl: Laravel.baseUrl || '',
        tags: [],

        init() {
            this.tags = this.allUsers.map(u => u.first_name);
            this.$nextTick(() => {
                const el = document.getElementById('users_list_sidebar');
                if (el && jQuery && jQuery.fn.autocomplete) {
                    jQuery(el).autocomplete({ source: this.tags });
                }
            });
        },

        getUserAvatar(user) {
            if (user.avatar) return this.baseUrl + user.avatar;
            if (user.avatar_url) return this.baseUrl + user.avatar_url;
            return this.baseUrl + this.assetType + '/chat/images/spondon-icon.png';
        },

        getStatusClass(status) {
            switch (parseInt(status)) {
                case 1: return 'active_chat';
                case 0: return 'inactive_chat';
                case 3: return 'busy_chat';
                default: return 'away_chat';
            }
        },

        openUserProfileModal(id) {
            const modal = document.getElementById(id);
            if (modal && jQuery) {
                jQuery(modal).modal('show');
            }
        },

        stripHtml(html) {
            if (!html) return '';
            return html.replace(/(<([^>]+)>)/ig, '').substring(0, 20) + '...';
        },

        canShowPhone(user) {
            return user.role_id != 4 || this.settings.teacher_phone_view;
        },

        canShowEmail(user) {
            return user.role_id != 4 || this.settings.teacher_email_view;
        },

        canShowContact(user) {
            return user.role_id != 4 || (this.settings.teacher_phone_view && this.settings.teacher_email_view);
        }
    }));

    Alpine.data('chatWindow', (data = {}) => ({
        send_message_url: data.send_message_url || '',
        app_url: data.app_url || '',
        files_url: data.files_url || '',
        from_user: data.from_user || {},
        to_user: data.to_user || {},
        loaded_conversations: data.loaded_conversations || {},
        connected_users: data.connected_users || [],
        forward_message_url: data.forward_message_url || '',
        delete_message_url: data.delete_message_url || '',
        load_more_url: data.load_more_url || '',
        can_file_upload: data.can_file_upload !== false,
        asset_type: data.asset_type || '',
        new_message_check_url: data.new_message_check_url || '',
        baseUrl: Laravel.baseUrl || '',

        conversations: [],
        newMessage: '',
        file: null,
        emoji: false,
        record_status: false,
        recorder: null,
        gumStream: null,
        preview_url: null,
        file_name_preview: null,
        addPadding: false,
        typing: false,
        replying: false,
        replying_text: '',
        replying_to: null,
        imageSrc: '',
        forward_conversation: null,
        forward_addon_text: '',
        keywords: '',
        search_result_bar: false,
        search_result_count: 0,
        search_result_ids: [],
        search_index: 0,
        seconds: 0,
        timing: '',
        timingIntervalId: null,
        current_conversation_ids: [],
        loadable: true,
        intervalId: null,

        init() {
            if (typeof this.loaded_conversations === 'object') {
                this.conversations = Object.keys(this.loaded_conversations).map(key => this.loaded_conversations[key]);
            }
            if (this.conversations.length < 20) {
                this.loadable = false;
            }
            this.intervalId = window.setInterval(() => this.checkNewMessage(), 10000);

            if (window.Echo) {
                Echo.private('single-chat.' + this.from_user.id)
                    .listen('ChatEvent', (e) => {
                        this.messagePush(e.message);
                        this.reset();
                    })
                    .listenForWhisper('single-typing', () => {
                        this.typing = true;
                        setTimeout(() => { this.typing = false; }, 1500);
                    });
            }
        },

        destroy() {
            if (this.intervalId) clearInterval(this.intervalId);
            if (this.timingIntervalId) clearInterval(this.timingIntervalId);
        },

        getUserAvatar(user) {
            if (user && user.avatar) return '/' + user.avatar;
            if (user && user.avatar_url) return this.baseUrl + user.avatar_url;
            return this.baseUrl + this.asset_type + '/chat/images/spondon-icon.png';
        },

        getStatusClass(status) {
            switch (parseInt(status)) {
                case 1: return 'active_chat';
                case 0: return 'inactive_chat';
                case 3: return 'busy_chat';
                default: return 'away_chat';
            }
        },

        diffHuman(date) {
            if (window.moment) return moment(date).fromNow();
            return date;
        },

        urlMaker(text) {
            if (!text) return text;
            return text.replace(/((http|https|ftp):\/\/[\w?=&.\/-;#~%-]+(?![\w\s?&.\/;#~%"=-]*>))/g, '<a href="$1" target="_blank">$1</a> ');
        },

        messagePush(message) {
            this.conversations.push({
                from_id: message.from_id,
                to_id: message.to_id,
                id: message.id,
                message: message.message,
                message_type: message.message_type,
                file_name: message.file_name,
                original_file_name: message.original_file_name,
                reply: message.reply,
                forward_from: message.forward_from,
                deleted_by_to: '0'
            });
        },

        sendMessage() {
            this.emoji = false;
            const config = { headers: { 'Content-Type': 'multipart/form-data' } };
            const formData = new FormData();
            if (this.replying_to) formData.append('reply', this.replying_to);
            formData.append('file_attach', this.file);
            formData.append('message', this.newMessage);
            formData.append('from_id', this.from_user.id);
            formData.append('to_id', this.to_user.id);

            axios.post(this.send_message_url, formData, config).then((response) => {
                if (response.data.empty) return;
                this.replying = false;
                this.messagePush(response.data.message);
                this.reset();
                this.cleanReply();
                this.closePreview();
                this.checkNewMessage();
                window.clearInterval(this.intervalId);
                this.intervalId = window.setInterval(() => this.checkNewMessage(), 10000);
            }).catch(err => console.log(err));
            this.newMessage = '';
        },

        checkNewMessage() {
            const formData = new FormData();
            if (this.conversations.length > 0) {
                formData.append('last_conversation_id', this.conversations[this.conversations.length - 1].id);
            } else {
                formData.append('last_conversation_id', null);
            }
            formData.append('user_id', this.to_user.id);
            axios.post(this.new_message_check_url, formData).then((response) => {
                if (response.data.invalid) return;
                Object.entries(response.data.messages).forEach(([key, value]) => this.messagePush(value));
                this.reset();
                this.closePreview();
            }).catch(err => console.log(err));
        },

        reset() {
            this.file = null;
            const fileInput = document.getElementById('imgInp');
            if (fileInput) fileInput.value = null;
        },

        onChangeFileUpload(e) {
            this.file = e.target.files[0];
        },

        onFileChange(e) {
            clearInterval(this.intervalId);
            const file = e.target.files[0];
            this.addPadding = true;
            if (['image/jpg', 'image/png', 'image/jpeg', 'image/JPG', 'image/PNG', 'image/JPEG'].includes(file.type)) {
                this.preview_url = URL.createObjectURL(file);
                return;
            }
            this.file_name_preview = file.name;
        },

        closePreview() {
            this.preview_url = null;
            this.file_name_preview = null;
            this.reset();
            this.addPadding = false;
        },

        selectEmoji(emoji) {
            this.newMessage += emoji.data;
        },

        toggleRecording() {
            this.emoji = false;
            if (this.recorder && this.recorder.state === 'recording') {
                this.record_status = false;
                this.recorder.stop();
                this.gumStream.getAudioTracks()[0].stop();
            } else {
                navigator.mediaDevices.getUserMedia({ audio: true }).then((stream) => {
                    this.record_status = true;
                    this.timingIntervalId = window.setInterval(() => this.incrementSeconds(), 1000);
                    this.gumStream = stream;
                    this.recorder = new MediaRecorder(stream);
                    this.recorder.ondataavailable = (e) => {
                        clearInterval(this.timingIntervalId);
                        this.seconds = 0;
                        const config = { headers: { 'Content-Type': 'multipart/form-data' } };
                        const formData = new FormData();
                        formData.append('reply', this.replying_to);
                        formData.append('file_attach', e.data);
                        formData.append('message', this.newMessage);
                        formData.append('from_id', this.from_user.id);
                        formData.append('to_id', this.to_user.id);
                        axios.post(this.send_message_url, formData, config).then((response) => {
                            this.messagePush(response.data.message);
                            this.reset();
                            this.quoteClosePreview();
                            this.checkNewMessage();
                        }).catch(err => console.log(err));
                        this.newMessage = '';
                    };
                    this.recorder.start();
                });
            }
        },

        incrementSeconds() {
            this.seconds += 1;
            this.timing = this.timeConverter(this.seconds);
        },

        timeConverter(time) {
            const hrs = ~~(time / 3600);
            const mins = ~~((time % 3600) / 60);
            const secs = ~~time % 60;
            let ret = '';
            if (hrs > 0) ret += '' + hrs + ':' + (mins < 10 ? '0' : '');
            ret += '' + mins + ':' + (secs < 10 ? '0' : '');
            ret += '' + secs;
            return ret;
        },

        deleteMessage(c) {
            this.cleanReply();
            const formData = new FormData();
            formData.append('conversation_id', c.id);
            formData.append('user_id', this.from_user.id);
            axios.post(this.delete_message_url, formData).then((response) => {
                if (response.data.success) {
                    const index = this.conversations.indexOf(c);
                    if (index > -1) this.conversations.splice(index, 1);
                    toastr.success('Message deleted!');
                } else {
                    toastr.error('Oops! something went wrong!');
                }
            }).catch(err => console.log(err));
        },

        reply(c) {
            this.replying = true;
            this.replying_text = c.message;
            this.replying_to = c.id;
        },

        cleanReply() {
            this.replying = false;
            this.replying_text = '';
            this.replying_to = null;
        },

        quoteClosePreview() {
            this.replying = false;
        },

        forward(c, u, id) {
            const btn = document.getElementById('forwordClick' + id);
            if (btn) {
                btn.textContent = ' Sent';
                btn.className = 'bg-less';
            }
            this.cleanReply();
            c.to_id = u.id;
            c.from_id = this.from_user.id;
            c.forward = c.id;
            c.message = this.forward_addon_text || null;
            axios.post(this.forward_message_url, c).catch(err => console.log(err));
        },

        forwardModalOpen(c) {
            this.forward_conversation = c;
            const modal = document.getElementById('forward');
            if (modal && jQuery) jQuery(modal).modal('show');
        },

        imageViewLargeScreen(url) {
            this.imageSrc = url;
            const modal = document.getElementById('imageView');
            if (modal && jQuery) jQuery(modal).modal('show');
        },

        search() {
            this.search_index = 0;
            this.search_result_ids = [];
            this.search_result_count = 0;
            this.cleanReply();
            const modal = document.getElementById('chat_search');
            if (modal && jQuery) jQuery(modal).modal('hide');
            this.search_result_bar = true;
            const elements = document.getElementsByClassName('textmsg');
            for (let i = 0; i < elements.length; i++) {
                if (elements[i].innerHTML.indexOf(this.keywords) > -1) {
                    this.search_result_count += 1;
                    this.search_result_ids.push(elements[i].id);
                }
            }
            for (let k = 0; k < elements.length; k++) {
                if (elements[k].innerHTML.indexOf(this.keywords) > -1) {
                    const targetElement = document.getElementById(elements[k].id);
                    this.scrollIfNeeded(targetElement, document.getElementById('chat_container'));
                    if (jQuery) jQuery('#' + elements[k].id).css('background-color', 'rgb(177 168 104)');
                    break;
                }
            }
        },

        scroll_to_search() {
            if (this.search_index + 1 < this.search_result_ids.length) {
                this.search_index++;
                const el = document.getElementById(this.search_result_ids[this.search_index]);
                this.scrollIfNeeded(el, document.getElementById('chat_container'));
                if (jQuery) jQuery('#' + this.search_result_ids[this.search_index]).css('background-color', 'rgb(177 168 104)');
            }
        },

        scroll_to_search_up() {
            if (this.search_index >= 1) {
                this.search_index--;
                const el = document.getElementById(this.search_result_ids[this.search_index]);
                this.scrollIfNeeded(el, document.getElementById('chat_container'));
                if (jQuery) jQuery('#' + this.search_result_ids[this.search_index]).css('background-color', 'rgb(177 168 104)');
            }
        },

        scrollIfNeeded(element, container) {
            if (!element || !container) return;
            if (element.offsetTop < container.scrollTop) {
                container.scrollTop = element.offsetTop - 70;
            } else {
                const offsetBottom = element.offsetTop + element.offsetHeight;
                const scrollBottom = container.scrollTop + container.offsetHeight;
                if (offsetBottom > scrollBottom) {
                    container.scrollTop = offsetBottom - container.offsetHeight;
                }
            }
        },

        close_search_bar() {
            const elements = document.getElementsByClassName('textmsg');
            for (let i = 0; i < elements.length; i++) {
                if (elements[i].innerHTML.indexOf(this.keywords) > -1) {
                    if (jQuery) jQuery('#' + elements[i].id).css('background-color', 'unset');
                }
            }
            this.keywords = '';
            this.search_result_bar = false;
            this.search_result_count = 0;
            this.search_result_ids = [];
            this.search_index = 0;
        },

        loadMore() {
            this.current_conversation_ids = [];
            for (let c in this.conversations) {
                this.current_conversation_ids.push(this.conversations[c].id);
            }
            const formData = new FormData();
            formData.append('ids', JSON.stringify(this.current_conversation_ids));
            formData.append('user_id', this.to_user.id);
            axios.post(this.load_more_url, formData).then((response) => {
                if (response.data.success) {
                    if (response.data.conversations) {
                        for (let index in response.data.conversations) {
                            this.conversations.unshift(response.data.conversations[index]);
                        }
                    } else {
                        this.loadable = false;
                    }
                } else {
                    toastr.error('Oops! something went wrong!');
                }
            }).catch(err => console.log(err));
        }
    }));

    Alpine.data('groupChat', (data = {}) => ({
        send_message_url: data.send_message_url || '',
        add_people_url: data.add_people_url || '',
        remove_people_url: data.remove_people_url || '',
        assign_role_url: data.assign_role_url || '',
        leave_group_url: data.leave_group_url || '',
        delete_group_url: data.delete_group_url || '',
        app_url: data.app_url || '',
        files_url: data.files_url || '',
        group: data.group || {},
        user: data.user || {},
        connected_users: data.connected_users || [],
        forward_message_url: data.forward_message_url || '',
        all_connected_users: data.all_connected_users || [],
        message_remove_url: data.message_remove_url || '',
        my_role: data.my_role || 2,
        load_more_url: data.load_more_url || '',
        can_add_people: data.can_add_people !== false,
        read_only: data.read_only || false,
        can_file_upload: data.can_file_upload !== false,
        asset_type: data.asset_type || '',
        single_threads: data.single_threads || {},
        make_read_only_url: data.make_read_only_url || '',
        baseUrl: Laravel.baseUrl || '',

        only_threads: [],
        newMessage: '',
        file: null,
        emoji: false,
        record_status: false,
        recorder: null,
        gumStream: null,
        preview_url: null,
        file_name_preview: null,
        addPadding: false,
        add_user: '',
        assignable_user: '',
        assignable_role: '',
        typing: false,
        replying: false,
        replying_text: '',
        replying_to: null,
        imageSrc: '',
        forward_conversation: null,
        forward_addon_text: '',
        keywords: '',
        search_result_bar: false,
        search_result_count: 0,
        search_result_ids: [],
        search_index: 0,
        seconds: 0,
        timing: '',
        timingIntervalId: null,
        current_conversation_ids: [],
        loadable: true,

        init() {
            if (typeof this.single_threads === 'object') {
                this.only_threads = Object.keys(this.single_threads).map(key => this.single_threads[key]);
            }
            if (this.only_threads.length < 20) {
                this.loadable = false;
            }

            if (window.Echo) {
                Echo.private('group-chat.' + this.group.id)
                    .listen('GroupChatEvent', (e) => {
                        this.only_threads.push({
                            conversation: e.conversation,
                            conversation_id: e.conversation.id,
                            created_at: e.thread.created_at,
                            group_id: e.group.id,
                            id: e.thread.id,
                            updated_at: e.thread.updated_at,
                            user: e.user,
                            user_id: e.user.id,
                            reply: e.conversation.reply,
                            deleted_by_to: '0'
                        });
                        this.reset();
                    })
                    .listenForWhisper('typing', () => {
                        this.typing = true;
                        setTimeout(() => { this.typing = false; }, 1500);
                    });
            }
        },

        getUserAvatar(user) {
            if (user && user.avatar) return this.baseUrl + user.avatar;
            if (user && user.avatar_url) return this.baseUrl + user.avatar_url;
            return this.baseUrl + this.asset_type + '/chat/images/spondon-icon.png';
        },

        diffHuman(date) {
            if (window.moment && date) return moment(date).fromNow();
            return date;
        },

        urlMaker(text) {
            if (!text) return text;
            return text.replace(/((http|https|ftp):\/\/[\w?=&.\/-;#~%-]+(?![\w\s?&.\/;#~%"=-]*>))/g, '<a href="$1" target="_blank">$1</a> ');
        },

        messagePush(message) {
            this.only_threads.push({
                conversation: message.conversation,
                conversation_id: message.conversation.id,
                created_at: message.created_at,
                group_id: message.group_id,
                id: message.id,
                updated_at: message.updated_at,
                user: message.user,
                user_id: message.user.id,
                reply: message.reply,
                deleted_by_to: '0'
            });
        },

        sendMessage() {
            this.emoji = false;
            const config = { headers: { 'Content-Type': 'multipart/form-data' } };
            const formData = new FormData();
            if (this.replying_to) formData.append('reply', this.replying_to);
            formData.append('file_attach', this.file);
            formData.append('message', this.newMessage);
            formData.append('user_id', this.user.id);
            formData.append('group_id', this.group.id);

            axios.post(this.send_message_url, formData, config).then((response) => {
                if (response.data.empty) return;
                this.messagePush(response.data.thread);
                this.replying = false;
                this.reset();
                this.cleanReply();
                this.closePreview();
            }).catch(err => console.log(err));
            this.newMessage = '';
        },

        reset() {
            this.file = null;
            const fileInput = document.getElementById('imgInp');
            if (fileInput) fileInput.value = null;
        },

        onChangeFileUpload(e) {
            this.file = e.target.files[0];
        },

        onFileChange(e) {
            clearInterval(this.intervalId);
            const file = e.target.files[0];
            this.addPadding = true;
            if (['image/jpg', 'image/png', 'image/jpeg', 'image/JPG', 'image/PNG', 'image/JPEG'].includes(file.type)) {
                this.preview_url = URL.createObjectURL(file);
                return;
            }
            this.file_name_preview = file.name;
        },

        closePreview() {
            this.preview_url = null;
            this.file_name_preview = null;
            this.reset();
            this.addPadding = false;
        },

        selectEmoji(emoji) {
            this.newMessage += emoji.data;
        },

        toggleRecording() {
            this.emoji = false;
            if (this.recorder && this.recorder.state === 'recording') {
                this.record_status = false;
                this.recorder.stop();
                this.gumStream.getAudioTracks()[0].stop();
            } else {
                navigator.mediaDevices.getUserMedia({ audio: true }).then((stream) => {
                    this.record_status = true;
                    this.timingIntervalId = window.setInterval(() => this.incrementSeconds(), 1000);
                    this.gumStream = stream;
                    this.recorder = new MediaRecorder(stream);
                    this.recorder.ondataavailable = (e) => {
                        clearInterval(this.timingIntervalId);
                        this.seconds = 0;
                        const config = { headers: { 'Content-Type': 'multipart/form-data' } };
                        const formData = new FormData();
                        formData.append('reply', this.replying_to);
                        formData.append('file_attach', e.data);
                        formData.append('message', this.newMessage);
                        formData.append('user_id', this.user.id);
                        formData.append('group_id', this.group.id);
                        axios.post(this.send_message_url, formData, config).then((response) => {
                            this.messagePush(response.data.thread);
                            this.reset();
                            this.quoteClosePreview();
                        }).catch(err => console.log(err));
                        this.newMessage = '';
                    };
                    this.recorder.start();
                });
            }
        },

        incrementSeconds() {
            this.seconds += 1;
            this.timing = this.timeConverter(this.seconds);
        },

        timeConverter(time) {
            const hrs = ~~(time / 3600);
            const mins = ~~((time % 3600) / 60);
            const secs = ~~time % 60;
            let ret = '';
            if (hrs > 0) ret += '' + hrs + ':' + (mins < 10 ? '0' : '');
            ret += '' + mins + ':' + (secs < 10 ? '0' : '');
            ret += '' + secs;
            return ret;
        },

        deleteMessage(thread) {
            this.cleanReply();
            const formData = new FormData();
            formData.append('thread_id', thread.id);
            axios.post(this.message_remove_url, formData).then((response) => {
                if (response.data.success) {
                    const index = this.only_threads.indexOf(thread);
                    if (index > -1) this.only_threads.splice(index, 1);
                    toastr.success('Message deleted!');
                } else {
                    toastr.error('Oops! something went wrong!');
                }
            }).catch(err => console.log(err));
        },

        sendTypingEvent() {
            if (window.Echo) {
                Echo.private('group-chat.' + this.group.id).whisper('typing', { name: this.user });
            }
        },

        add_new_user() {
            const formData = new FormData();
            formData.append('user_id', this.add_user);
            formData.append('group_id', this.group.id);
            axios.post(this.add_people_url, formData).then(() => {
                toastr.success('User successfully added!');
                window.location.reload();
            }).catch(err => console.log(err));
        },

        remove_people(id) {
            const formData = new FormData();
            formData.append('user_id', id);
            formData.append('group_id', this.group.id);
            axios.post(this.remove_people_url, formData).then(() => {
                toastr.success('User successfully removed!');
                window.location.reload();
            }).catch(err => console.log(err));
        },

        leave_group() {
            if (!confirm('Do you want to leave?')) return;
            const formData = new FormData();
            formData.append('user_id', this.user.id);
            formData.append('group_id', this.group.id);
            axios.post(this.leave_group_url, formData).then((response) => {
                toastr.success('You successfully leave this group!');
                window.location.href = response.data.url;
            }).catch(err => console.log(err));
        },

        delete_group() {
            if (!confirm('Do you want to proceed?')) return;
            const formData = new FormData();
            formData.append('group_id', this.group.id);
            axios.post(this.delete_group_url, formData).then((response) => {
                if (response.data.notPermitted) {
                    toastr.error("You can't perform this action!");
                } else {
                    toastr.success('Your group successfully deleted');
                    window.location.href = response.data.url;
                }
            }).catch(err => console.log(err));
        },

        make_read_only(type = null) {
            if (!confirm('Do you want to proceed?')) return;
            const formData = new FormData();
            formData.append('group_id', this.group.id);
            formData.append('type', type);
            axios.post(this.make_read_only_url, formData).then((response) => {
                if (response.data.notPermitted) {
                    toastr.error("You can't perform this action!");
                } else {
                    toastr.success('Your group successfully mark as read only');
                    window.location.href = response.data.url;
                }
            }).catch(err => console.log(err));
        },

        assign_role_to_user() {
            const formData = new FormData();
            formData.append('user_id', this.assignable_user);
            formData.append('role_id', this.assignable_role);
            formData.append('group_id', this.group.id);
            axios.post(this.assign_role_url, formData).then((response) => {
                if (response.data.notPermitted) {
                    toastr.error("You can't perform this action!");
                } else {
                    toastr.success('User in a specific rule!');
                    window.location.reload();
                }
            }).catch(err => console.log(err));
        },

        reply(c) {
            this.replying = true;
            this.replying_text = c.message;
            this.replying_to = c.id;
        },

        cleanReply() {
            this.replying = false;
            this.replying_text = '';
            this.replying_to = null;
        },

        quoteClosePreview() {
            this.replying = false;
        },

        forward(c, u, id) {
            const btn = document.getElementById('forwordClick' + id);
            if (btn) {
                btn.textContent = ' Sent';
                btn.className = 'bg-less';
            }
            this.cleanReply();
            c.to_id = u.id;
            c.from_id = this.user.id;
            c.group_id = this.group.id;
            c.forward = c.id;
            c.message = this.forward_addon_text || null;
            axios.post(this.forward_message_url, c).catch(err => console.log(err));
        },

        forwardModalOpen(c) {
            this.forward_conversation = c;
            const modal = document.getElementById('forward');
            if (modal && jQuery) jQuery(modal).modal('show');
        },

        imageViewLargeScreen(url) {
            this.imageSrc = url;
            const modal = document.getElementById('imageView');
            if (modal && jQuery) jQuery(modal).modal('show');
        },

        search() {
            this.cleanReply();
            this.search_index = 0;
            this.search_result_ids = [];
            this.search_result_count = 0;
            const modal = document.getElementById('chat_search');
            if (modal && jQuery) jQuery(modal).modal('hide');
            this.search_result_bar = true;
            const elements = document.getElementsByClassName('textmsg');
            for (let i = 0; i < elements.length; i++) {
                if (elements[i].innerHTML.indexOf(this.keywords) > -1) {
                    this.search_result_count += 1;
                    this.search_result_ids.push(elements[i].id);
                }
            }
            for (let k = 0; k < elements.length; k++) {
                if (elements[k].innerHTML.indexOf(this.keywords) > -1) {
                    const targetElement = document.getElementById(elements[k].id);
                    this.scrollIfNeeded(targetElement, document.getElementById('chat_container'));
                    if (jQuery) jQuery('#' + elements[k].id).css('background-color', 'rgb(177 168 104)');
                    break;
                }
            }
        },

        scroll_to_search() {
            if (this.search_index + 1 < this.search_result_ids.length) {
                this.search_index++;
                const el = document.getElementById(this.search_result_ids[this.search_index]);
                this.scrollIfNeeded(el, document.getElementById('chat_container'));
                if (jQuery) jQuery('#' + this.search_result_ids[this.search_index]).css('background-color', 'rgb(177 168 104)');
            }
        },

        scroll_to_search_up() {
            if (this.search_index >= 1) {
                this.search_index--;
                const el = document.getElementById(this.search_result_ids[this.search_index]);
                this.scrollIfNeeded(el, document.getElementById('chat_container'));
                if (jQuery) jQuery('#' + this.search_result_ids[this.search_index]).css('background-color', 'rgb(177 168 104)');
            }
        },

        scrollIfNeeded(element, container) {
            if (!element || !container) return;
            if (element.offsetTop < container.scrollTop) {
                container.scrollTop = element.offsetTop - 70;
            } else {
                const offsetBottom = element.offsetTop + element.offsetHeight;
                const scrollBottom = container.scrollTop + container.offsetHeight;
                if (offsetBottom > scrollBottom) {
                    container.scrollTop = offsetBottom - container.offsetHeight;
                }
            }
        },

        close_search_bar() {
            const elements = document.getElementsByClassName('textmsg');
            for (let i = 0; i < elements.length; i++) {
                if (elements[i].innerHTML.indexOf(this.keywords) > -1) {
                    if (jQuery) jQuery('#' + elements[i].id).css('background-color', 'unset');
                }
            }
            this.keywords = '';
            this.search_result_bar = false;
            this.search_result_count = 0;
            this.search_result_ids = [];
            this.search_index = 0;
        },

        loadMore() {
            this.current_conversation_ids = [];
            for (let c in this.only_threads) {
                this.current_conversation_ids.push(this.only_threads[c].id);
            }
            const formData = new FormData();
            formData.append('ids', JSON.stringify(this.current_conversation_ids));
            formData.append('group_id', this.group.id);
            axios.post(this.load_more_url, formData).then((response) => {
                if (response.data.success) {
                    if (response.data.threads) {
                        for (let index in response.data.threads) {
                            this.only_threads.unshift(response.data.threads[index]);
                        }
                    } else {
                        this.loadable = false;
                    }
                } else {
                    toastr.error('Oops! something went wrong!');
                }
            }).catch(err => console.log(err));
        }
    }));

    Alpine.data('chatNotification', (data = {}) => ({
        unreads: data.unreads || [],
        redirect_url: data.redirect_url || '',
        user_id: data.user_id || 0,
        asset_type: data.asset_type || '',
        mark_all_as_read_url: data.mark_all_as_read_url || '',
        count_unread: 0,
        open_modal: false,

        init() {
            this.count_unread = this.unreads.length;
            if (window.Echo) {
                Echo.private('App.Models.User.' + this.user_id).notification((notification) => {
                    this.unreads.push(notification);
                    this.count_unread += 1;
                    const sound = document.getElementById('sound');
                    if (sound) {
                        sound.pause();
                        sound.currentTime = 0;
                        sound.volume = 0.3;
                        sound.play();
                    }
                });
            }
        },

        getNotificationUrl(unread) {
            if (unread.type === 'Modules\\Chat\\Notifications\\InvitationNotification') {
                if (unread.data && unread.data.url) return unread.data.url + '/' + unread.id;
                if (unread.url) return unread.url + '/' + unread.id;
                return '#';
            }
            if (unread.type === 'Modules\\Chat\\Notifications\\GroupCreationNotification' || unread.type === 'Modules\\Chat\\Notifications\\GroupMessageNotification') {
                if (unread.data && unread.data.url) return unread.data.url;
                if (unread.url) return unread.url;
                return '#';
            }
            if (unread.data && unread.data.user) return this.redirect_url + '/' + unread.data.user.id + '/' + unread.id;
            if (unread.user) return this.redirect_url + '/' + unread.user.id + '/' + unread.id;
            return '#';
        },

        getNotificationTitle(unread) {
            if (unread.type === 'Modules\\Chat\\Notifications\\InvitationNotification') {
                if (unread.data && unread.data.user) return unread.data.user.first_name;
                if (unread.user) return unread.user.first_name;
                return '';
            }
            if (unread.type === 'Modules\\Chat\\Notifications\\GroupCreationNotification' || unread.type === 'Modules\\Chat\\Notifications\\GroupMessageNotification') {
                if (unread.data && unread.data.group) return unread.data.group.name;
                if (unread.group) return unread.group.name;
                return '';
            }
            if (unread.data && unread.data.user) return unread.data.user.first_name;
            if (unread.user) return unread.user.first_name;
            return '';
        },

        getNotificationMessage(unread) {
            if (unread.type === 'Modules\\Chat\\Notifications\\InvitationNotification') {
                if (unread.data) return unread.data.message;
                return unread.message || '';
            }
            if (unread.type === 'Modules\\Chat\\Notifications\\GroupCreationNotification') {
                return 'You are invited in new group!';
            }
            if (unread.type === 'Modules\\Chat\\Notifications\\GroupMessageNotification') {
                return 'New message in this group!';
            }
            if (unread.thread) return unread.thread.message;
            if (unread.data && unread.data.thread) return unread.data.thread.message;
            return '';
        }
    }));

    Alpine.data('jqueryChatNotification', (data = {}) => ({
        unreads: data.unreads || [],
        redirect_url: data.redirect_url || '',
        user_id: data.user_id || 0,
        asset_type: data.asset_type || '',
        mark_all_as_read_url: data.mark_all_as_read_url || '',
        check_new_notification_url: data.check_new_notification_url || '',
        count_unread: 0,
        open_modal: false,
        intervalId: null,

        init() {
            this.count_unread = this.unreads.length;
            this.intervalId = window.setInterval(() => this.checkNewMessageNoti(), 10000);
        },

        destroy() {
            if (this.intervalId) clearInterval(this.intervalId);
        },

        checkNewMessageNoti() {
            const result = this.unreads.map(a => a.id);
            const formData = new FormData();
            formData.append('notification_ids', JSON.stringify(result));
            axios.post(this.check_new_notification_url, formData).then((response) => {
                if (response.data.invalid) return;
                if (response.data.notifications.length > 0) {
                    for (const key of Object.keys(response.data.notifications)) {
                        this.unreads.push(response.data.notifications[key]);
                        this.count_unread += 1;
                    }
                    this.sound();
                }
            }).catch(err => console.log(err));
        },

        sound() {
            const sound = document.getElementById('sound');
            if (sound) {
                sound.pause();
                sound.currentTime = 0;
                sound.volume = 0.3;
                sound.play();
            }
        },

        getNotificationUrl(unread) {
            if (unread.type === 'Modules\\Chat\\Notifications\\InvitationNotification') {
                if (unread.data && unread.data.url) return unread.data.url + '/' + unread.id;
                if (unread.url) return unread.url + '/' + unread.id;
                return '#';
            }
            if (unread.type === 'Modules\\Chat\\Notifications\\GroupCreationNotification' || unread.type === 'Modules\\Chat\\Notifications\\GroupMessageNotification') {
                if (unread.data && unread.data.url) return unread.data.url;
                if (unread.url) return unread.url;
                return '#';
            }
            if (unread.data && unread.data.user) return this.redirect_url + '/' + unread.data.user.id + '/' + unread.id;
            if (unread.user) return this.redirect_url + '/' + unread.user.id + '/' + unread.id;
            return '#';
        },

        getNotificationTitle(unread) {
            if (unread.type === 'Modules\\Chat\\Notifications\\InvitationNotification') {
                if (unread.data && unread.data.user) return unread.data.user.first_name;
                if (unread.user) return unread.user.first_name;
                return '';
            }
            if (unread.type === 'Modules\\Chat\\Notifications\\GroupCreationNotification' || unread.type === 'Modules\\Chat\\Notifications\\GroupMessageNotification') {
                if (unread.data && unread.data.group) return unread.data.group.name;
                if (unread.group) return unread.group.name;
                return '';
            }
            if (unread.data && unread.data.user) return unread.data.user.first_name;
            if (unread.user) return unread.user.first_name;
            return '';
        },

        getNotificationMessage(unread) {
            if (unread.type === 'Modules\\Chat\\Notifications\\InvitationNotification') {
                if (unread.data) return unread.data.message;
                return unread.message || '';
            }
            if (unread.type === 'Modules\\Chat\\Notifications\\GroupCreationNotification') {
                return 'You are invited in new group!';
            }
            if (unread.type === 'Modules\\Chat\\Notifications\\GroupMessageNotification') {
                return 'New message in this group!';
            }
            if (unread.thread) return unread.thread.message;
            if (unread.data && unread.data.thread) return unread.data.thread.message;
            return '';
        }
    }));
});

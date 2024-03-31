<template>

    <div v-if="comments_display === 1" class="mt-2">
        <div v-if="final_response.message" class="alert" :class="final_response.response_type === 200 ? 'alert-success' : 'alert-danger'" v-html="final_response.message"></div>

        <form @submit.prevent="save_comments">

            <div class="d-flex justify-content-end my-1">
                <button :disabled="save_btn_disabled" type="submit" class="btn btn-light border border-1" style="max-width: fit-content;">
                    Publish
                    <img src="https://cdn-icons-png.flaticon.com/512/1828/1828817.png" width="30" alt="">
                </button>
            </div>

            <div class="form-groups" :style="{ display: mentioner_hidden ? 'none' : '' }">
                <select @change="select_mentioner" class="form-control mention_dropdown" :style="{ display: mentioner_hidden ? 'none' : '' }">
                    <option value=""  selected>Select User</option>
                    <option :value="mention" v-for="(mention,email) in mentions" :key="email">{{mention}}</option>
                </select>
            </div>
            <Editor
                @keypress="handle_key_press"
                v-model="comments"
                api-key="otec0248jrnb9offtkqc0pwpbaq7lfaycpnu5ziypnyfaws1"
                :init="{
                     placeholder: 'Enter Comments',
                     toolbar_mode: 'sliding',
                     toolbar: false,
                     menubar:false,
                     plugins: 'anchor  charmap codesample emoticons     searchreplace  visualblocks wordcount',
                     toolbar: 'undo redo codesample| blocks fontfamily fontsize | bold italic underline strikethrough | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                  }"

            />

        </form>
    </div>

</template>

<script >
    import common from "../../template_setups/common/common";
    import Editor from '@tinymce/tinymce-vue';
    // import Select2 from 'vue3-select2-component';

    export default {
        name: "create_comments",
        components:{
            Editor,
        },
        data(){
            return {
                comments : '',
                final_response : {
                    message : '',
                    response_type : '',
                    reset_allowed : true
                },

                save_btn_disabled : false,
                mentioner_hidden : true,
                mentioner_selected : '',

                mentions : []
            }
        },
        props:{
            comments_display : Number,
            feedback_id : String,
            mentioned_user : String
        },
        mounted() {
            console.log(common.get_base_url()+'/comments-manager');

            this.get_mentioners();
        },

        methods : {
            save_comments(){
                this.save_btn_disabled = true;
                axios.post(common.get_base_url()+'/comments-manager',{
                    feedback_id : this.feedback_id,
                    comments : this.comments
                }).then((response)=>{
                    this.$emit('feedback_comments',response.data.data);
                    console.log(response);
                    this.save_btn_disabled = false;
                    this.comments = '';
                }).catch((error)=>{
                    this.final_response.message = common.error_ui(error);
                    this.final_response.response_type = error.response.status;
                    this.save_btn_disabled = false;
                })
            },

            handle_key_press(data){

                if(data.key === '@' && Object.keys(this.mentions).length){
                    this.mentioner_hidden = false;
                    $('.mention_dropdown').prop('selectedIndex', 0);
                    return false;
                }
                else {
                    this.mentioner_hidden = true;
                }
            },
            get_mentioners(){

                    axios.get(common.get_base_url()+'/get-mentions').then((response)=>{
                        console.log(response);
                        this.mentions = response.data;
                    }).catch((error)=>{
                        console.log(error);
                    })

            },

            select_mentioner(data){
                if(data.target.value)
                    this.append_mentioners(data.target.value);
                this.mentioner_hidden = true;
            },

            append_mentioners(val){
                this.comments = (this.comments ? this.comments + ' ' : '') + '<span style="color: blue"><u><b>@' + val + '</b></u><span style="color: black">&nbsp;</span></span>';
            }

        },
        watch : {
            'comments'(newVal){
                console.log(newVal);
                this.final_response.message = ''
            },
            'mentioned_user'(newVal) {

                if (newVal && !this.comments.includes('@'+newVal)) {
                    this.append_mentioners(newVal);
                }
            },
            'feedback_id'(newVal){
                this.comments = ''
            },

        }
    }
</script>

<style >

    .mention_dropdown{
        position: relative !important;
        top: 133px !important;
        z-index: 9 !important;
        left: 72px !important;
        width: 250px !important;
    }
    .form-groups{
        position: absolute;
    }

</style>

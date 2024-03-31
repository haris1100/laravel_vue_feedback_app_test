<template>
    <div>
        <hr>
        <div v-if="final_response.message" class="alert" :class="final_response.response_type === 200 ? 'alert-success' : 'alert-danger'" v-html="final_response.message"></div>
        <div class="d-flex justify-content-between">
            <div>
                <strong>Title:</strong>
                {{ feedback_detail.title }}
            </div>
            <router-link to="/app/dashboard/feedback" style="text-decoration: none;">❌</router-link>
        </div>

        <strong>Category:</strong>
            {{feedback_detail?.category?.replace('_',' ')}}
        <br>
        <strong>Created By:</strong>
        {{feedback_detail?.users_linked?.name}}
        <br>
        <div class="break-after-words border border-2 p-2 rounded-4 mt-1">

            <strong>Feedback:</strong>
            <br>
                {{feedback_detail.description}}
        </div>
        <div class="border border-1 rounded-4 mt-2">
            <div class="d-flex justify-content-end p-2">
                <img src="https://cdn-icons-png.flaticon.com/512/889/889140.png" width="30" alt="" class="me-3">
                <img src="	https://cdn-icons-png.flaticon.com/512/1828/1828874.png" width="30" alt="" class="me-3">
                <a @click="toggle_comments" href="javascript:void(0)"><img src="https://cdn-icons-png.flaticon.com/512/2593/2593482.png" width="30" alt=""></a>
            </div>
        </div>
        <create_comments :mentioned_user="mentioned_user" @feedback_comments="get_comments" :feedback_id="this.$route.params.id" :comments_display="parseInt(display_comments)"></create_comments>

        <div class="mt-4 mb-5" v-if="feedback_detail?.comments?.length" >
            <strong>Comments</strong>
            <div style="max-height: 300px;overflow: auto">
                <div class="border border-1 border-dark rounded-4 p-2 my-2" v-for="comment in feedback_detail.comments" :key="comment.id">
                    <a :style="{ cursor: comment.users_linked.id === get_user_id ? 'not-allowed' : 'pointer' }" href="javascript:void(0)" @click="mentioned(comment.users_linked.name,comment.users_linked.id)" class="fs-tiny text-dark"><u>{{comment.users_linked.name}}</u></a>
                    <p class="text-secondary" style="font-style: italic" v-html="comment.comments"></p>
                    <p class="text-secondary text-end p-0" style="font-style: italic;font-size: 10px;margin: 0" v-html="comment.created_at"></p>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    import common from "../../template_setups/common/common";
    import create_comments from "./create_comments.vue";
    export default {
        components:{
            create_comments
        },
        data(){
           return {

               feedback_detail : {},
               final_response : {
                   message : '',
                   response_type : '',
                   reset_allowed : true
               },
               display_comments : parseInt(this.$route.query?.comments_toggle ?? 0),
               mentioned_user : ''
           }
        },
        mounted() {
            console.log(this.display_comments);
              this.get_feedback_detail();
        },
        computed:{

                get_user_id(){
                    return window.user_id;
                }

        },

        methods:{
            get_feedback_detail(feedback_id = this.$route.params.id){
                axios.get(common.get_base_url()+'/feedback/'+feedback_id).then((response)=>{
                    this.feedback_detail = response.data;
                }).catch((error)=>{
                    this.final_response.message = common.error_ui(error);
                    this.final_response.response_type = error.response.status;
                })
            },
            toggle_comments(){
                // console.log(this.display_comments);
                this.display_comments = this.display_comments === 1 ? 0 : 1;
                // console.log(this.display_comments);
                this.$router.push({query : {comments_toggle : this.display_comments}})
            },
            get_comments(data){
                console.log('commentes_recieved',data);
                console.log('data', this.feedback_detail);
                this.feedback_detail.comments.push(data);
                this.feedback_detail.comments.sort((a, b) => b.id - a.id);
                this.$emit('total_comments_updated',{
                    feedback_id    :  this.$route.params.id,
                    comments_count :  this.feedback_detail.comments.length
                });
                this.display_comments = 0;
            },
            mentioned(user_name,id){
                if(window.user_id !== id)
                this.mentioned_user = user_name;
            },

        },
        watch: {
            '$route' (to, from) {

                this.get_feedback_detail(to.path.split('/').pop());
            }
        }
    }
</script>

<style scoped>
    .break-after-words::after {
        content: '\A'; /* '\A' is a line break character in CSS */
        white-space: pre; /* Preserve white space */
    }

    /* Add a space after specific words to ensure the line break works properly */
    .break-after-words::after {
        content: '\A'; /* '\A' is a line break character in CSS */
        white-space: pre; /* Preserve white space */
    }

    .break-after-words::after {
        content: '\A'; /* '\A' is a line break character in CSS */
        white-space: pre; /* Preserve white space */
    }
</style>

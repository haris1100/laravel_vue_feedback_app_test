<template>
    <div class="container">

        <div class="row border border-2 rounded px-2">
            <div class="col-sm-12">
                <div class="d-flex justify-content-end mt-2">
                    <div class="px-3">
                        <router-link  @click="manage_feedback_opener()" class="btn btn-light border border-1" :to="{ path: feedback_creation_manager.paths[feedback_creation_manager.mode] }">{{feedback_creation_manager.mode === 'open' ? 'Close' : 'Add'}}
                            <img :src="feedback_creation_manager.mode === 'open' ? 'https://cdn-icons-png.flaticon.com/512/10308/10308996.png' : 'https://cdn-icons-png.flaticon.com/512/1828/1828817.png'"  width="30" alt=""> </router-link>
                    </div>
                </div>
            </div>
            <div :class="!display_table_only?'col-sm-12 col-md-6 col-lg-6' : 'col-sm-12'">
                <div class="" style="overflow: auto; width: 100%">
                    <div class="table-alignment">
                        <table class=" table table-hover table-striped table-bordered" v-if="feedback_data.length && feedback_loaded">
                            <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Created By</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="feedback in feedback_data" :key="feedback.id">
                                <td>{{feedback.title}}</td>
                                <td>{{feedback.category.replace('_',' ')}}</td>
                                <td>{{feedback.users_linked.name}}</td>
                                <td class="text-end">
                            <span v-show="feedback.comments_count" class="comments-wrapper">
                              <span class="comments-count">{{feedback.comments_count}}</span>

                            </span>
                                    <router-link :to="{path :'/app/dashboard/feedback/comments/'+feedback.id}"><img src="https://cdn-icons-png.flaticon.com/512/2190/2190510.png" width="30" alt=""></router-link>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <p v-else-if="feedback_loaded && !feedback_data.length">
                            No Feedback found yet. Please create one.
                        </p>
                        <p v-else>Please Wait, We are loading the feedbacks ...</p>
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-2" v-if="feedback_data.length">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <li class="page-item">
                                <button @click="move_to_page(--pagination.current_page)" :disabled="pagination.current_page === 1" class="page-link btn ">Previous</button>
                            </li>
                            <li v-for="item in pagination.paginated_items" :key="item" class="page-item">
                                <button @click="move_to_page(item)" :disabled="pagination.current_page === item" class="page-link btn" :class="{'btn-primary' : pagination.current_page === item}">{{ item }}</button>
                            </li>
                            <li class="page-item">
                                <button @click="move_to_page(++pagination.current_page)" :disabled="pagination.current_page === pagination.total_pages" class="page-link btn">Next</button>
                            </li>
                        </ul>
                    </nav>

                </div>
            </div>
            <div :class="display_creation_only ? 'col-sm-12':'col-sm-12 col-md-6 col-lg-6'">
                <router-view  @total_comments_updated="comments_count_updated" @feedback_created="manage_feedback_creation"></router-view>
            </div>
        </div>



    </div>
</template>

<script>
    import common from "../../template_setups/common/common";

    export default {
        data() {
            return {
                feedback_data : [],
                feedback_loaded : false,

                feedback_creation_manager : {
                    mode : this.$route.path.split('/').pop() === 'create' ? 'open' : 'close',
                    paths : {
                        open: '/app/dashboard/feedback/',
                        close: '/app/dashboard/feedback/create',
                    }
                },

                pagination : {
                    current_page: this.$route.query?.page ?? 1,
                    total_pages: 1,
                    paginated_items: []
                },

                current_path : this.$route.path,

            };
        },
        created() {
            // console.log('created',this.feedback_creation_manager);
        },
        updated() {
            console.log(this.$route);
        },
        computed:{

            display_table_only(){
                return  ['/app/dashboard/feedback/','/app/dashboard/feedback'].includes(this.current_path);
            },
            display_creation_only(){
                return this.feedback_data.length === 0;
            }

        },
        mounted() {
            this.feedback_loader();
            console.log('mounted',this.pagination.paginated_items);
        },
        methods : {
            creation_mode_check(path){
                    this.feedback_creation_manager.mode =  path.path.split('/').pop() === 'create' ? 'open' : 'close'
            },
            manage_feedback_opener(){
                this.feedback_creation_manager.mode = this.feedback_creation_manager.mode === 'open' ? 'close' : 'open';
            },
            feedback_loader(){

                    axios.get(common.get_base_url()+'/feedback?page='+this.pagination.current_page).then(response => {
                        this.feedback_data = response.data.data;
                        this.pagination.current_page = response.data.current_page;
                        this.pagination.total_pages = response.data.last_page;
                        this.pagination.paginated_items = Array.from({ length: this.pagination.total_pages }, (_, index) => index + 1);
                        // console.log('feedback_recieved',response);
                        this.feedback_loaded = true;
                    });

            },
            manage_feedback_creation(data){

                // console.log('recieved',data);
                this.feedback_data.push(data);
                this.feedback_data.sort((a, b) => b.id - a.id);
                this.manage_feedback_opener();
                this.current_path = this.$route.path;
                this.self_loader();
            },

            move_to_page(page_no){

                this.pagination.current_page = page_no;
                this.feedback_loader();
                this.$router.push({ query: { page: page_no } });
            },

            self_loader(){
                if( this.feedback_data.length % 11 === 0 ){
                    this.feedback_loader();
                }
            },
            comments_count_updated(data){
                console.log('data_rec',data);
                let feedback_index = this.feedback_data.findIndex(feed => feed.id == data.feedback_id);
                console.log(feedback_index, this.feedback_data[feedback_index]);
                this.feedback_data[feedback_index].comments_count = data.comments_count;
                console.log(this.feedback_data[feedback_index].comments_count);
            }
        },
        watch: {
            '$route' (to, from) {
               this.creation_mode_check(to);
                this.current_path = to.path;
                // console.log('Route changed from', from, 'to', to);

                // You can perform any actions based on the route change here
            },

        }
    };
</script>

<style>
    .comments-count {
        background-color: red;
        color: white;
        border-radius: 50%;
        width: 21px;
        height: 21px;
        font-size: 12px;
        text-align: center;
        line-height: 21px;
        position: relative;
        top:10px;
        left:9px;
    }

    .comments-wrapper {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .table-alignment{
        margin-top: 18px;
    }

</style>

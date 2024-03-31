<template>

    <form @submit.prevent="submit_feedback">
        <div class="row border border-1 rounded-4 my-3 p-3">
            <div v-if="final_response.message" class="alert" :class="final_response.response_type === 200 ? 'alert-success' : 'alert-danger'" v-html="final_response.message"></div>

            <h4>Add Feedback</h4>

                <div class="col-sm-12 col-lg-6">
                    <div class="form-group">
                        <label for="">Title</label>
                        <input class="form-control" type="text" v-model="feedback_form.title">
                        <small class="text-danger" v-if="feedback_form_validation.title">{{feedback_form_validation.title}}</small>
                    </div>
                </div>
                <div class="col-sm-12 col-lg-6">
                    <div class="form-group">
                        <label for="">Category</label>
                        <select class="form-control" v-model="feedback_form.category">
                            <option v-for="cat in feedback_default_categories" :key="cat.id" :value="cat.name">{{cat.text}}</option>
                        </select>
                        <small class="text-danger" v-if="feedback_form_validation.category">{{feedback_form_validation.category}}</small>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="">Description</label>
                        <textarea v-model="feedback_form.description" class="form-control" id="" cols="30" rows="5"></textarea>
                        <small class="text-danger" v-if="feedback_form_validation.description">{{feedback_form_validation.description}}</small>
                    </div>
                </div>

            <div class="col-sm-12">

                <div class="d-flex justify-content-center">
                    <div class="px-3">
                            <button :disabled="save_btn_disabled" type="submit" class="btn btn-light border border-1"><img src="https://cdn-icons-png.flaticon.com/512/907/907027.png" width="25" alt=""> Add Feedback</button>
                    </div>
                </div>

            </div>

        </div>
    </form>
</template>

<script>
    import common_helpers from "../../template_setups/common/common";

    export default {
        name: "create_feedback",
        data()  {
            return {

                    feedback_form : {
                        title : '',
                        description : '',
                        category : 'bug_report'
                    },

                    feedback_form_validation : {
                        title : '',
                        description : '',
                        category : ''
                    },

                    feedback_default_categories : [
                        { id : 1, name : 'bug_report' , text : 'Bug report'},
                        { id : 2, name : 'feature_request',  text : 'Feature request'},
                        { id : 3, name : 'improvement',  text : 'Improvements'}
                    ],

                    final_response : {
                        message : '',
                        response_type : '',
                        reset_allowed : true
                    },

                    save_btn_disabled : false

            }
        },
        methods : {

                 validate_feedback() {
                    let feedback_form = this.feedback_form;
                    let instance = this;
                    let is_valid = true; // Flag to track overall validation result

                    for (let field of Object.keys(feedback_form)) {
                        if (feedback_form[field] === '') {
                            instance.feedback_form_validation[field] = 'Please enter the ' + field;
                            is_valid = false; // Set flag to false if any field fails validation
                        }
                    }

                    return is_valid; // Return overall validation result after checking all fields
                },


             submit_feedback(){
                    console.log(this.validate_feedback());
                    this.save_btn_disabled = true;
                    if( this.validate_feedback()) {

                        axios.post('/feedback', this.feedback_form).then(response => {
                            // console.log(response);
                            this.final_response.message =  'Feedback Created Successfully';
                            this.final_response.response_type = 200;
                            this.final_response.reset_allowed = false;
                            this.reset_form();
                            this.save_btn_disabled = false;
                            setTimeout(() => {

                                this.$emit('feedback_created', response.data.data);
                                this.final_response.reset_allowed = true;
                                this.$router.push({path : '/app/dashboard/feedback'})

                            },1000);
                        }).catch(error => {
                            this.final_response.message = common_helpers.error_ui(error);
                            this.final_response.response_type = error.response.status;
                            console.log(error);
                            this.save_btn_disabled = false;
                        })

                    }
                    else
                        this.save_btn_disabled = false;

                },

                reset_form() {
                    this.feedback_form.title = '';
                    this.feedback_form.description = '';
                    this.feedback_form.category = this.feedback_default_categories[0].name;
                },

                reset_alert_message(){
                     if(this.final_response.reset_allowed)
                        this.final_response.message = '';
                }


        },
        watch: {
            'feedback_form.title'(newValue, oldValue) {
                this.feedback_form_validation['title'] = '';
                this.reset_alert_message();
            },
            'feedback_form.description'(newValue, oldValue) {
                this.feedback_form_validation['description'] = '';
                this.reset_alert_message();
            },
            'feedback_form.category'(newValue, oldValue) {
                this.feedback_form_validation['category'] = '';
                this.reset_alert_message();
            }
        }
    }
</script>

<style scoped>

</style>

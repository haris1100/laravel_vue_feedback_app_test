<template>
    <div class="login-card">
        <div v-if="final_response.message" class="alert" :class="final_response.response_type === 200 ? 'alert-success' : 'alert-danger'" v-html="final_response.message"></div>
        <form @submit.prevent="submit_form">
            <h3 class="text-center">{{ default_template_setup }}</h3>

                <div class="form-group" v-for="input in form" :key="input.id" v-show="input.allowed_for.includes(default_template_setup)">
                    <label for="">{{input.label}}</label>
                    <input :type="input.type" v-model="input.value" :name="input.name" @input="validate_fields(input)" class="form-control">
                    <small class="text-danger">{{ input.validation_msg }}</small>
                </div>

            <button :disabled="submit_button_disabled" type="submit" class="btn btn-outline-primary  mt-3">{{ default_template_setup  == 'LOGIN' ? 'Login' : 'Register' }}
                <img src="https://cdn-icons-png.flaticon.com/512/1828/1828466.png" width="25" alt=""></button>

            <p class="text-center mt-2">
                <span v-if="default_template_setup == 'LOGIN'">Don't have account? </span>
                <span v-else="">Already have account? </span>
                <a @click="switch_forms()" href="javascript:void(0)">Sign {{ default_template_setup == 'LOGIN' ? 'up' : 'in' }}</a>
            </p>
        </form>
    </div>
</template>

<script>

    import form_fields from '../template_setups/auth_template/fields.json';
    import axios from 'axios';
    import common_helpers from '../template_setups/common/common.js';



    export default {
        components : {

        },
        data() {
            return {
                test_bind : '',

                default_template_setup : 'LOGIN',

                template_configurations : {
                    LOGIN : {
                        post_url : 'login',
                    },
                    REGISTRATION : {
                        post_url : 'register'
                    }
                },

                form: form_fields,

                submit_button_disabled : false,

                final_response : {
                    message : '',
                    response_type : ''
                },



            };
        },
        mounted() {

            // console.log(JSON.stringify(this.form))
        },
        methods : {

            switch_forms(){

                    this.default_template_setup = this.default_template_setup === 'LOGIN' ? 'REGISTRATION' : 'LOGIN';
                    this.validate_fields(null);
            },

            validate_fields(input = null){

                if(input)
                    input.validation_msg = '';
                this.submit_button_disabled = false;
                this.final_response.message = '';
                    // switch(field_name){

                    //     case 'email' : this.validat_email(false);break;
                    //     case 'name' : this.validat_name(false);break;
                    //     case 'password' : this.validat_password(false);break;

                    // }

            },

            validat_email() {

                let ge_email_field = this.form.filter(field => field.name == 'email')[0];
                if(ge_email_field.value.length == 0){
                    ge_email_field.validation_msg = "Please enter the email";
                    return false;
                }
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                let validate = emailRegex.test(ge_email_field.value);
                if(!validate){
                    ge_email_field.validation_msg = "Email Should have right format!";
                    return false;
                }


                return true;

            },

            validat_password() {
                let ge_password_field = this.form.filter(field => field.name == 'password')[0];

                if(ge_password_field.value.length < 6){
                    ge_password_field.validation_msg = "Password Should have length of 6 characters or more.";
                    return false;
                }

                return true;

            },

            validate_confirm_password(){
                let ge_password_field = this.form.filter(field => field.name == 'password')[0];
                let ge_password_confirmation_field = this.form.filter(field => field.name == 'password_confirmation')[0];
                if(ge_password_field.value !== ge_password_confirmation_field.value){
                    ge_password_confirmation_field.validation_msg = "Password doesn't match!";
                    return false;
                }
                return true;

            },

            validate_name(){
                let ge_name_field = this.form.filter(field => field.name == 'name')[0];
                if(ge_name_field.value.length == 0){
                    ge_name_field.validation_msg = "Please enter the name";
                    return false;
                }

                return true;

            },

            validate_availability() {

                let errors = 0;

                switch (this.default_template_setup) {
                    case 'LOGIN':
                    errors += this.validat_email() ? 0 : 1;
                    errors += this.validat_password() ? 0 : 1;
                    break;
                    case 'REGISTRATION':
                    errors += this.validat_email() ? 0 : 1;
                    errors += this.validat_password() ? 0 : 1;
                    errors += this.validate_confirm_password() ? 0 : 1;
                    errors += this.validate_name() ? 0 : 1;
                    break;
                }

                return errors === 0;
            },

            submit_form(){

                this.submit_button_disabled = true;

                if(!this.validate_availability()){
                    console.log('validation failed');
                    return false;
                }

                let formData = this.form.filter(field => field.allowed_for.includes(this.default_template_setup)).reduce((accumulator, field) => {
                    accumulator[field.name] = field.value;
                    return accumulator;
                }, {});
                console.log(formData);
                axios.post(this.template_configurations[this.default_template_setup].post_url,formData)
                .then(response => {
                    console.log('Response:', response.data);
                    this.final_response.message =  common_helpers.success_ui(response.data);
                    this.final_response.response_type = 200;
                    this.submit_button_disabled = false;
                    window.auth_user = true;
                    this.reset_form();
                    this.$router.push({ path: 'app/dashboard/feedback' });
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.final_response.message = common_helpers.error_ui(error);
                    this.final_response.response_type = error.response.status;
                    this.submit_button_disabled = false;
                });

            },

            reset_form(){
                let form = this.form;
                Object.keys(form).map(function (key) {
                        form[key].value = '';
                })
            }
        }
    }
</script>

<style lang="css">

@import url('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css');
@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
@import '/resources/css/app.css';
@import '/resources/css/auth.css';

</style>

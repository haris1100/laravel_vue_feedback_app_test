export default {
    error_ui: function (data) {
        let errors = (data.response.data);
        let ul = '<ul>';
        Object.keys(errors).map(function(key){
                try {
                    if(typeof errors[key] === 'object' && errors[key] !== null)
                        Object.keys(errors[key]).map(function(sub_key) {
                            ul += '<li>' + errors[key][sub_key] + '</li>';
                        })
                    else {
                        ul += '<li>' + errors[key] + '</li>';
                    }
                } catch (err) {
                    ul += '<li>' + errors[key] + '</li>';
                }
        });
        ul += '</ul>';
        return ul;
    },

    success_ui: function (data) {
        let errors = (data);
        let ul = '<ul>';
        Object.keys(errors).map(function(key){
            try {
                if(typeof errors[key] === 'object' && errors[key] !== null)
                    Object.keys(errors[key]).map(function(sub_key) {
                        ul += '<li>' + errors[key][sub_key] + '</li>';
                    })
                else {
                    ul += '<li>' + errors[key] + '</li>';
                }
            } catch (err) {
                ul += '<li>' + errors[key] + '</li>';
            }
        });
        ul += '</ul>';
        return ul;
    },

    validate_auth : async function(){

            await axios.post('validate_auth')
                .then(response => {
                    console.log(response.data);
                    this.set_user_auth(response.data);
                    return response.data;
                })
                .catch(error => {
                    this.set_user_auth();
                    return false;
                })

    },

    set_user_auth : function(auth = false){
            window.auth_user = auth;
    },

    get_base_url : function(){
        return window.base_url;
    }

}


// import Vue from 'vue';
import { createRouter, createWebHistory } from "vue-router";
import common from "../resources/js/template_setups/common/common.js";
import Dashboard from '../resources/js/components/dashboard.vue';
import mainComponent from '../resources/js/components/mainComponent.vue';
import feedback_listing from "../resources/js/components/child/feedback_listing.vue";
import create_feedback from "../resources/js/components/child/create_feedback.vue";
import comments from "../resources/js/components/child/comments.vue";

const router = createRouter({
    history: createWebHistory(),
    routes: [

        {
            path: '/app/dashboard',
            name: 'dashboard',
            component: Dashboard,
            meta: { requires_auth: true },
            children: [
                {
                    path: 'feedback',
                    component: feedback_listing,
                    meta: { requires_auth: true },
                    children: [
                        {
                            path: 'create',
                            component: create_feedback,
                            meta: { requires_auth: true },
                        },
                        {
                            path: 'comments/:id',
                            component: comments,
                            meta: { requires_auth: true },
                        }
                    ]
                }
            ]
        },
        {
            path: '/app',
            name: 'auth',
            component: mainComponent
        }
    ]
});

router.beforeEach(async (to, from, next) => {
    if (to.meta.requires_auth) {
        try {
            // Call validate_auth function and wait for the response

            if (window.auth_user) {
                next();
            } else {
                next('/app');
            }
        } catch (error) {
            console.error('Error validating user:', error);
            next('/app');
        }
    } else {
        next();
    }
});


export default router

<template>
<div id="jcbApp">
    <site-navbar v-if="authenticated"></site-navbar>
    <site-menubar v-if="authenticated"></site-menubar>
    <div id="PageContent" v-bind:class="{'no-margin' : !authenticated}">
        <router-view></router-view>
    </div>
</div>
</template>

<script>
    import SiteNavbar from './components/SiteNavbar';
    import SiteMenubar from './components/SiteMenubar';
    import CommonService from './services/CommonService'
    const commonService = new CommonService();

    export default {
        data() {
            return {
                authenticated: false,
                commonService
            };
        },
        watch: {
            $route (to_route, from_route) {
                this.authenticated = commonService.is_authenticated();
            }
        },
        components: {
            SiteNavbar,
            SiteMenubar,
        },
        mounted() {
            this.authenticated = commonService.is_authenticated();
        },
        created() {
            this.authenticated = commonService.is_authenticated();
        }
    }
</script>
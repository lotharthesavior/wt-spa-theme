/**
 * This is the Router for the WT SPA
 *
 * @author Savio Resende <savio@savioresende.com.br>
 *
 */

const NotFound = {
    template: '<p>Page not found</p>',
    start: function(){
        $('article').prev().html('Oops');
        $('article').html('This page is not available. ');
    }
};

// const routes = {
const routes = [

    // -------------------------------
    // basic -------------------------
    // -------------------------------
    { path: '/', name: "home", mode: "hash", component: WtPage},
    { path: '/my-account', name: "my-account", mode: "hash", component: WtPage},
    // -------------------------------
    // -------------------------------
    // -------------------------------

    // -------------------------------
    // general action for shortcodes -
    // -------------------------------
    { path: '/:slug/:action?', mode: "hash", component: WtPage}
    // -------------------------------
    // -------------------------------
    // -------------------------------

];

const router = new VueRouter({
    routes: routes // short for `routes: routes`
})

var wt_app = new Vue({
    router: router,
    el: '#theme-space',
    data: {
        currentRoute: window.location.hash.replace('#','')
    },
    computed: {
        ViewComponent () {
            return this.$route.matched[0].components.default || NotFound;
        }
    },
    render (h) {
        this.ViewComponent.route = this.$route;
        this.ViewComponent.start();
    }
});

window.onhashchange = function(){
    wt_app.currentRoute = window.location.hash.replace('#','');
}

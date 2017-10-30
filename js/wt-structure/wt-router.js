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

const routes = {
    // -------------------------------
    // basic -------------------------
    // -------------------------------
    '/': WtPage,
    '/my-account': WtPage,
    // -------------------------------
    // -------------------------------
    // -------------------------------

    // -------------------------------
    // writer ------------------------
    // -------------------------------
    '/writer': WtPage,
    '/writer/new': WtPage
    // -------------------------------
    // -------------------------------
    // -------------------------------
}

var wt_app = new Vue({
    el: '#theme-space',
    data: {
        currentRoute: window.location.hash.replace('#','')
    },
    computed: {
        ViewComponent () {
            return routes[this.currentRoute] || NotFound;
        }
    },
    render (h) {
        this.ViewComponent.start();
    }
});

window.onhashchange = function(){
    wt_app.currentRoute = window.location.hash.replace('#','');
}

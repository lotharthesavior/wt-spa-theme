/**
 *
 * This is a component for wp page control
 *
 * @author Savio Resende <savio@savioresende.com.br>
 *
 */

var $ = jQuery;

const WtPage = {
    start: function(){

        current_location = window.location.hash.replace('#', '');

        if( current_location == '/' )
            current_location = 'home';

        this.callRoute(current_location);

    },

    callRoute: function(current_location) {

        // this is necessary for the first time load when copying
        // and pasting an url
        var verification = (typeof wp.api.models.Page == 'undefined');

        var that = this;

        if( verification ) {

            setTimeout(function () {
                that.callRoute(current_location);
            }, 200);

        } else {

            $('article').prev().append("<span class='loading'>Loading...</span>");

            var pages = new wp.api.models.Page();
            pages.fetch({data: {slug: current_location}}).done(function (result) {
                console.log(result);

                var title = result[0].title.rendered;
                var content = result[0].content.rendered;

                // fix for the ajax return of login on my-account
                content_parsed = content.replace('/wp-json/wp/v2/pages?slug=%2Fmy-account', '/#/my-account');

                $('article').prev().html(title);
                $('article').html(content_parsed);
            });

        }

    }
};
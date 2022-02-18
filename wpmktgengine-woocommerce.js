/**
 * Genoo
 * @type {Genoo|*|*|Object|{}}
 */
var Genoo = Genoo || {};

/**
 * Start Products Import
 * @param event
 */
Genoo.startProducstImport = function(event)
{
    // Prevent default
    if(event.preventDefault) event.preventDefault();
    event.returnValue = null;

    /**
     * Step 1: Start import
     */

    Genoo.startEventLog();
    Genoo.setLog();

    var data = { action: 'wpme_import_products_count'};

    jQuery.post(ajaxurl, data, function(response){

        /**
         * Turn of logger
         */
        Genoo.setLog(false);

        /**
         * Step 2: If we can import, import, display next step message
         */

        if(response.error){
            // No import
            Genoo.addLogMessage(response.error, 0);
        } else {
            // Import
            Genoo.addLogMessage('Importing Products', 0);

            // Prep vars
            var msgs = response.found;
            var msgOffset = 0;
            var msgPer = 50;
            var msgSteps = 1;
            if(msgs > msgPer){ msgSteps = Math.ceil(msgs / msgPer); }
            var msgStep = 0;

            /**
             * Step 3: Loop through steps, catch response
             */

            Genoo.startEventLogIn();
            Genoo.addLogMessage('Started importing products.');
            Genoo.setProgressBar();
            Genoo.progressBar(0);


            /**
             * Step 4: Set up interval, steps that wait for last to finish
             */

            (function importProducts(){

                msgOffset = msgStep * msgPer;
                var temp = { action: 'wpme_import_products', offset: msgOffset, per: msgPer };

                /**
                 * Step 5: Add log message for each comment with success / error.
                 */

                jQuery.post(ajaxurl, temp, function(importResponse){
                    if(Genoo.isArray(importResponse.messages)){
                        for (var i = 0; i < importResponse.messages.length; i++){
                            Genoo.addLogMessage(importResponse.messages[i]);
                        }
                    } else {
                        Genoo.addLogMessage(importResponse.messages);
                    }
                    msgStep++;
                    Genoo.progressBar(Genoo.logPercentage(msgStep, msgSteps));
                    if(msgStep < msgSteps){
                        setTimeout(function(){
                            importProducts();
                        }, 1000);
                    }
                });
            }());
        }
    });
};
/**
 * Created by keniw on 08/10/2015.
 */
/*
 * doAjax
 * @param {type} url
 * @param {type} type
 * @param {type} data
 * @param {type} error
 * @param {type} success
 * @param {type} before
 * @param {type} async
 * @returns {Boolean}
 */
function doAjax(url, type, data, error, success, before, async) {

    if (typeof async === 'undefined') {
        async = false;
    }
    $.ajax({
        url: url,
        async: true,
        type: type,
        data: data,
        error: error,
        success: success,
        beforeSend: before
    });
    return true;
}
/*
 * process_ajax
 * @param string action         Url to controller's method
 * @param object data           Data object
 * @param string additional     Evaluated callback function
 * @param Boolean async         Sets asynchronous execution. Default is true
 * @returns string controller   Url to controller
 */
function process_ajax(action, _data, additional, async, controller) {
    if (typeof async === 'undefined') {
        async = true;
    }
    if (controller == undefined) {
        controller = "ajaxhandler";
    }

    var ajax_url;
    ajax_url = '/'+controller + '/' + action;

    doAjax(
        ajax_url,
        'POST',
        'data=' + encodeURIComponent(JSON.stringify(_data)),
        function (jqXHR, textStatus, errorThrown) {
            console.error('Error in ' + action + ': ' + errorThrown);
        },
        function (s) {
            // alert(s);
            _Query = $.parseJSON(s);
             if (additional != undefined)
			{
				if (typeof additional == 'function')
				{
					additional.call(this, _Query);
				}
				else
				{ 
					eval(additional);
				}
			}
        },
        function () {

        },
        async
    );
}
function processAjax(action, _data, additional) {
    process_ajax(action, _data, additional);
}


function getPageContents(page) {
    var _data = {};
    _data["request"] = "getPageContents";
    _data["page"] = page;
    var requestData = JSON.stringify(_data);
    doAjax(
        '/ajaxhandler',
        'POST',
        'data=' + encodeURIComponent(requestData),
        function (jqXHR, textStatus, errorThrown) {
            console.error(errorThrown);
        },
        function (s) {
            /* alert(s); */
            _Query = $.parseJSON(s);
        }
    );
}
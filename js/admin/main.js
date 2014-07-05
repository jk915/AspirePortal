

function blockObject(obj)
{
     obj.block
     ({
        message: '<div><img src="'+base_url+'images/admin/ajax-loader-big.gif"/></div>',  
        css: {border:'0px','background-color':'transparent',position:'absolute'},
        overlayCSS: {opacity:0.04,cursor:'pointer'}
      });
 }

function unblockObject(obj)
{
    obj.unblock();
}


function blockElement(elementSelector)
{
	$(elementSelector).block(
	{
		message: '<div><img src="'+base_url+'images/admin/ajax-loader-big.gif"/></div>',  
		css: {border:'0px','background-color':'transparent',position:'absolute'},
		overlayCSS: {opacity:0.04,cursor:'pointer',position:'absolute'}
	});
 }

function unblockElement(elementSelector)
{
    $(elementSelector).unblock();
}

function handleJSON_response(data)
{

    if (data.js)
    {
        alert(data.js);
        eval(data.js);
    }

}

/***
* Checks to see if the pass parameter is a js function or not. 
* 
* @param functionToCheck
*/
function isFunction(functionToCheck) 
{
    var getType = {};
    return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
}
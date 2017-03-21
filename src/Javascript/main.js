function processFormSubmit()
{
	console.log(_Query);
		
	if (_Query.status=='success')
	{
		window.location = '{destinationURL}';	
	}
	
}

function isValidEmailAddress(emailAddress) {
    var pattern = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return pattern.test(emailAddress);
};


$(document).ready(function()
{
	$('#submit_button').unbind('click').click(function()
	{
		var _data = {};
		_data['action']     = 'saveFormData';
		_data['first_name'] = $('#first_name').val();
		_data['surname']    = $('#surname').val();
		_data['email']      = $('#email').val();	
			
		var errors = false;
		var errorString = '';
		var focusField = '';
		
		if (_data['first_name'].length==0)
		{
			errorString += 'Please enter your First Name!\n';
			if (focusField!='') 
			{ 
				focusField = 'first_name'; 
			}
			errors = true;
		}
	

		if (_data['surname'].length==0)
		{
			errorString += 'Please enter your Surname!\n';
			if (focusField!='') 
			{ 
				focusField = 'surname'; 
			}	
			errors = true;
		}
		
		if (_data['email'].length==0)
		{
			errorString += 'Please enter your Email!\n';
			if (focusField!='') 
			{ 
				focusField = 'email'; 
			}	
			errors = true;
		}
		else if (!isValidEmailAddress(_data['email']))
		{
			errorString += 'Please enter a valid Email!\n';
			if (focusField!='') 
			{ 
				focusField = 'email'; 
			}	
			errors = true;
		}
		
				
		if (errors==true)
		{
			
			alert('You have the following errors: \n\n'+errorString);
			$('#'+focusField).focus();
		}
		else
		{
		
			process_ajax('ajaxhandler.php', _data, function () { processFormSubmit(); }, true, '../Examples/'); 	
		}
	});
	
});


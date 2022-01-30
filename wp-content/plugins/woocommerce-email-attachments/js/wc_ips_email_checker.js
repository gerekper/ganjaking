/* 
 * Checks EMail addresses and allows the following:
 * 
 *		myname@domain.ext
 *		My Name  <myname@domain.ext>
 *		
 *	Needs:
 *		- email="check" as attribute in input fields for single email address,
 *		- email="check_multi" for multiple email addresses seperated by ","
 *		- emailname="yes" if 'My Name' is allowed
 *	
 *	Needs 'ip_error_email' defined in CSS to show amy Errors (is added as class to input field).
 *	Attaches to submit handler of the form.
 *	
 */


	
	function wc_ips_email_test(test_email, email_only)
	{
		var regex_name_eliminate = /[@>\.]/g;
		var regex_emailtest = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		var name = '';
		var address = '';
		
		var retval = {
				error: true,
				email: '',
				message: ''
				};
		
		if(typeof test_email !== 'string')
		{
			retval.message = wc_ips_email_checker_tx.nostring;
			return retval;
		}
		
		//	trim string
		test_email = test_email.replace (/^\s+/, '').replace (/\s+$/, '');
		retval.email = test_email;
		
		if(test_email.length === 0)
		{
			retval.message = wc_ips_email_checker_tx.emptystring;
			return retval;
		}
		
		//	email consists of  name <email.addresse> 
		if(test_email.indexOf('<', 0) >= 0)
		{
			var splitted = test_email.split('<');
			if(splitted.length > 2)
			{
				retval.message = wc_ips_email_checker_tx.error1;
				return retval;
			}
			if(splitted.length === 1)
			{
				address = splitted[0];
			}
			else
			{
				name = splitted[0].replace (/^\s+/, '').replace (/\s+$/, '');
				name = name.replace (regex_name_eliminate, '');
				address = splitted[1];
			}
			var ind = address.indexOf('>', 0);
			if(ind < 0)
			{
				retval.message = wc_ips_email_checker_tx.error2;
				return retval;
			}
			address = address.substr(0, ind).replace (/^\s+/, '').replace (/\s+$/, '');
		}
		else
		{
			address = test_email;
		}
		
		//	check EMail
		retval.error = !regex_emailtest.test(address);
		if(retval.error)
		{
			retval.message = wc_ips_email_checker_tx.invalid;
		}
		
		//	check, if name part not allowed in adress
		if((name.length > 0) && email_only)
		{
			retval.error = true;
			if(retval.message.length > 0)
			{
				retval.message += '\r\n';
			}
			retval.message += wc_ips_email_checker_tx.emailonly;
		}
		
		//	rebuild EMailaddress
		if(name.length > 0)
		{
			retval.email = name + ' <' + address + '>';
		}
		else
		{
			retval.email = address;
		}
		return retval;
	}
	
	function wc_ips_email_checker()
	{
		var check_ok = '';
		
		jQuery('input[email^="check"]').each(function(){
			var address = jQuery(this).val();
			var attr = jQuery(this).attr('email');
			var email_only = jQuery(this).attr('emailname');
			if((typeof email_only === 'string') && (email_only.toLowerCase() === 'yes'))
			{
				email_only = false;
			}
			else
			{
				email_only = true;
			}
			
			var address_split = address.split(',');
			var sum_check = '';
			
			for(i = 0; i < address_split.length; i++)
			{
				var check = wc_ips_email_test(address_split[i], email_only);
				if(typeof sum_check === 'string')
				{
					sum_check = check;
				}
				else
				{
					sum_check.error = sum_check.error || check.error;
					if(sum_check.message.length > 0)
					{
						sum_check.message += '\r\n';
					}
					sum_check.message += check.message;
					if(sum_check.email.length > 0)
					{
						sum_check.email += ', ';
					}
					sum_check.email += check.email;
				}	
			}
		
			if((attr.toLowerCase() !== 'check_multi') && (address_split.length > 1))
			{
				sum_check.error = true;
				if(sum_check.message.length > 0)
				{
					sum_check.message += '\r\n';
				}
				sum_check.message += wc_ips_email_checker_tx.oneaddressonly;
			}
			 
			jQuery(this).val(sum_check.email);
			
			if(sum_check.error && (sum_check.email !== ''))
			{
				jQuery(this).addClass("email_att_error_email");
				check_ok += sum_check.message + '\r\n';
			}
			else
			{
				jQuery(this).removeClass("email_att_error_email");
			}
			return this;
		});
		
		return check_ok;
	}
jQuery(document).ready(function() {
	jQuery("form").submit(function() 
	{
		var error = wc_ips_email_checker();
		if(error.length === 0)
			return true;
		var msg = wc_ips_email_checker_tx.errorsfound + '\r\n\r\n' + error + '\r\n\r\n' + wc_ips_email_checker_tx.marked;
		alert (msg);
		return false;
	});
});
	
function alert_test()
{
	alert ('alert_test');
}
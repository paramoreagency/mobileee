#MobileEE

An ExpressionEngine extension that provides global variables for detecting mobile, phone, iPhone, tablet, iPad. The module allows you to create links that will set those global variables.

##Global Variables

	{is_mobile}		- (boolean)		- Is the user on a mobile device.
	{is_phone}		- (boolean)		- Is the user on a phone.
	{is_iphone}		- (boolean)		- Is the user on a iPhone.
	{is_tablet}		- (boolean)		- Is the user on a tablet.
	{is_ipad}		- (boolean)		- Is the user on a iPad.

##Methods

###mobile_url

Sets `{is_mobile}` to `true` and redirects to the given URL.

__Parameters__

	{url}			- (string)		- The URL to redirect to.*

###desktop_url

Sets `{is_mobile}` to `false` and redirects to the given URL.

__Parameters__

	{url}			- (string)		- The URL to redirect to.*

###set_variable_url

Sets MobileEE global variable to the provided value and redirects.

__Parameters__

	{url}			- (string)		- The URL to redirect to.*
	{variable}		- (string)		- MobileEE global variable to set.
	{value}			- (bool)		- true or false

###set_variable_url

Unsets MobileEE global variable and redirects to provided URL.

__Parameters__

	{url}			- (string)		- The URL to redirect to.*
	{variable}		- (string)		- MobileEE global variable to unset.

_*If no URL is provided, it redirects to the current page._

##Examples

	<ul>
		<li>
			is_mobile: {if is_mobile}yes{if:else}no{/if}
		</li>

		<li>
			is_phone: {if is_phone}yes{if:else}no{/if}
		</li>

		<li>
			is_iphone: {if is_iphone}yes{if:else}no{/if}
		</li>

		<li>
			is_tablet: {if is_tablet}yes{if:else}no{/if}
		</li>

		<li>
			is_ipad: {if is_ipad}yes{if:else}no{/if}
		</li>
	</ul>

	<ul>
		<li>
			<a 
				href="{exp:mobileee:mobile_url
					url='/mobile'
				}">
					Mobile
			</a>
		</li>

		<li>
			<a
				href="{exp:mobileee:desktop_url
					url='/desktop'
				}">
					Desktop
			</a>
		</li>

		<li>
			<a
				href="{exp:mobileee:set_variable_url
					url='/phone-true'
					variable='is_phone'
					value='true'
				}">
					Set Phone
			</a>
		</li>

		<li>
			<a
				href="{exp:mobileee:unset_variable_url
					url='/phone-false'
					variable='is_phone'
				}">
					Unset Phone
			</a>
		</li>
	</ul>
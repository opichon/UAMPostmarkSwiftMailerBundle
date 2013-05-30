uam-postmark-swiftmailer-bundle
===================

A Symfony2 bundle that provides a SwiftMailer Transport implementation based on Postmark's API.

While a Symfony app by no means requires to use SwiftMailer for sending emails, there are many benefits to using it: log of emails sent, ability to redirect to a single delivery address during development, etc.

This bundle allows you to use the Postmark API as a SwiftMailer transport in your Symfony app. This provides you with the benefits of using the SwiftMailer component in hour app and of the Postmark API.

This bundle is in its early stages of development, and can hardly be viewed as production-ready. Usage in production is at your peril.

Installation
------------
Add package to your project's `composer.json`:

```
"require": {
	"uam/postmark-swiftmailer-bundle": "dev-master",
	…
}
```

Register this bundle as well as the  MZPostmarkBundle in `AppKernel.php`:

``` 
public function registerBundles()
{
	bundles = (
		// …
		new MZ\PostmarkBUndle\MZPostmarkBundle(),
		new UAM\Bundle\PostmarkSwiftMailerBundle\UAMPostmarkSwiftMailerBundle(),
	);
	
	return bundles();
}

```

Configuration
-------------
Configure the MZPostmarkBundle as per that bundle's documentation:

```
# app/config/config.php

mz_postmark:
    api_key:    %postmark_api_key%
    from_email: %postmark_from_email%
    from_name:  %postmark_from_name%
    use_ssl:    %postmark_use_ssl%
    timeout:    %postmark_timeout%
```

Update your SwiftMailer configuration to use the `uam_postmark` SwiftMailer transport provided by this bundle.

```
# app/config.php
swiftmailer:
	transport: uam_postmark
```

Usage
-----

This bundle creates a service aliased `uam_postmark` which implements a SwiftMailer transport based on the [Postmark](https://postmarkapp.com/) API.

Create your SwiftMailer messages as usual. When sent, the messages will be routed through the `uam_postmark` transport to the [Postmark](https://postmarkapp.com/) servers.

SwitfMailer plugins
-------------------

The UAMPostmarkTransport should in theory be able to support all swiftmailer plugins. However, so far only the Redirecting plugin has been tested to some extent.

### Redirecting

Edit your swiftmailer configuration as per the symfony SwiftMailerBundle documentation:

```
# app/config.php
swiftmailer:
	delivery_address: test@example.com
```

Known issues
------------

### HTML message content shows up as raw text

Make sure that the HTML body in your emails are set as a MIME part:

```
$message
    ->addPart($htmlBody, 'text/html');
```

### Email count

The `Swift_Transport#send()` method returns the count of messages sent. 

This bundle's implementation will return the number of emails sent to recipients included in the 'To' header. Emails sent to 'Cc' and 'Bcc' recipients will not be included in the email count returned.

The reason for this is that the postmark API, while supporting Cc and Bcc recipients, does not seem to include any data about them in its response to a request to send a message. 

 
### Failed recipients

The `Swift_Transport#send()` method's second parameter is designed to be passed a variable which collects the email addresses of failed recipients.

This is not supported by this bundle's implementation of the Swift_Transport interface. By design, this implementation will make a single call to the Postmark API for all the recipients (To, Cc, and Bcc included included in a single call, as opposed to a call per recipient). This single call will fail if one of the email addresses is invalid. 

### Redirecting custom headers are lost

SwiftMailer's Redirecting plugin adds custom headers to your message to reflect the origin recipients ('X-Swift-To', 'X-Swift-Cc', 'X-Swift-Bcc'). These headers are not recognized by the Postmark API and are not retained in the actual message sent via Postmark.
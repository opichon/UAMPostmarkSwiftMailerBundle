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
		new UAM\Bundle\PostmarkBundle\UAMPostmarkBundle(),
	);
	
	return bundles();
}

```

Configuration
-------------
Configure the MZPostmarkBundle as per that bundle's documentation:

```
# app/config.php

mz_postmark:
    api_key:    %postmark_api_key%
    from_email: %postmark_from_email%
    from_name:  %postmark_from_name%
    use_ssl:    %postmark_use_ssl%
    timeout:    %postmark_timeout%
```

Update your SwiftMailer configuration:

```
# app/config.php
swiftmailer:
	transport: uam_postmark
```

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

### Redirecting custom headers are lost

SwiftMailer's Redirecting plugin adds custom headers to your message to reflect the origin recipients ('X-Swift-To', 'X-Swift-Cc', 'X-Swift-Bcc'). These headers are not recognized by the Postmark API and are not retained in the actual message sent via Postmark.
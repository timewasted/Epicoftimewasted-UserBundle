About EpicoftimewastedUserBundle
===================

This is based on the [FriendsOfSymfony UserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle/).  `FOSUserBundle` is a wonderful framework for user management, with features that will appeal to a broad audience.

`EpicoftimewastedUserBundle` was created to satisfy my needs at the time.  This means that features that I had no use for were removed, and features that I needed were added.  The most noteworthy features are:

1. bcrypt and pbkdf2 password hashing
2. Login attempt throttling
3. Captcha support

Installation:
-------------

1. Download the bundle
2. Configure the autoloader
3. Enable the bundle
4. Configure the bundle

### Step 1: Download the bundle

How you actually download the bundle is entirely up to you.  However, once downloaded, you should place it in the `src/Epicoftimewasted/UserBundle` directory.

### Step 2: Configure the autoloader

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
        // ...
        'Epicoftimewasted' => __DIR__ . '/../src',
));
```

### Step 3: Enable the bundle

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
        $bundles = array(
                // ...
                new Epicoftimewasted\UserBundle\EpicoftimewastedUserBundle(),
        );
}
```

### Step 4: Configure the bundle

``` yaml
epicoftimewasted_user:
    db_driver:                  ~ # Required, currently only 'orm' is supported
    user_class:                 ~ # Required
    firewall_name:              ~ # Required
    model_manager_name:         null
    use_listener:               true
    from_email:
        address:                webmaster@example.com
        sender_name:            webmaster
    captcha:
        enabled:                false
        public_key:             null # Required if captcha is enabled
        private_key:            null # Required if captcha is enabled
    encoder:
        algorithm:              bcrypt # Can be 'bcrypt', or one of hash_algos() for use in pbkdf2
        work_factor:            10
    registration:
        confirmation:
            enabled:            false
            template:           EpicoftimewastedUserBundle:Registration:email.txt.twig
            from_email:
                address:        ~
                sender_name:    ~
        form:
            type:               epicoftimewasted_user_registration
            handler:            epicoftimewasted_user.registration.form.handler.default
            name:               epicoftimewasted_user_registration_form
        routes:
            confirmed:          epicoftimewasted_user_registration_account_confirmed
    resetting:
        token_ttl:              86400
        email:
            template:           EpicoftimewastedUserBundle:Resetting:email.txt.twig
            from_email:
                address:        ~
                sender_name:    ~
        form:
            type:               epicoftimewasted_user_resetting
            handler:            epicoftimewasted_user.resetting.form.handler.default
            name:               epicoftimewasted_user_resetting_form
        routes:
            reset_success:      epicoftimewasted_user_resetting_reset_success
    service:
        mailer:                 epicoftimewasted_user.mailer.default
        email_canonicalizer:    epicoftimewasted_user.util.email_canonicalizer.default
        username_canonicalizer: epicoftimewasted_user.util.username_canonicalizer.default
        user_manager:           epicoftimewasted_user.user_manager.default
    security:
        login_throttling:
            enabled:            false
            threshold:          3
```

License:
--------

```
Copyright (c) 2010-2011 FriendsOfSymfony
Copyright (c) 2011-2012 Ryan Rogers where noted

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is furnished
to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
```

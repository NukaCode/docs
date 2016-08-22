# How to start

## GitHub Token
First you will need to get your github access token from [your settings page](https://github.com/settings/tokens).  Enable 
everything (or at least the reads) and you should be fine.

## Generate a secret key
You can do this however you want, but this key should be kept secret as it will be used for the github webhooks.

## Update the .env
You will find a few new keys in the .env.example file.  You need to set 3 things:

1. Your GITHUB_TOKEN
2. Your GITHUB_SECRET
3. The namespace of your packages.  (eg: For Laravel\Scout the PACKAGE_NAMESPACE would be "laravel")

## Normal Steps
This would be things like run `composer install`, `npm install`, `php artisan migrate`, etc.

## Add a repository
Now that we know everything that's needed, you can add your site.  Run `php artisan docs:add-repo <name> <icon>`.  The 
name would be the actual package itself.  In the previous example using Laravel\Scout, the name would be scout.  The icon 
would be a font-awesome icon that will represent your package on the home page.  (Only the part after the fa- is needed).

`php artisan docs:add-repo scout binoculars`

This command will populate the `repositories` and `versions` tables with the details of this repository.

## Get the docs
Next, run `php artisan docs:get-docs <name>` to get the documentation from the package.  You should make sure to have a 
`docs` directory in the root of your package.

`php artisan docs:get-docs scout`

This will create a folder in `resources/docs` called `scout` and then a directory per version with unique docs.  By this 
we mean that if version have the same sha for the docs dir, they all use the first instance of them.  For example if releases 
2.0 and 2.1 have the same sha for the docs dir, but 2.2 has a different sha, it would create `resources/docs/scout/2.0` and 
`resources/docs/scout/2.2`.

Next it goes through the files of each directory to populate the `chapters` and `sections` tables.  These are what are used 
to display the content on the site since only versions that have chapters are selectable.

## Set up your webhooks
For any package that you want to be updated, you will need to add a webhook.  You can do this by going to your repository in 
GitHub and clicking Settings, then "Webhooks & Services".  Click "Add webhook".  here you will set the "Payload URL" to whatever 
URL this docs site is on then `/hook`.  So if you installed this repository on `http://docs.scout.com` the Payload URL would 
be `http://docs.scout.com/hook`.  The content type should be "application/json" and the secret should be the same as what 
you put in your .env for GITHUB_SECRET.

The events you need are only `Push` and `Release`.  These are both side-by-side towards the bottom of the checkboxes.  Lastly 
click "Add webhook" and you are done.

Anytime a webhook is received it will check a few things.  First it checks that the signature matches your secret key.  This 
ensures no one can do bad things with that route.  Next it checks to make sure the event type is one that we understand.  Currently 
this is set to "push" and "release".  Then it will run the `php artisan docs:add-repo` and `php artisan docs:get-docs` commands 
for the repository that triggered the event.  If that repository hasn't been added to the system yet, it will not run these.  
Lastly it wraps up everything that was done and sends it to the logs as one lump log.  An example of the logs is included below.

```
[2016-08-22 06:29:50] local.INFO: Event Log:
GitHub event received
GitHub event signature matched
Repository set to database
Running add-repo
Running get-docs
GitHub event processed.  
```

If any errors were encountered it will display them and the log level would be error instead of info.

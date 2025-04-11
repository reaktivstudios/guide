# The Guide by Reaktiv

<img width="823" alt="Screenshot 2024-06-10 at 3 48 13 PM" src="https://github.com/reaktivstudios/reaktivist/assets/90352979/ba90672a-679f-494c-8b5b-1fe8906d8c58">

This plugin will pull in docs that have been flagged with "Site Guide" so that the content is viewable on the WordPress site. This will help make it easier to reference documentation.

There are a few steps to get started, so please follow the usage steps below.

## Usage

### Create Notion API Integration
Add a new API integration in Notion. If you have multiple notion projects, they can share a single integration. Save the API key, it will be used in a later step.

### Share the database
Before a database can be accessed by the site guide, it is necessary to share it with the API to provide access. 


### Define constants
The main setup is that constants must be defined. The two constants that are required are the
- RKV_SITE_GUIDE_API_KEY: The API key that allows access to notion docs. 
- RKV_SITE_GUIDE_DATABASE_ID: This is the database ID for the database containing the site guide. To find a database ID, navigate to the database URL in your Notion workspace. The ID is the string of characters in the URL that is between the slash following the workspace name (if applicable) and the question mark. The ID is a 32 characters alphanumeric string.

Define these as PHP constants in the appropriate config file. 

**Note**: For the `RKV_SITE_GUIDE_API_KEY`, this is a SECRET key so please use environment variables instead of directly committing the key into version control.

For WordPress VIP this might look like:
~~~
define( 'RKV_SITE_GUIDE_API_KEY', vip_get_env_var( 'RKV_SITE_GUIDE_API_KEY', '' ) );
~~~

For more details:
- [WordPress VIP Env Variables](https://docs.wpvip.com/infrastructure/environments/manage-environment-variables/)
- [Pantheon Secrets Management](https://docs.pantheon.io/guides/wordpress-developer/wordpress-secrets-management)
- [WPE Environment Variables](https://developers.wpengine.com/docs/atlas/platform-guides/environment-variables/)

### Manual sync
The site guides should automatically update once a day, but it is possible to manually sync to push an important update.

1. Log into the WordPress dashboard.
2. Append this to the wp-admin URL `?rkv-guide-sync=1` like `example.com/wp-admin/?rkv-guide-sync=1`.


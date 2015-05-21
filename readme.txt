=== Host your blog on S3 or Google Cloud Storage ===
Contributors: sircelj.m
Tags: cache, static, s3, amazon, aws, cloud, google, gcs
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.6
Tested up to: 4.1
Stable tag: 0.0.2

Transfer your blog to static html pages and upload it to Amazon S3 or Google Cloud Storage.

== Description ==

This plugin captures all pages of your blog and uploads them to Amazon S3. This way you can host your blog as a static page for very little cost.

Before you run your blog this way, make sure that you are not using any functionality that isn't static or replace it with a service. For example:
 - disable comments or use Disqus plugin instead of standard commenting system
 - disable search
 - use Google Forms for contact form
 - etc.
 
To publish new content, you will have to run your server with WordPress, write new post and then run the plugin again to create new files. For that purpose it is best to run WordPress on your local machine or to make an EC2 instance in Amazon cloud and run it only when you are updating your blog.
 
== Installation ==

!!! For more detailed instructions see http://www.s-media.si/hosting-wp-blog-on-s3-or-gcs/ !!!


1.  Sign up to AWS
2.  Launch a new EC2 instance and install WordPress - you can launch already configured WordPress Bitnami AMI
3.  Create an S3 bucket
4.  Migrate your data and configure your blog on EC2 instance
5.  Install this plugin through the WordPress admin panel

6. UPLOAD TO AWS S3
6.1. Obtain AWS credentials
6.2. Setup and run this plugin
6.3. Make sure new files are in your S3 bucket
6.4. Enable website hosting on your S3 bucket and configure "Index Document" and "Error Document"
6.5. Point your domain to S3 bucket in AWS Route 53
6.6. You can now stop your EC2 instance.

7. UPLOAD TO GOOGLE CLOUD STORAGE
7.1. Obtain GCS credentials
7.2. Setup and run this plugin
7.3. Make sure new files are in your GCS bucket
7.4. Enable website hosting on your GCS bucket and configure "Main page" and "404 (not found) page"
7.5. Point your domain to GCS bucket in your GCS
7.6. You can now stop your EC2 instance.


!!! For more detailed instructions see http://www.s-media.si/hosting-wp-blog-on-s3-or-gcs/ !!!

== Frequently Asked Questions ==


== Changelog ==

= 0.0.2 =
Updated instructions

= 0.0.1 =
Initial release

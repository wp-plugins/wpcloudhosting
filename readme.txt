=== Host your blog on S3 ===
Contributors: sircelj.m
Tags: cache, static, s3, amazon, aws, cloud
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.6
Tested up to: 4.1
Stable tag: 0.0.1

Transfer your blog to static html pages and upload it to Amazon S3.

== Description ==

This plugin captures all pages of your blog and uploads them to Amazon S3. This way you can host your blog as a static page for very little cost.

Before you run your blog this way, make sure that you are not using any functionality that isn't static or replace it with a service. For example:
 - disable comments or use Disqus plugin instead of standard commenting system
 - disable search
 - use Google Forms for contact form
 - etc.
 
To publish new content, you will have to run your server with WordPress, write new post and then run the plugin again to create new files. For that purpose it is best to run WordPress on your local machine or to make an EC2 instance in Amazon cloud and run it only when you are updating your blog.
 
== Installation ==

1.  Sign up to AWS
2.  Launch a new EC2 instance and install WordPress - you can launch already configured WordPress Bitnami AMI
3.  Create an S3 bucket
4.  Migrate your data and configure your blog on EC2 instance
5.  Install this plugin through the WordPress admin panel
6.  Obtain AWS credentials
7.  Setup and run this plugin
8.  Make sure new files are in your S3 bucket
9.  Enable website hosting on your S3 bucket and configure "Index Document" and "Error Document"
10. Point your domain to S3 bucket in AWS Route 53 
11. You can now stop your EC2 instance.


== Frequently Asked Questions ==


== Changelog ==

= 0.0.1 =
Initial release
